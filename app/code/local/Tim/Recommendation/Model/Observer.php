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

    public function saveUserStars()
    {
        if (Mage::app()->getRequest()->getParam('section') == 'tim_recommendation') {
            $userLevelsClient = unserialize(Mage::getStoreConfig('tim_recommendation/user_level/client'));
            $usersCollection = Mage::getModel('tim_recommendation/user')->getCollection();
            foreach ($usersCollection as $user) {
                $userPoints = $user->getPoints();
                $level = 0;
                foreach ($userLevelsClient as $userLevel) {
                    if ($userPoints >= $userLevel['from'] && $userPoints <= $userLevel['to']) {
                        $level = $userLevel['point'];
                        break;
                    }
                }
                if ($user->getLevel() < $level) {
                    $user->setLevel($level)->save();
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
     * Send email with information about person who added opinion | sends email to user
     * @param (obj)$observer
     */
    public function sendOpinionEmail($observer)
    {
        $opinionData = $observer->getEvent()->getOpinionData();
        $email = Mage::getStoreConfig('tim_settings/confirm_set/tim_email_to');
        $status = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_enabled');
        $statusToUser = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_opinion_inform');
        if ($status == 1 and !empty($email)) {
            $this->sendEmail($email, $opinionData, 'Opinion');
        }
        if ($statusToUser == 1) {
            $this->sendEmailToUser($opinionData['user_id'], 'opinion');
        }
    }

    /**
     * Send email with information about acceptance opinion | comment
     * @param (obj)$observer
     */
    public function sendAcceptConfirmationEmail($observer)
    {
        $opinionId = $observer->getEvent()->getOpinionId();
        $customerId = Mage::getModel('tim_recommendation/recommendation')->load($opinionId)->getUserId();
        $customerInfo = Mage::getModel('customer/customer')->load($customerId)->getData();
        $customerEmail = $customerInfo['email'];
        $type = Mage::helper('tim_recommendation')->checkOpinionOrComment($opinionId);
        if ($type == 'opinion') {
            $customerSubject = 'Opinion accepted';
            $customerInfo['subject'] = Mage::helper('tim_recommendation')->__($customerSubject);
            $customerInfo['type'] = Mage::helper('tim_recommendation')->__('opinion');
        } else {
            $customerSubject = 'Comment accepted';
            $customerInfo['subject'] = Mage::helper('tim_recommendation')->__($customerSubject);
            $customerInfo['type'] = Mage::helper('tim_recommendation')->__('comment');
        }

        $sendConfirmationToUser = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_send_confirmation_' . $type);
        if ($sendConfirmationToUser == 1) {
            $this->sendEmail($customerEmail, $customerInfo, $customerSubject);
        }
    }

    /**
     * Send email with information about person who added comment | sends email to user
     * @param (obj)$observer
     */
    public function sendCommentEmail($observer)
    {
        $commentData = $observer->getEvent()->getCommentData();
        $email = Mage::getStoreConfig('tim_settings/confirm_set/tim_email_to');
        $status = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_comment_enabled');
        $statusToUser = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_comment_inform');
        if ($status == 1 and !empty($email)) {
            $this->sendEmail($email, $commentData, 'Comment');
        }
        if ($statusToUser == 1) {
            $this->sendEmailToUser($commentData['user_id'], 'comment');
        }
    }

    /**
     * Sends email to admin | sends email to user
     * @param $observer
     */
    public function sendMalpracticeEmail($observer)
    {
        $malpracticeData = $observer->getEvent()->getMalpracticeData();
        $emailToAdmin = Mage::getStoreConfig('tim_settings/confirm_set/tim_malpractice_email_to');
        $status = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_malpractice_enabled');
        $statusToUser = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_malpractice_inform');
        if ($status == 1 and !empty($emailToAdmin)) {
            $this->sendEmail($emailToAdmin, $malpracticeData, 'Malpractice');
        }
        if ($statusToUser == 1) {
            $this->sendEmailToUser($malpracticeData['userId'], 'malpractice');
        }
    }

    /**
     * Send email with confirmation for abuse
     * @param $observer
     */
    public function sendMalpracticeAcceptanceEmail($observer)
    {
        $malpracticeData = $observer->getEvent()->getMalpracticeData();
        $this->sendEmailToUser(null, 'malpractice', $malpracticeData['email']);
    }

    /**
     * Sending email
     * @param (str)$toEmail
     * @param (arr)$templateVar
     * @param (str)$subject
     */
    public function sendEmail($toEmail, $templateVar, $subject)
    {
        switch ($subject) {
            case 'Opinion':
                $templateId = 'opinion_template';
                $copyTo = explode(',', rtrim(Mage::getStoreConfig('tim_settings/confirm_set/tim_copy_to'), ',;'));
                break;
            case 'Comment':
                $templateId = 'comment_template';
                $copyTo = explode(',', rtrim(Mage::getStoreConfig('tim_settings/confirm_set/tim_comment_copy_to'), ',;'));
                break;
            case 'Malpractice':
                $templateId = 'malpractice_template';
                $copyTo = explode(',', rtrim(Mage::getStoreConfig('tim_settings/confirm_set/tim_malpractice_copy_to'), ',;'));
                break;
            case 'User':
                $templateId = 'user_template';
                $subject = ucfirst($templateVar['subject']);
                break;
            case 'Opinion accepted':
            case 'Comment accepted':
                $templateId = 'confirmation_for_user_template';
                $subject = ucfirst($templateVar['subject']);
                break;
        }
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
        $processedTemplate = $emailTemplate->getProcessedTemplate($templateVar);
        $mail = Mage::getModel('core/email')
            ->setToEmail($toEmail)
            ->setBody($processedTemplate)
            ->setSubject(Mage::helper('tim_recommendation')->__($subject))
            ->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setType('html');
        if (!empty($copyTo[0])) {
            switch ($subject) {
                case 'Opinion':
                    $method = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_copy_method');
                    break;
                case 'Comment':
                    $method = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_comment_copy_method');
                    break;
                case 'Malpractice':
                    $method = (integer)Mage::getStoreConfig('tim_settings/confirm_set/tim_malpractice_copy_method');
                    break;
            }
            switch ($method) {
                case 1:
                    $mail->setCc($copyTo);
                    break;
                case 2:
                    $mail->setBcc($copyTo);
                    break;
            }
        }

        if ($subject == 'Opinion') {
            $mail->send($templateVar);
        } else {
            $mail->send();
        }
    }

    /**
     * Sends email to user
     * @param (int)$userId
     * @param (str)$userSubject
     * @param string | null $userEmail
     */
    public function sendEmailToUser($userId, $userSubject, $userEmail = null)
    {
        $_helper = Mage::helper('tim_recommendation');
        if(!empty($userId)){
            $userInformation = Mage::getModel('customer/customer')->load($userId)->getData();
            $userEmail = $userInformation['email'];
        }
        $userInformation['subject'] = $_helper->__($userSubject);

        $this->sendEmail($userEmail, $userInformation, 'User');
    }
}