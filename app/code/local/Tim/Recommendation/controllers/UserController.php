<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_UserController. User profile methods.
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_UserController extends Mage_Core_Controller_Front_Action
{
    /**
     * Checking user id and open user_profile page
     */
    public function profileAction()
    {
        $userId = (int)$this->getRequest()->getParam('id');
        $userData = Mage::getModel('customer/customer')->load($userId);
        if ($userData->getEntityId()) {
            Mage::register('user_data', $userData);
            $this->loadLayout();
            $this->renderLayout();
        } else {
            $this->norouteAction();
            return;
        }
    }

    /**
     * Load and render layout
     */
    public function dataAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Load and render layout
     */
    public function editAction()
    {
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            return $this->_redirect('customer/account/login', array('_secure' => true));
        }
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
        $nick = false;
        $defaultAvatar = null;
        $postData = $this->getRequest()->getPost();
        if (!empty($postData['selected_avatar'])) {
            $avatar = $postData['selected_avatar'];
            $defaultAvatar = true;
        }
        if (!empty($postData['avatar-hide'])) {
            $avatar = explode('|', $postData['avatar-hide']);
            $defaultAvatar = false;
        }
        if (!empty($postData['banner-hide'])) {
            $banner = explode('|', $postData['banner-hide']);
        }
        $siteUrl = !empty($postData['url']) ? $postData['url'] : '';
        if (!empty($postData['description'])) {
            $description = $postData['description'];
        }
        if (!empty($postData['nick'])) {
            $nick = $postData['nick'];
        }
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $path = Mage::getBaseDir('media') . '/tim/recommendation';
        $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $userData = $user->getData();
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }

        if (isset($postData['banner-checkbox'])) {
            $user->setAd('')->save();
        }
        if (isset($postData['avatar-checkbox'])) {
            $user->setAvatar('')->save();
        }

        if (!is_null($avatar)) {
            try {
                if ($defaultAvatar == false) {
                    $this->_cleanDirectory($avatar[2]);
                    rename($avatar[1], $avatar[0]);
                    if (!empty($userData)) {
                        $user->setAvatar($avatar[0]);
                    } else {
                        $user->setCustomerId($customerId);
                        $user->setAvatar($avatar[0]);
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
                $this->_cleanDirectory($banner[2]);
                rename($banner[1], $banner[0]);
                if (!empty($userData)) {
                    $user->setAd($banner[0]);
                } else {
                    $user->setCustomerId($customerId);
                    $user->setAd($banner[0]);
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
     * Removes all files from directory
     *
     * @param string $path Path to folder
     */
    protected function _cleanDirectory($path)
    {
        $oldFiles = glob($path . '*');
        if ($oldFiles) {
            foreach ($oldFiles as $oldFile) {
                unlink($oldFile);
            }
        }
    }

    /**
     * Check file type
     *
     * @param string $fileType File extension
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
     *
     * @param int $fileSize File size
     * @param int $limit Limit size
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

    /**
     * Saves cropped image and returns path to it
     *
     * @return bool
     */
    public function saveCropImageAction()
    {
        $data = $this->getRequest()->getParams();
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $typeOfImage = $data['typeOfImage'];
        $folderName = $typeOfImage;
        $imageData = $data['data'];
        list($typeData, $imageData) = explode(';', $imageData);
        $imageType = substr($typeData, 11);
        $imageName = $typeOfImage . '-' . $customerId . '.' . $imageType;
        $imagePath['path'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . DS . 'media' . DS . 'tim' . DS . 'recommendation' . DS . $folderName . DS . $folderName . $customerId . DS . 'tmp' . DS . $imageName;
        $imagePath['formData'] = 'media' . DS . 'tim' . DS . 'recommendation' . DS . $folderName . DS . $folderName . $customerId . DS . $imageName;
        $imagePath['tmpFolder'] = 'media' . DS . 'tim' . DS . 'recommendation' . DS . $folderName . DS . $folderName . $customerId . DS . 'tmp' . DS . $imageName;
        $imagePath['imgFolder'] = 'media' . DS . 'tim' . DS . 'recommendation' . DS . $folderName . DS . $folderName . $customerId . DS;
        list(, $imageData) = explode(',', $imageData);
        $imageData = base64_decode($imageData);
        $folderForImage = Mage::getBaseDir('media') . DS . 'tim' . DS . 'recommendation' . DS . $folderName . DS . $folderName . $customerId . DS . 'tmp';

        if (!is_dir($folderForImage)) {
            mkdir($folderForImage, 0755, true);
        }

        try {
            $this->_cleanDirectory($folderForImage);
            file_put_contents($folderForImage . DS . $imageName, $imageData);
            $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($imagePath));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            return false;
        }
    }
}