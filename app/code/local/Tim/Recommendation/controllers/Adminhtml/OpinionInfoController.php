<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Adminhtml_OpinionInfoController. Actions for opinion info popup.
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
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

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}