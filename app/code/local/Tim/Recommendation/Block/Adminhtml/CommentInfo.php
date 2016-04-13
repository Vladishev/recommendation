<?php
class Tim_Recommendation_Block_Adminhtml_CommentInfo extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Get array with comment data
     * @return array
     */
    public function getCommentData()
    {
        $recomId = $this->getRequest()->getParam('id');
        return $this->getRequest()->getParam('id');
    }
}