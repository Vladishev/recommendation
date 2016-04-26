<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Adminhtml_NoteReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title(Mage::helper('tim_recommendation')->__('Notes'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_noteReport'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_noteReport_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return true;
    }
}