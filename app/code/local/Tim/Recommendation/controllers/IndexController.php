<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

    }

    public function addAction()
    {
        $params = $this->getRequest()->getParams();
        $files = $this->reArrangeFiles($_FILES['tim-recommendation-img']);
        $folderForFiles = Mage::getBaseDir('media') . DS . 'tim' . DS . 'recommendation';

        if (!is_dir($folderForFiles)) {
            mkdir($folderForFiles, 0777, true);
        }

        $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
            ->setUserId($params['customer_id'])
            ->setProductId($params['product_id'])
            ->setAdvantages($params['opinion-advantages'])
            ->setDefects($params['opinion-disadvantages'])
            ->setConclusion($params['opinion-summary'])
            ->setRatingPrice($params['itemValuetomoney'])
            ->setRatingDurability($params['itemDurability'])
            ->setRatingFailure($params['itemFailure'])
            ->setRatingService($params['itemEaseofinstall'])
            ->setRecommend($params['itemDoyourecommend'])
            ->setTimIp($params['customer_ip_address'])
            ->setTimHost($params['customer_host_name']);
        try {
            $recommendationModel->save();
            $recomId = $recommendationModel->getRecomId();
                Mage::getSingleton('core/session')->addSuccess(Mage::helper('tim_recommendation')->__('Opinion was successfully added.'));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Can\'t add opinion.'));
        }

        if (!empty($params['link_to_youtube'])) {
            Mage::getModel('tim_recommendation/media')
                ->setRecomId($recomId)
                ->setName($params['link_to_youtube'])
                ->setType('url/youtube')
                ->save();
        }

        foreach ((array)$files as $file) {
            if ($file['error'] == 0) {
                $fileName = time() . $file['name'];
                $mediaModel = Mage::getModel('tim_recommendation/media')
                    ->setRecomId($recomId)
                    ->setName('/media/tim/recommendation/' . $fileName)
                    ->setType($file['type']);
                try {
                    $saveMedia = $mediaModel->save();
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), NULL, 'tim_recommendation.log');
                    Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Didn\'t save %s file.', $file['name']));
                }
                if ($saveMedia) {
                    $this->saveImage($fileName, $folderForFiles, $file);
                }
            }
        }

        if(!empty($recomId))
        {
            $eventData = $this->_getDataForConfirmEmail($recomId, $recommendationModel, 'opinion');
            $event = array('opinion_data' => $eventData);
            Mage::dispatchEvent('controller_index_add_opinion_data', $event);
        }
        $this->_redirectReferer();
    }

    /**
     * Returns array whith custom data for event
     * 'opinion' and 'comment'($type) - types of confirm email
     * @param (int)$recomId
     * @param (obj)$recommendationModel
     * @param (str)$type
     * @return array
     */
    protected function _getDataForConfirmEmail($recomId, $recommendationModel, $type)
    {
        $recommendationData = $recommendationModel->load($recomId);
        $eventData['recom_id'] = $recomId;
        $eventData['date_add'] = $recommendationData->getDateAdd();
        $eventData['user_id'] = $recommendationData->getUserId();

        if ($type === 'opinion')
        {
            $productId = $recommendationData->getProductId();
            $productCollection = Mage::getModel('catalog/product')->load($productId);
            $eventData['product_name'] = $productCollection->getName();
            $eventData['product_url'] = $productCollection->getProductUrl();
            $eventData['advantages'] = $recommendationData->getAdvantages();
            $eventData['defects'] = $recommendationData->getDefects();
            $eventData['conclusion'] = $recommendationData->getConclusion();
            $eventData['rating_price'] = $recommendationData->getRatingPrice();
            $eventData['rating_durability'] = $recommendationData->getRatingDurability();
            $eventData['rating_failure'] = $recommendationData->getRatingFailure();
            $eventData['rating_service'] = $recommendationData->getRatingService();

            if($recommendationData->getByIt() == 1)
            {
                $eventData['by_it'] = 'TAK';
            }else{
                $eventData['by_it'] = 'NIE';
            }
            if($recommendationData->getRecommend() == 1)
            {
                $eventData['recommend'] = 'TAK';
            }else{
                $eventData['recommend'] = 'NIE';
            }
            $mediaCollection = Mage::getModel('tim_recommendation/media')->getCollection();
            $mediaCollection->addFieldToFilter('recom_id', $recomId);
            $mediaData = $mediaCollection->getData();
            foreach ($mediaData as $item)
            {
                if ($item['type'] == 'url/youtube')
                {
                    $eventData['media_name'] = $item['name'];
                }else{
                    $eventData['image_type'] = $item['type'];
                    $eventData['image_name'] = $item['name'];
                }
            }
        }
        if ($type === 'comment')
        {
            $eventData['comment'] = $recommendationModel->getComment();
            $parentId = $recommendationData->getParent();
            $commentData = $recommendationModel->load($parentId);
            $productId = $commentData->getProductId();
            $productCollection = Mage::getModel('catalog/product')->load($productId);
            $eventData['product_name'] = $productCollection->getName();
            $eventData['product_url'] = $productCollection->getProductUrl();

        }

        $eventData['confirm_url'] = $this->getConfirmUrl($recomId, '0');
        $eventData['moderate_url'] = $this->getConfirmUrl($recomId, '1');

        return $eventData;
    }

    /**
     * Returns url with data
     * $status = 0 - to make accept url
     * $status = 1 - to make moderate url
     * @param (int)$recomId
     * @param (str)$status
     * @return string
     */
    public function getConfirmUrl($recomId, $status)
    {
        $salt = 'test';
        $md5 = 'tim_recommendation.md5';
        $request = sha1($salt.$status.$md5);
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).'recommendation/index/confirm/request/'.$request.'/id/'.$recomId;
        return $url;
    }

    /**
     * Adds comment to opinion
     */
    public function addCommentAction()
    {
        $params = $this->getRequest()->getParams();
        $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
            ->setUserId($params['customer_id'])
            ->setParent($params['recom_id']) //recommendation ID
            ->setComment($params['opinion-comment'])
            ->setTimIp($params['customer_ip_address'])
            ->setTimHost($params['customer_host_name']);
        try {
            $recommendationModel->save();
            $recomId = $recommendationModel->getRecomId();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('tim_recommendation')->__('Comment was successfully added.'));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Can\'t add comment.'));
        }
        if(!empty($recomId))
        {
            $eventData = $this->_getDataForConfirmEmail($recomId, $recommendationModel, 'comment');
            $event = array('comment_data' => $eventData);
            Mage::dispatchEvent('controller_index_add_comment_data', $event);
        }

        $this->_redirectReferer();
    }

    public function landingAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save image to folder
     * @param string $fileName
     * @param string $path
     * @param array $file
     */
    public function saveImage($fileName, $path, $file)
    {
        $uploader = new Varien_File_Uploader($file);
        $uploader->setAllowedExtensions(array('png', 'gif', 'jpeg', 'jpg'));
        $uploader->setAllowCreateFolders(true);
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        try {
            $uploader->save($path, $fileName);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
        }
    }

    /**
     * Rearrange $_FILES array to clear array
     * @param array $fileArray
     * @return array $newFileArray
     */
    public function reArrangeFiles($fileArray)
    {
        foreach ($fileArray as $key => $all) {
            foreach ($all as $i => $val) {
                $newFileArray[$i][$key] = $val;
            }
        }
        return $newFileArray;
    }
}