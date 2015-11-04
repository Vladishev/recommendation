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
    /**
     * CRUD value in table tim_user_type
     */
    public function saveUserLevel()
    {
        $userLevels = unserialize(Mage::getStoreConfig('tim_recommendation/user_level/level_values'));
        if (!empty($userLevels)) {
            if (is_array($userLevels)) {
                foreach ($userLevels as $id => $values) {
                    $model = Mage::getModel('tim_recommendation/userLevel');
                    if ($model->load($id, 'system_config_id')->getData()) {
                        $model->setPoint($values['point'])
                            ->setFrom($values['from'])
                            ->setTo($values['to']);
                    } else {
                        $model->setSystemConfigId($id)
                            ->setPoint($values['point'])
                            ->setFrom($values['from'])
                            ->setTo($values['to']);
                    }
                    try {
                        $model->save();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                        Mage::getSingleton('core/session')->addError(
                            Mage::helper('tim_recommendation')->__('There was error during the saving.'));
                    }
                }
                $ids = Mage::helper('tim_recommendation')->getUserLevelDiffIds($userLevels);
                foreach ($ids as $id) {
                    $forDelete = Mage::getModel('tim_recommendation/userLevel')->load($id, 'system_config_id');
                    try {
                        $forDelete->delete();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                    }
                }
            }
        } else {
            $collection = Mage::getModel('tim_recommendation/userLevel')->getCollection();
            foreach ($collection as $item) {
                try {
                    $item->delete();
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                }
            }
        }
    }

    /**
     * CRUD value in table tim_user_type
     */
    public function saveUserType()
    {
        $userTypes = unserialize(Mage::getStoreConfig('tim_recommendation/user_type/values'));
        if (!empty($userTypes)) {
            if (is_array($userTypes)) {
                foreach ($userTypes as $id => $values) {
                    $model = Mage::getModel('tim_recommendation/userType');
                    if ($model->load($id, 'system_config_id')->getData()) {
                        $model->setName($values['user_type'])
                            ->setAdmin($values['administrator']);
                    } else {
                        $model->setSystemConfigId($id)
                            ->setName($values['user_type'])
                            ->setAdmin($values['administrator']);
                    }
                    try {
                        $model->save();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                        Mage::getSingleton('core/session')->addError(
                            Mage::helper('tim_recommendation')->__('Didn\'t save %s value.', $values['user_type']));
                    }
                }
                $ids = Mage::helper('tim_recommendation')->getUserTypeDiffIds($userTypes);
                foreach ($ids as $id) {
                    $userType = Mage::getModel('tim_recommendation/userType')->load($id, 'system_config_id');
                    try {
                        $userType->delete();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                    }
                }
            }
        } else {
            $collection = Mage::getModel('tim_recommendation/userType')->getCollection();
            foreach ($collection as $item) {
                try {
                    $item->delete();
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                }
            }
        }
    }

    /**
     * Send email with information about person who added opinion
     * @param (obj)$observer
     */
    public function sendOpinionEmail($observer)
    {
        $opinionData = $observer->getEvent()->getOpinionData();
        $email = Mage::getStoreConfig('tim_confirm/confirm_opinion/tim_email_to');
        $status = (integer)Mage::getStoreConfig('tim_confirm/confirm_opinion/tim_enabled');
        if ($status == 1 and !empty($email))
        {
                $this->sendEmail($email, $opinionData, 'Opinion');
        }
    }

    /**
     * Send email with information about person who added comment
     * @param (obj)$observer
     */
    public function sendCommentEmail($observer)
    {
        $commentData = $observer->getEvent()->getCommentData();
        $email = Mage::getStoreConfig('tim_confirm/confirm_opinion/tim_email_to');
        $status = (integer)Mage::getStoreConfig('tim_confirm/confirm_opinion/tim_enabled');
        if ($status == 1 and !empty($email))
        {
            $this->sendEmail($email, $commentData, 'Comment');
        }
    }

    /**
     * Sending email
     * @param (str)$toEmail
     * @param (arr)$templateVar
     * @param (str)$subject
     */
    public function sendEmail($toEmail, $templateVar, $subject)
    {
        $copyTo = explode(',', rtrim(Mage::getStoreConfig('tim_confirm/confirm_opinion/tim_copy_to'), ',;'));
        $method = (integer)Mage::getStoreConfig('tim_confirm/confirm_opinion/tim_copy_method');
        if ($subject == 'Opinion') {
            $templateId = 'opinion_template';
        } else {
            $templateId = 'comment_template';
        }
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
        $processedTemplate = $emailTemplate->getProcessedTemplate($templateVar);
        $mail = Mage::getModel('core/email')
            ->setToEmail($toEmail)
            ->setBody($processedTemplate)
            ->setSubject($subject)
            ->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setType('html');
        if ($method === 1)
        {
            $mail->setCc($copyTo);
        }
        if ($method === 2)
        {
            $mail->setBcc($copyTo);
        }

        if($subject == 'Opinion')
        {
            $mail->send($templateVar);
        }else{
            $mail->send();
        }

    }

    public function saveCustomerAction($observer)
    {
        $controller = $observer->getEvent()->getControllerAction();

        $description = null;
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
        if (!is_null($postData['description'])) {
            $description = $postData['description'];
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
                $this->saveImage($banner, $path, 'banner');
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
        if (!empty($siteUrl)) {
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
    }

    /**
     * Save image to folder
     * @param string $varName
     * @param string $path
     * @param string $postName
     */
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