<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy (vladomsu@gmail.com)
 */
class Tim_Recommendation_Adminhtml_MalpracticeReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Malpractice'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport'));
        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
        $this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport_grid')->toHtml()
        );
    }

    public function exportCsvAction()
    {
        $fileName = 'recommendations.csv';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export recommendation grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'recommendations.xml';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_malpractice');
    }
}