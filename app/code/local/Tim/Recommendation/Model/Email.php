<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Model_Email extends Mage_Core_Model_Email
{
    /**
     * Customized for sending Cc and Bcc email
     * @return $this
     */
    public function send($templateVar = null)
    {
        if (Mage::getStoreConfigFlag('system/smtp/disable')) {
            return $this;
        }

        $mail = new Zend_Mail();

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        }
        else {
            $mail->setBodyText($this->getBody());
        }

        $mail->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());

        if ($this->getBcc()) {
            $mail->addBcc($this->getBcc());
        }
        if ($this->getCc()) {
            $mail->addCc($this->getCc());
        }

        if(isset($templateVar['image_name'])) {
            $attachment = $mail->createAttachment(file_get_contents(Mage::getBaseDir().$templateVar['image_name']));
            $attachment->type        = $templateVar['image_type'];
            $attachment->disposition = Zend_Mime::DISPOSITION_INLINE;
            $attachment->encoding    = Zend_Mime::ENCODING_BASE64;
            $attachment->filename    = 'Image';
        }

        $mail->send();

        return $this;
    }
}