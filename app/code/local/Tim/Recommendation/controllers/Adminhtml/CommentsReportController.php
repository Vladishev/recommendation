<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Adminhtml_CommentsReportController extends Mage_Adminhtml_Controller_Action
{
    public function indexAction()
    {
        $this->_title($this->__('Comments'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_commentsReport'));
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
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_commentsReport_grid')->toHtml()
        );
    }

    /** Changed data in acceptance field to 1
     * @throws Exception
     */
    public function massAcceptanceYesAction()
    {
        $commentsId = $this->getRequest()->getParam('acceptance');
        if (!empty($commentsId)) {
            foreach ($commentsId as $item) {
                $recommendationModel = Mage::getModel('tim_recommendation/recommendation')->load((integer)$item, 'recom_id');
                $recommendationModel->setAcceptance(1);
                $recommendationModel->setPublicationDate(date('Y-m-d H:i:s', Mage::getModel('core/date')->timestamp(time())));
                $recommendationModel->save();
                Mage::dispatchEvent('controller_index_allow_opinion_data', array('opinion_id' => $item));
            }
            $this->_addAlert('allowed', $commentsId);
        }
        $this->_redirect('*/*/index');
    }

    /**Changed data in acceptance field to 0
     * @throws Exception
     */
    public function massAcceptanceNoAction()
    {
        $commentsId = $this->getRequest()->getParam('acceptance');
        if (!empty($commentsId)) {
            foreach ($commentsId as $item) {
                $recommendationModel = Mage::getModel('tim_recommendation/recommendation')->load((integer)$item, 'recom_id');
                $recommendationModel->setAcceptance(0);
                $recommendationModel->save();
            }
            $this->_addAlert('denied', $commentsId);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Added alert to user
     * @param (string)$status
     * @param (int)$id
     */
    protected function _addAlert($status, $id)
    {
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('tim_recommendation')->__(
                'Total of %d opinion(s) were ' . $status . '.', count($id)
            ));
    }

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_comments');
    }
}