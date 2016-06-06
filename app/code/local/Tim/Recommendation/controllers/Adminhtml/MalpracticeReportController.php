<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Adminhtml_MalpracticeReportController. Actions for malpractice grid.
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Adminhtml_MalpracticeReportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Set general data
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('tim_recommendation')->__('Abuse'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport'));
        $this->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport_grid')->toHtml()
        );
    }

    /**
     * Export recommendation grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'malpractice.csv';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export recommendation grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'malpractice.xml';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_malpracticeReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_malpractice');
    }
}