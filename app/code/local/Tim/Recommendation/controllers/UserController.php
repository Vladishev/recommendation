<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vlad Verbitskiy <vladmsu@ukr.net>
 */
class Tim_Recommendation_UserController extends Mage_Core_Controller_Front_Action
{
    /*Checking user id and open user_profile page*/
    public function profileAction()
    {
        $userId = $this->getRequest()->getParam('id');
        $model = Mage::getModel('tim_recommendation/user');
        if ($model->load($userId)->getCustomerId() != NULL) {
            $userData = $model->load($userId);
            Mage::register('user_data', $userData);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->norouteAction();
            return;
        }
    }

    public function dataAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function saveAction()
    {
        $fname = NULL;
        $siteUrl = NULL;
        $postData = $this->getRequest()->getPost();
        if ($_FILES['image']['name'] != '')
        {
            $fname = $_FILES['image']['name'];
        }
        if ($postData['url'] != NULL)
        {
            $siteUrl = $postData['url'];
        }
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $path = Mage::getBaseDir('media') . '/tim/' . $customerId;
        $model = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $modelData = $model->getData();
        var_dump($postData['delete']);
//        exit;
        if ($postData['delete'] != NULL)
        {
            $model->setPhoto('');
            $model->save();
        }
        if ($fname != NULL)
        {
            try {
                if (!is_dir($path))
                {
                    mkdir($path, 0777, true);
                }
                $dbPath = '/media/tim/' . $customerId . '/' . $fname;
                $uploader = new Varien_File_Uploader('image');
                $uploader->setAllowedExtensions(array('png', 'gif', 'jpeg', 'jpg'));
                $uploader->setAllowCreateFolders(true);
                $uploader->setAllowRenameFiles(false);
                $uploader->setFilesDispersion(false);
                $uploader->save($path, $fname);
                if(!empty($modelData)){
                    $model->setPhoto($dbPath);
                    $model->save();
                }else{
                    $model->setCustomerId($customerId);
                    $model->setPhoto($dbPath);
                    $model->save();
                }
            } catch (Exception $e) {
                echo 'Error Message: ' . $e->getMessage();
            }
        }
        if ($siteUrl != NULL)
        {
            $modelData = Mage::getModel('tim_recommendation/user')
                ->load($customerId, 'customer_id')
                ->getData();
            if(!empty($modelData))
            {
                $model->setSiteUrl($siteUrl);
                $model->save();
            }else{
                $model->setCustomerId($customerId);
                $model->setSiteUrl($siteUrl);
                $model->save();
            }
        }
//        $this->_redirectReferer('*/*/data');
    }
}