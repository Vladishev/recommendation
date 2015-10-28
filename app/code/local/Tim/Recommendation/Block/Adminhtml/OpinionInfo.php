<?php
class Tim_Recommendation_Block_Adminhtml_OpinionInfo extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Get array with opinion data
     * @return array
     */
    public function getOpinionData()
    {
        $opinionId = $this->getRequest()->getParam('id');
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($opinionId)->getData();
        $opinionMedia = Mage::helper('tim_recommendation')->getOpinionMediaPath($opinionId);
        $opinion['media'] = $opinionMedia;

        return $opinion;
    }
}