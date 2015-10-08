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
    public function saveCustomerAction($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();

        $avatar = null;
        $banner = null;
        $siteUrl = null;
        $postData = $controller->getRequest()->getPost();
        if (!empty($_FILES['image']['name'])) {
            $avatar = time() . $_FILES['image']['name'];
        }
        if (!empty($_FILES['banner']['name'])) {
            $banner = time() . $_FILES['banner']['name'];
        }
        if (!is_null($postData['url'])) {
            $siteUrl = $postData['url'];
        }
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $path = Mage::getBaseDir('media') . '/tim/recommendation';
        $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $userData = $user->getData();
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if (!is_null($avatar)) {
            try {
                $this->saveImage($avatar, $path, 'image');
                $dbPath = '/media/tim/recommendation/' . $avatar;
                if (!empty($userData)) {
                    $user->setAd($dbPath);
                } else {
                    $user->setCustomerId($customerId);
                    $user->setAd($dbPath);
                }
                $user->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__($e->getMessage()));
            }
        }
        if (!is_null($banner)) {
            try {
                $this->saveImage($banner, $path, 'banner');
                $dbPath = '/media/tim/recommendation/' . $banner;
                if (!empty($userData)) {
                    $user->setBanner($dbPath);
                } else {
                    $user->setCustomerId($customerId);
                    $user->setBanner($dbPath);
                }
                $user->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__($e->getMessage()));
            }
        }
        if (!is_null($siteUrl)) {
            try {
                if (!empty($userData)) {
                    $user->setWww($siteUrl);
                } else {
                    $user->setCustomerId($customerId);
                    $user->setWww($siteUrl);
                }
                $user->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
    }
    /**
    * @ string $postName
    * @ string $path
    * @ string $varName
    **/
    public function saveImage($varName, $path, $postName)
    {
        $uploader = new Varien_File_Uploader($postName);
        $uploader->setAllowedExtensions(array('png', 'gif', 'jpeg', 'jpg'));
        $uploader->setAllowCreateFolders(true);
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $uploader->save($path, $varName);
    }
}