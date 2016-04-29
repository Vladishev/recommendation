<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_IndexController. Default controller for Recommendation module
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_IndexController extends Mage_Core_Controller_Front_Action
{
    /**
     * Add customer opinion
     */
    public function addAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $response = array();
            if (isset($_FILES['tim-recommendation-img'])) {
                $deletedImagesObj = json_decode($params['deleted_imgs']);
                $deletedImages = array();
                foreach ($deletedImagesObj as $item) {
                    $deletedImages[] = get_object_vars($item);
                }
                $allFiles = $this->reArrangeFiles($_FILES['tim-recommendation-img']);
                $files = $this->filesForSave($allFiles, $deletedImages);
            }
            $folderForFiles = Mage::getBaseDir('media') . DS . 'tim' . DS . 'recommendation';
            $averageRating = $this->_getAverageRating($params);

            if (!is_dir($folderForFiles)) {
                mkdir($folderForFiles, 0777, true);
            }
            $userAccess = Mage::helper('tim_recommendation')->getUserLevelAccess($params['customer_id']);

            $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
                ->setDateAdd(date('Y-m-d H:i:s'))
                ->setUserId($params['customer_id'])
                ->setProductId($params['product_id'])
                ->setAdvantages($params['opinion-advantages'])
                ->setDefects($params['opinion-disadvantages'])
                ->setConclusion($params['opinion-summary'])
                ->setRatingPrice($params['itemValuetomoney'])
                ->setRatingDurability($params['itemDurability'])
                ->setRatingFailure($params['itemFailure'])
                ->setRatingService($params['itemEaseofinstall'])
                ->setAverageRating($averageRating)
                ->setRecommend($params['itemDoyourecommend'])
                ->setTimIp($params['customer_ip_address'])
                ->setTimHost($params['customer_host_name'])
                ->setManufacturerId($params['manufacturer_id'])
                ->setCategoryId($params['current_category_id'])
                ->setAddMethod($params['add_method']);
            //sets acceptance to opinion in case if admin give this opportunity for customer
            if($userAccess['moderation']){
                $recommendationModel->setAcceptance($userAccess['moderation'])
                    ->setPublicationDate(date('Y-m-d H:i:s'));
            }
            try {
                $recommendationModel->save();
                $recomId = $recommendationModel->getRecomId();
                $response['message'] = Mage::helper('tim_recommendation')->__('Thank you for adding opinion. Your opinion has been submitted for review by the administrator.');
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                $response['message'] = Mage::helper('tim_recommendation')->__('Can\'t add opinion. Please try again.');
            }

            if (!empty($params['link_to_youtube'])) {
                Mage::getModel('tim_recommendation/media')
                    ->setRecomId($recomId)
                    ->setName($params['link_to_youtube'])
                    ->setType('url/youtube')
                    ->save();
            }
            if (isset($files)) {
                if (!empty($files)) {
                    foreach ((array)$files as $file) {
                        if ($file['error'] == 0) {
                            $file['name'] = str_replace(' ', '_', $file['name']);
                            $fileName = time() . $file['name'];
                            $mediaModel = Mage::getModel('tim_recommendation/media')
                                ->setRecomId($recomId)
                                ->setName('/media/tim/recommendation/' . $fileName)
                                ->setType($file['type']);
                            try {
                                $saveMedia = $mediaModel->save();
                            } catch (Exception $e) {
                                Mage::log($e->getMessage(), NULL, 'tim_recommendation.log');
                                $response['message'] = Mage::helper('tim_recommendation')->__('Didn\'t save %s file.', $file['name']);
                            }
                            if (isset($saveMedia)) {
                                $this->saveImage($fileName, $folderForFiles, $file);
                            }
                        }
                    }
                }
            }
            if (!empty($recomId) || $userAccess['moderation']) {
                $this->saveMd5($recomId);
                $eventData = $this->_getDataForConfirmEmail($recomId, $recommendationModel, 'opinion');
                $event = array('opinion_data' => $eventData);
                Mage::dispatchEvent('controller_index_add_opinion_data', $event);
            }

            echo json_encode($response);
        } else {
            $this->_redirectReferer();
            return;
        }
    }

    /**
     * Returns average rating for product
     *
     * @param array $params Array with rating data
     * @return float
     */
    protected function _getAverageRating($params)
    {
        $rating = array();
        $rating[] = $params['itemValuetomoney'];
        $rating[] = $params['itemDurability'];
        $rating[] = $params['itemFailure'];
        $rating[] = $params['itemEaseofinstall'];
        $average = round(array_sum($rating) / count($rating), 1);
        return $average;
    }

    /**
     * Saves hash to table
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     */
    public function saveMd5($recomId)
    {
        $recommendation = Mage::getModel('tim_recommendation/recommendation')->load($recomId);
        $salt = Mage::helper('tim_recommendation')->getSalt();
        $md5hash = md5($recommendation->getUserId() . $recommendation->getDateAdd() . $recommendation->getAdvantages() . $recommendation->getComment() . $salt);
        $recommendation->setMd5($md5hash);
        try {
            $recommendation->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
        }
    }

    /**
     * Returns array whith custom data for event
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @param object $recommendationModel 'tim_recommendation/recommendation'
     * @param string $type Can be 'opinion' and 'comment' - types of confirm email
     * @return array
     */
    protected function _getDataForConfirmEmail($recomId, $recommendationModel, $type)
    {
        $recommendationData = $recommendationModel->load($recomId);
        $eventData['recom_id'] = $recomId;
        $eventData['date_add'] = $recommendationData->getDateAdd();
        $eventData['user_id'] = $recommendationData->getUserId();
        $userAccess = Mage::helper('tim_recommendation')->getUserLevelAccess($eventData['user_id']);
        $eventData['user_moderation'] = $userAccess['moderation'];

        if ($type === 'opinion') {
            $productId = $recommendationData->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId);
            $eventData['product_name'] = $product->getName();
            $eventData['product_url'] = $product->getProductUrl();
            $eventData['advantages'] = $recommendationData->getAdvantages();
            $eventData['defects'] = $recommendationData->getDefects();
            $eventData['conclusion'] = $recommendationData->getConclusion();
            $eventData['rating_price'] = $recommendationData->getRatingPrice();
            $eventData['rating_durability'] = $recommendationData->getRatingDurability();
            $eventData['rating_failure'] = $recommendationData->getRatingFailure();
            $eventData['rating_service'] = $recommendationData->getRatingService();
            $eventData['modify_opinion_url'] = Mage::helper('tim_recommendation')->getModifyOpinionUrl($recomId);

            if ($recommendationData->getByIt() == 1) {
                $eventData['by_it'] = Mage::helper('tim_recommendation')->__('TAK');
            } else {
                $eventData['by_it'] = Mage::helper('tim_recommendation')->__('NIE');
            }
            if ($recommendationData->getRecommend() == 1) {
                $eventData['recommend'] = Mage::helper('tim_recommendation')->__('TAK');
            } else {
                $eventData['recommend'] = Mage::helper('tim_recommendation')->__('NIE');
            }
            $mediaCollection = Mage::getModel('tim_recommendation/media')->getCollection();
            $mediaCollection->addFieldToFilter('recom_id', $recomId);
            $mediaData = $mediaCollection->getData();
            $i = 0;
            foreach ($mediaData as $item) {
                if ($item['type'] == 'url/youtube') {
                    $eventData['media_name'] = $item['name'];
                    continue;
                }
                $eventData['image_type' . $i] = $item['type'];
                $eventData['image_name' . $i] = $item['name'];
                $i++;
            }
        }
        if ($type === 'comment') {
            $eventData['comment'] = $recommendationModel->getComment();
            $parentId = $recommendationData->getParent();
            $commentData = $recommendationModel->load($parentId);
            $productId = $commentData->getProductId();
            $product = Mage::getModel('catalog/product')->load($productId);
            $eventData['product_name'] = $product->getName();
            $eventData['product_url'] = $product->getProductUrl();
            $eventData['modify_comment_url'] = Mage::helper('tim_recommendation')->getModifyCommentUrl($recomId);
        }

        $eventData['confirm_url'] = $this->getConfirmUrl($recomId, '0');

        return $eventData;
    }

    /**
     * Returns url with data
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @param string $status 0 - to make accept url
     * @return string
     */
    public function getConfirmUrl($recomId, $status)
    {
        $salt = Mage::helper('tim_recommendation')->getSalt();
        $md5 = Mage::helper('tim_recommendation')->getRecommendationMd5($recomId);
        $request = sha1($salt . $status . $md5);
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation/index/confirm/request/' . $request . '/id/' . $recomId;
        return $url;
    }

    /**
     * Displays confirm/moderate links or 404 if GET data are wrong
     */
    public function confirmAction()
    {
        $requestArray = $this->getRequest()->getParams();
        if (!empty($requestArray)) {
            $result = Mage::helper('tim_recommendation')->checkForNoRoute($requestArray);
            if ($result) {
                $this->norouteAction();
            } else {
                $this->loadLayout();
                $this->renderLayout();
            }
        }
    }

    /**
     * Send email to customer about modification from direct link
     */
    public function modifyOpinionAction()
    {
        $opinionId = $this->getRequest()->getParam('opinionId');
        if (!empty($opinionId)) {
            $opinion = Mage::getModel('tim_recommendation/recommendation')->load($opinionId, 'recom_id');
            $customer = Mage::getModel('customer/customer')->load($opinion->getUserId());
            $product = Mage::getModel('catalog/product')->load($opinion->getProductId());
            $templateVar = array();
            $templateVar['customerName'] = $customer->getName();
            $templateVar['productName'] = $product->getName();
            $templateVar['indexTim'] = $product->getSku();
            $mailResult = Mage::helper('tim_recommendation')->sendEmail($customer->getEmail(), $templateVar, 'modify_opinion_template', 'Opinia została zablokowana');

            if ($mailResult) {
                $this->loadLayout();
                $this->renderLayout();
            } else {
                $this->norouteAction();
            }
        } else {
            $this->norouteAction();
        }
    }

    /**
     * Send email to customer about modification from direct link
     */
    public function modifyCommentAction()
    {
        $commentId = $this->getRequest()->getParam('commentId');
        if (!empty($commentId)) {
            $opinion = Mage::getModel('tim_recommendation/recommendation')->load($commentId, 'recom_id');
            $customer = Mage::getModel('customer/customer')->load($opinion->getUserId());
            $product = Mage::getModel('catalog/product')->load($opinion->getProductId());
            $templateVar = array();
            $templateVar['customerName'] = $customer->getName();
            $templateVar['productName'] = $product->getName();
            $templateVar['indexTim'] = $product->getSku();
            $mailResult = Mage::helper('tim_recommendation')->sendEmail($customer->getEmail(), $templateVar, 'modify_comment_template', 'Komentarz został zablokowany');

            if ($mailResult) {
                $this->loadLayout();
                $this->renderLayout();
            } else {
                $this->norouteAction();
            }
        } else {
            $this->norouteAction();
        }
    }

    /**
     * Set acceptance = 1 in tim_recommendation table for current opinion/comment
     */
    public function allowAction()
    {
        $requestArray = $this->getRequest()->getParams();
        if (!empty($requestArray)) {
            $result = Mage::helper('tim_recommendation')->checkForNoRoute($requestArray);
            if ($result) {
                $this->norouteAction();
            } else {
                $opinion = Mage::getModel('tim_recommendation/recommendation')->load((int) $requestArray['id']);
                //add points for adding comment or opinion by customer
                Mage::helper('tim_recommendation')->savePointsForCustomer($opinion);
                $opinion->setAcceptance('1')
                    ->setPublicationDate(date('Y-m-d H:i:s'));
                try {
                    $opinion->save();
                    Mage::dispatchEvent('controller_index_allow_opinion_data', array('opinion_id' => $requestArray['id']));
                    echo '<h2>' . Mage::helper('tim_recommendation')->__('The opinion/comment was successfully allowed!') . '</h2>';
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                    echo '<h2>' . Mage::helper('tim_recommendation')->__('The opinion/comment wasn\'t allowed. Please try again.') . '</h2>';
                }
            }
        }
    }

    /**
     * Adds comment to opinion
     */
    public function addCommentAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $userAccess = Mage::helper('tim_recommendation')->getUserLevelAccess($params['customer_id']);
            $response = array();
            $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
                ->setDateAdd(date('Y-m-d H:i:s'))
                ->setUserId($params['customer_id'])
                ->setParent($params['recom_id'])//recommendation ID
                ->setProductId($params['product_id'])
                ->setComment($params['opinion-comment'])
                ->setTimIp($params['customer_ip_address'])
                ->setTimHost($params['customer_host_name'])
                ->setAddMethod($params['add_method']);
            //sets acceptance to comment in case if admin give this opportunity for customer
            if($userAccess['moderation']){
                $recommendationModel->setAcceptance($userAccess['moderation'])
                    ->setPublicationDate(date('Y-m-d H:i:s'));
            }
            try {
                $recommendationModel->save();
                $recomId = $recommendationModel->getRecomId();
                $response['message'] = Mage::helper('tim_recommendation')->__('Thank you for adding comment. Your comment has been submitted for review by the administrator.');
                $response['commentRecomId'] = $params['recom_id'];
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                $response['message'] = Mage::helper('tim_recommendation')->__('Can\'t add comment. Please try again.');
            }
            if (!empty($recomId) || $userAccess['moderation']) {
                $this->saveMd5($recomId);
                $eventData = $this->_getDataForConfirmEmail($recomId, $recommendationModel, 'comment');
                $event = array('comment_data' => $eventData);
                Mage::dispatchEvent('controller_index_add_comment_data', $event);
            }

            echo json_encode($response);
        } else {
            $this->_redirectReferer();
            return;
        }
    }

    /**
     * Load and render layout
     */
    public function landingAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Load and render layout
     */
    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Save image to folder
     *
     * @param string $fileName File name
     * @param string $path Path to save
     * @param array $file Array with file information
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
     *
     * @param array $fileArray $_FILES array
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

    /**
     * Remove files from array that was delete on frontend
     * @param array $filesArray
     * @param array $deletedImagesArray
     * @return array
     */
    public function filesForSave($filesArray, $deletedImagesArray)
    {
        foreach ($deletedImagesArray as $image) {
            if ($element = $filesArray[$image['id']]) {
                if ($element['name'] == $image['name'] && $element['size'] == $image['size']) {
                    unset($filesArray[$image['id']]);
                }
            }
        }
        return $filesArray;
    }

    /**
     * Saves user data to tim_recom_malpractice table
     */
    public function saveMalpracticeAction()
    {
        if ($this->getRequest()->isPost()) {
            $params = $this->getRequest()->getParams();
            $model = Mage::getModel('tim_recommendation/malpractice');
            $model->setDateAdd(date('Y-m-d H:i:s'));
            $model->setRecomId($params['recom_id']);
            $model->setUserId($params['userId']);
            $model->setComment($params['comment']);
            $model->setTimIp($params['customerIp']);
            $model->setTimHost($params['customerHostName']);
            if (!empty($params['email'])) {
                $model->setEmail($params['email']);
            }
            try {
                $model->save();
                $eventData = $params;
                $event = array('malpractice_data' => $eventData);
                Mage::dispatchEvent('controller_index_add_malpractice_data', $event);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            }
        } else {
            $this->_redirectReferer();
            return;
        }
    }
}