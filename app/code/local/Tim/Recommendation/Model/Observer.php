<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Model_Observer
{
    public function saveAction($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();

        $avatar = NULL;
        $banner = NULL;
        $siteUrl = NULL;
        $postData = $controller->getRequest()->getPost();
        if ($_FILES['image']['name'] != '') {
            $avatar = time() . $_FILES['image']['name'];
        }
        if ($_FILES['banner']['name'] != '') {
            $banner = time() . $_FILES['banner']['name'];
        }
        if ($postData['url'] != NULL) {
            $siteUrl = $postData['url'];
        }
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $path = Mage::getBaseDir('media') . '/tim/recommendation';
        $model = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $modelData = $model->getData();

        if ($avatar != NULL) {
            try {
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $dbPath = '/media/tim/recommendation/' . $avatar;
                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('png', 'gif', 'jpeg', 'jpg'));
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $uploader->save($path, $avatar);
                if (!empty($modelData)) {
                    $model->setPhoto($dbPath);
                    $model->save();
                } else {
                    $model->setCustomerId($customerId);
                    $model->setPhoto($dbPath);
                    $model->save();
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__($e->getMessage()));
            }
        }
        if ($banner != NULL) {
            try {
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $dbPath = '/media/tim/recommendation/' . $banner;
                $uploader = new Varien_File_Uploader('banner');
                $uploader->setAllowedExtensions(array('png', 'gif', 'jpeg', 'jpg'));
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $uploader->save($path, $banner);
                if (!empty($modelData)) {
                    $model->setBanner($dbPath);
                    $model->save();
                } else {
                    $model->setCustomerId($customerId);
                    $model->setBanner($dbPath);
                    $model->save();
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__($e->getMessage()));
            }
        }
        if ($siteUrl != NULL) {
            try {
                $modelData = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getData();
                if (!empty($modelData)) {
                    $model->setSiteUrl($siteUrl);
                    $model->save();
                } else {
                    $model->setCustomerId($customerId);
                    $model->setSiteUrl($siteUrl);
                    $model->save();
                }
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
    }
}