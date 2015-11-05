<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Adminhtml_OpinionCommentReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Opinion and comment'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionCommentReport'));
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
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionCommentReport_grid')->toHtml()
        );
    }

    /**
     * Export recommendation grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'recommendations.csv';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionCommentReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export recommendation grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'recommendations.xml';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionCommentReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_opinion_comment');
    }
}