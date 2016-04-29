<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Adminhtml_CommentsReportController. Actions for comment grid.
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Adminhtml_CommentsReportController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Set general data
     */
    public function indexAction()
    {
        $this->_title(Mage::helper('tim_recommendation')->__('Comments'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_commentsReport'));
        $this->renderLayout();
    }

    public function commentInfoAction()
    {
        $this->_title(Mage::helper('tim_recommendation')->__('Comment info'));
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
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_commentsReport_grid')->toHtml()
        );
    }

    /**
     * Changed data in acceptance field to 1(accepted)
     *
     * @throws Exception
     */
    public function massAcceptanceYesAction()
    {
        $commentsIds = $this->getRequest()->getParam('acceptance');
        if(!is_array($commentsIds)){
            $commentsIds = array($commentsIds);
        }
        if (!empty($commentsIds)) {
            foreach ($commentsIds as $id) {
                $recommendationModel = Mage::getModel('tim_recommendation/recommendation')->load((int) $id, 'recom_id');
                //add points for comment by customer
                Mage::helper('tim_recommendation')->savePointsForCustomer($recommendationModel);
                $recommendationModel->setAcceptance(1);
                $recommendationModel->setPublicationDate(date('Y-m-d H:i:s'));
                $recommendationModel->save();
                Mage::dispatchEvent('controller_index_allow_opinion_data', array('opinion_id' => $id));
            }
            $this->_addAlert('allowed', $commentsIds);
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
        $commentsIds = $this->getRequest()->getParam('acceptance');
        if(!is_array($commentsIds)){
            $commentsIds = array($commentsIds);
        }
        if (!empty($commentsIds)) {
            foreach ($commentsIds as $id) {
                $recommendationModel = Mage::getModel('tim_recommendation/recommendation')->load((int) $id, 'recom_id');
                $recommendationModel->setAcceptance(0);
                $recommendationModel->save();
            }
            $this->_addAlert('denied', $commentsIds);
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Send email to customer about modification
     * @throws Exception
     */
    public function modifyCommentAction()
    {
        $commentIds = $this->getRequest()->getParam('acceptance');
        if(!is_array($commentIds)){
            $commentIds = array($commentIds);
        }
        if (!empty($commentIds)) {
            foreach ($commentIds as $id) {
                $comment = Mage::getModel('tim_recommendation/recommendation')->load($id, 'recom_id');
                $customer = Mage::getModel('customer/customer')->load($comment->getUserId());
                $product = Mage::getModel('catalog/product')->load($comment->getProductId());
                $templateVar = array();
                $templateVar['customerName'] = $customer->getName();
                $templateVar['productName'] = $product->getName();
                $templateVar['indexTim'] = $product->getSku();
                Mage::helper('tim_recommendation')->sendEmail($customer->getEmail(), $templateVar, 'modify_comment_template', 'Komentarz został zablokowany');
            }
            $this->_addAlert('modified', $commentIds);
        }

        $this->_redirect('*/*/index');
    }

    /**
     * Added alert to user
     *
     * @param string $status Key word for alert
     * @param array $id Array with comments recom_id(tim_recommendation table)
     */
    protected function _addAlert($status, $id)
    {
        Mage::getSingleton('adminhtml/session')->addSuccess(
            Mage::helper('tim_recommendation')->__(
                'Total of %d comment(s) were %s.', count($id), $status
            ));
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('report/tim/tim_comments');
    }
}