<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Adminhtml_OpinionReportController. Actions for opinion grid.
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Adminhtml_OpinionReportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Set general data
     */
    public function indexAction()
    {
        $this->_title($this->__('Opinions'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionReport'));
        $this->renderLayout();
    }

    /**
     * Opinion info action
     */
    public function opinionInfoAction()
    {
        $this->_title($this->__('Opinion info'));
        $this->loadLayout();
        $this->renderLayout();
    }

    /**
     * Grid action
     */
    public function gridAction()
    {
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionReport_grid')->toHtml()
        );
    }

    /**
     * Changed data in acceptance field to 1(accepted)
     *
     * @throws Exception
     */
    public function massAcceptanceYesAction()
    {
        $opinionIds = $this->getRequest()->getParam('acceptance');
        if(!is_array($opinionIds)){
            $opinionIds = array($opinionIds);
        }
        if (!empty($opinionIds)) {
            foreach ($opinionIds as $id) {
                $recommendationModel = Mage::getModel('tim_recommendation/recommendation')->load((int) $id, 'recom_id');
                //add points for opinion by customer
                Mage::helper('tim_recommendation')->savePointsForCustomer($recommendationModel);
                $recommendationModel->setAcceptance(1);
                $recommendationModel->setPublicationDate(date('Y-m-d H:i:s'));
                $recommendationModel->save();
                Mage::dispatchEvent('controller_index_allow_opinion_data', array('opinion_id' => $id));
            }
            $this->_addAlert('allowed', $opinionIds);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Changed data in acceptance field to 0(not accepted)
     *
     * @throws Exception
     */
    public function massAcceptanceNoAction()
    {
        $opinionIds = $this->getRequest()->getParam('acceptance');
        if(!is_array($opinionIds)){
            $opinionIds = array($opinionIds);
        }
        if (!empty($opinionIds)) {
            foreach ($opinionIds as $id) {
                $recommendationModel = Mage::getModel('tim_recommendation/recommendation')->load((int) $id, 'recom_id');
                $recommendationModel->setAcceptance(0);
                $recommendationModel->save();
            }
            $this->_addAlert('denied', $opinionIds);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Send email to customer about modification
     */
    public function modifyOpinionAction()
    {
        $opinionIds = $this->getRequest()->getParam('acceptance');
        if(!is_array($opinionIds)){
            $opinionIds = array($opinionIds);
        }
        if (!empty($opinionIds)) {
            foreach ($opinionIds as $id) {
                $opinion = Mage::getModel('tim_recommendation/recommendation')->load($id, 'recom_id');
                $customer = Mage::getModel('customer/customer')->load($opinion->getUserId());
                $product = Mage::getModel('catalog/product')->load($opinion->getProductId());
                $templateVar = array();
                $templateVar['customerName'] = $customer->getName();
                $templateVar['productName'] = $product->getName();
                $templateVar['indexTim'] = $product->getSku();
                Mage::helper('tim_recommendation')->sendEmail($customer->getEmail(), $templateVar, 'modify_opinion_template', 'Opinia została zablokowana');
            }
            $this->_addAlert('modified', $opinionIds);
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Added alert to user
     *
     * @param string $status Key word for alert
     * @param array $id Array with opinions recom_id(tim_recommendation table)
     */
    protected function _addAlert($status, $id)
    {
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('tim_recommendation')->__(
                'Total of %d opinion(s) were ' . $status . '.', count($id)
            ));
    }

    /**
     * Export recommendation grid to CSV format
     */
    public function exportCsvAction()
    {
        $fileName = 'recommendations.csv';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export recommendation grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName = 'recommendations.xml';
        $grid = $this->getLayout()->createBlock('tim_recommendation/adminhtml_opinionReport_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_recommendation');
    }
}