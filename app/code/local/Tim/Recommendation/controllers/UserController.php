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
        if (!empty($postData['selected_avatar'])) {
            $avatar = $postData['selected_avatar'];
            $defaultAvatar = true;
        }
        if (!empty($postData['avatar-hide'])) {
            $avatar = $postData['avatar-hide'];
        }
        if (!empty($postData['banner-hide'])) {
            $banner = $postData['banner-hide'];
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
                if (empty($defaultAvatar)) {
                    if (!empty($userData)) {
                        $user->setAvatar($avatar);
                    } else {
                        $user->setCustomerId($customerId);
                        $user->setAvatar($avatar);
                    }
                } else {
                    $dbPath = '/media/tim/recommendation/' . $avatar;
                    if (!empty($userData)) {
                        $user->setAvatar($dbPath);
                    } else {
                        $user->setCustomerId($customerId);
                        $user->setAvatar($dbPath);
                    }
                }
                $user->save();
            } catch (Exception $e) {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__($e->getMessage()));
            }
        }
        if (!is_null($banner)) {
            try {
                if (!empty($userData)) {
                    $user->setAd($banner);
                } else {
                    $user->setCustomerId($customerId);
                    $user->setAd($banner);
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
            } else {
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Sorry, but this nick already exist.'));
            }
        }

        $this->_redirect('*/*/edit');
    }

    /**
     * Check file type
     * @param string $fileType
     * @return bool
     */
    public function checkFileType($fileType)
    {
        switch ($fileType) {
            case 'image/png':
            case 'image/jpeg':
                break;
            default:
                Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Nie można przesłać pliku. Dopuszczalne są pliki graficzne w formacie jpg lub png.'));
                return false;
        }
        return true;
    }

    /**
     * Check file size
     * @param int $fileSize
     * @param int $limit
     * @return bool
     */
    public function checkFileSize($fileSize, $limit)
    {
        if ($fileSize > $limit) {
            Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Nie można przesłać pliku. Maksymalny rozmiar to 400 kb.'));
            return false;
        }
        return true;
    }

    public function saveCropImageAction()
    {
        $data = $this->getRequest()->getParams();
        $customerId = $data['customerId'];
        $typeOfImage = $data['typeOfImage'];
        $folderName = $typeOfImage;
        $imageData = $data['data'];
        list($typeData, $imageData) = explode(';', $imageData);
        $imageType = substr($typeData, 11);
        $imageName = $typeOfImage . '-' . $customerId .'.' . $imageType;
        $imagePath['path'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . DS . 'media' . DS . 'tim' . DS . 'recommendation' . DS . $folderName . DS . $imageName;
        $imagePath['formData'] = 'media' . DS . 'tim' . DS . 'recommendation' . DS . $folderName . DS . $imageName;
        list(, $imageData)      = explode(',', $imageData);
        $imageData = base64_decode($imageData);
        $folderForImage = Mage::getBaseDir('media') . DS . 'tim' . DS . 'recommendation' . DS . $folderName;
        if (!is_dir($folderForImage)) {
            mkdir($folderForImage, 0777, true);
        }
        try {
            file_put_contents($folderForImage . DS . $imageName, $imageData);
            echo json_encode($imagePath);
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            return false;
        }

    }
}