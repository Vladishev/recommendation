<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Adminhtml_CommentsToOpinionController extends Mage_Adminhtml_Controller_Action
{
    /**
     * TODO remove this grid till 01/02/2016. Reason: useless. The same functionality in grid @see Tim_Recommendation_Block_Adminhtml_CommentsReport_Grid
     */
    public function indexAction()
    {
        $this->_title($this->__('Recommendations'));
        $this->loadLayout();
        $this->_setActiveMenu('report/tim');
        $this->_addContent($this->getLayout()->createBlock('tim_recommendation/adminhtml_commentsToOpinion'));
        $this->renderLayout();
    }

    public function opinionInfoAction()
    {
        $this->_title($this->__('All comments to opinion'));
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
            $this->getLayout()->createBlock('tim_recommendation/adminhtml_commentsToOpinion_grid')->toHtml()
        );
    }

    protected function _isAllowed()
    {
        return true;
    }
}