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

        $fname = NULL;
        $siteUrl = NULL;
        $postData = $controller->getRequest()->getPost();
        if ($_FILES['image']['name'] != '') {
            $fname = $_FILES['image']['name'];
        }
        if ($postData['url'] != NULL) {
            $siteUrl = $postData['url'];
        }
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $path = Mage::getBaseDir('media') . '/tim/' . $customerId;
        $model = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $modelData = $model->getData();
        Mage::log($fname);

        if ($postData['tim-form-delete'] != NULL) {
            try {
                $model->setPhoto('');
                $model->save();
                $this->clearPath($path);
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        if ($fname != NULL) {
            try {
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
                $this->clearPath($path);
                $dbPath = '/media/tim/' . $customerId . '/' . $fname;
                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('png', 'gif', 'jpeg', 'jpg'));
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $uploader->save($path, $fname);
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

    public function clearPath($path)
    {
        if (is_dir($path)) {
            foreach (glob($path . '/*') as $file) {
                unlink($file);
            }
        }
    }
}