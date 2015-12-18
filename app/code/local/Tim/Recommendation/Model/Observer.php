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
        if (!empty($copyTo[0])) {

            $method = (integer)Mage::getStoreConfig('tim_confirm/confirm_opinion/tim_copy_method');

            if ($method === 1)
            {
                $mail->setCc($copyTo);
            }
            if ($method === 2)
            {
                $mail->setBcc($copyTo);
            }
        }

        if($subject == 'Opinion')
        {
            $mail->send($templateVar);
        }else{
            $mail->send();
        }
    }
}