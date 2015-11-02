<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Model_Email extends Aschroder_SMTPPro_Model_Email
{
    /**
     * Customized for sending Cc and Bcc email
     * and adding attachment to letter
     * @return $this
     */
    public function send($templateVar = null)
    {
        $_helper = Mage::helper('smtppro');

        // If it's not enabled, just return the parent result.
        if (!$_helper->isEnabled()) {
            return parent::send();
        }

        if (Mage::getStoreConfigFlag('system/smtp/disable')) {
            return $this;
        }

        $mail = new Zend_Mail('utf-8');

        if (strtolower($this->getType()) == 'html') {
            $mail->setBodyHtml($this->getBody());
        } else {
            $mail->setBodyText($this->getBody());
        }

        $mail->setFrom($this->getFromEmail(), $this->getFromName())
            ->addTo($this->getToEmail(), $this->getToName())
            ->setSubject($this->getSubject());
//start rewrite//////////
         if ($this->getBcc()) {
             $mail->addBcc($this->getBcc());
         }
         if ($this->getCc()) {
             $mail->addCc($this->getCc());
         }

         if(isset($templateVar['image_name0'])) {
             $i = 0;
             while($templateVar['image_name'.$i]) {
                 $attachment = $mail->createAttachment(file_get_contents(Mage::getBaseDir() . $templateVar['image_name'.$i]));
                 $attachment->type = $templateVar['image_type'.$i];
                 $attachment->disposition = Zend_Mime::DISPOSITION_INLINE;
                 $attachment->encoding = Zend_Mime::ENCODING_BASE64;
                 $attachment->filename = 'Image';
                 $i++;
             }
         }
//end rewrite////////////
        $transport = new Varien_Object(); // for observers to set if required
        Mage::dispatchEvent('aschroder_smtppro_before_send', array(
            'mail' => $mail,
            'email' => $this,
            'transport' => $transport
        ));

        if ($transport->getTransport()) { // if set by an observer, use it
            $mail->send($transport->getTransport());
        } else {
            $mail->send();
        }

        Mage::dispatchEvent('aschroder_smtppro_after_send', array(
            'to' => $this->getToName(),
            'subject' => $this->getSubject(),
            'template' => "n/a",
            'html' => (strtolower($this->getType()) == 'html'),
            'email_body' => $this->getBody()));

        return $this;
    }
}