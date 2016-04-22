<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Adminhtml_OpinionImagesController. Actions for opinion images grid.
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Adminhtml_OpinionImagesController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Set general data
     */
    public function indexAction()
    {
        $this->_title($this->__('Opinion images'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionImages'));
        $this->renderLayout();
    }

    /**
     * Opinion info action
     */
    public function opinionInfoAction()
    {
        $this->_title($this->__('Opinion images'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid action
     *
     * @return null
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionImages_grid')->toHtml()
        );
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_opinion_images');
    }
}