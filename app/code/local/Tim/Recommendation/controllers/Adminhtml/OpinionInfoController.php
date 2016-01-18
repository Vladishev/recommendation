<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Adminhtml_OpinionInfoController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Set popup content
     */
    public function infoPopupAction()
    {
        $this->loadLayout();
        $body = $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionInfo')
            ->setTemplate('tim/recommendation/opinionPopup.phtml')->toHtml();
        $this->getResponse()->setBody($body);
    }

    protected function _isAllowed()
    {
        return true;
    }
}