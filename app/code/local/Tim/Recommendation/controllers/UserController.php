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
        $model = Mage::getModel('customer/customer');
        if ($model->load($userId)->getEntityId() != null) {
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

    public function editAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Saves customer data from /recommendation/user/edit/
     */
    public function saveCustomerAction()
    {

        $description = null;
        $avatar = null;
        $banner = null;
        $siteUrl = null;
        $nick = null;
        $postData = $this->getRequest()->getPost();
        if (!empty($_FILES['image']['name'])) {
            $avatar = time() . $_FILES['image']['name'];
        }
        if (!empty($_FILES['banner']['name'])) {
            $banner = time() . $_FILES['banner']['name'];
        }
        $siteUrl = $postData['url'];
        if (!is_null($postData['description'])) {
            $description = $postData['description'];
        }
        if (!is_null($postData['nick'])) {
            $nick = $postData['nick'];
        }
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $path = Mage::getBaseDir('media') . '/tim/recommendation';
        $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $userData = $user->getData();
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }

        if (isset($postData['banner-checkbox'])) {
            $user->setAd('')->save();
        }
        if (isset($postData['avatar-checkbox'])) {
            $user->setAvatar('')->save();
        }

        if (!is_null($avatar)) {
            try {
                Mage::helper('tim_recommendation')->saveImage($avatar, $path, 'image');
                $dbPath = '/media/tim/recommendation/' . $avatar;
                if (!empty($userData)) {
                    $user->setAvatar($dbPath);
                } else {
                    $user->setCustomerId($customerId);
                    $user->setAvatar($dbPath);
                }
                $user->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__($e->getMessage()));
            }
        }
        if (!is_null($banner)) {
            try {
                Mage::helper('tim_recommendation')->saveImage($banner, $path, 'banner');
                $dbPath = '/media/tim/recommendation/' . $banner;
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
        try {
            if ($userData != null) {
                $user->setWww($siteUrl);
            } else {
                $user->setCustomerId($customerId);
                $user->setWww($siteUrl);
            }
            $user->save();
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
        }
        if (!empty($description)) {
            try {
                if (!empty($userData)) {
                    $user->setDescription($description);
                } else {
                    $user->setCustomerId($customerId);
                    $user->setDescription($description);
                }
                $user->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError($e->getMessage());
            }
        }
        if (!empty($userData)) {
            $user->setUserType($postData['user_type']);
        } else {
            $user->setCustomerId($customerId);
            $user->setUserType($postData['user_type']);
        }
        try {
            $user->save();
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('User type didn\'t save.'));
        }
        if (!empty($nick)) {
            $existingNick = Mage::getModel('tim_recommendation/user')->load($nick, 'nick');
            if (empty($existingNick['nick'])) {
                try {
                    if (!empty($userData)) {
                        $user->setNick($nick);
                    } else {
                        $user->setCustomerId($customerId);
                        $user->setNick($nick);
                    }
                    $user->save();
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($e->getMessage());
                }
            } elseif ($existingNick['nick'] == $nick and $existingNick['customer_id'] == $customerId) {
            }else {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Sorry, but this nick already exist.'));
            }
        }

        $this->_redirect('*/*/edit');
    }
}