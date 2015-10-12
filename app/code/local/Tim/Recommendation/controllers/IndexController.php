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
            ->setTitle($params['opinion-title'])
            ->setAdvantages($params['opinion-advantages'])
            ->setDefects($params['opinion-disadvantages'])
            ->setConclusion($params['opinion-summary'])
            ->setRatingPrice($params['itemValuetomoney'])
            ->setRatingDurability($params['itemDurability'])
            ->setRatingFailure($params['itemFailure'])
            ->setRatingService($params['itemEaseofinstall'])
            ->setRecomend($params['itemDoyourecommend']);
        try {
            $recommendationModel->save();
            $recomId = $recommendationModel->getRecomId();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('tim_recommendation')->__('Opinion was successfully added.'));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Can\'t add opinion.'));
        }

        if(!empty($params['link_to_youtube'])){
            Mage::getModel('tim_recommendation/media')
                ->setRecomId($recomId)
                ->setName($params['link_to_youtube'])
                ->setType('url/youtube')
                ->save();
        }

        foreach ((array)$files as $file) {
            if($file['error'] == 0){
                $mediaModel = Mage::getModel('tim_recommendation/media')
                    ->setRecomId($recomId)
                    ->setName('/media/tim/recommendation/' . $file['name'])
                    ->setType($file['type']);
                try {
                    $saveMedia = $mediaModel->save();
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), NULL, 'tim_recommendation.log');
                    Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Didn\'t save %s file.', $file['name']));
                }
                if ($saveMedia) {
                    if (isset($file['name']) && !empty($file['name'])) {
                        $fileName = time() . $file['name'];
                        $this->saveImage($fileName, $folderForFiles, $file);
                    }
                }
            }
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