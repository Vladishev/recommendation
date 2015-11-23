<?php
class Tim_Recommendation_Block_Adminhtml_OpinionInfo extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Get array with opinion data
     * @return array
     */
    public function getOpinionData()
    {
        $recomId = $this->getRequest()->getParam('id');
        $checkedId = Mage::helper('tim_recommendation')->checkForOpinionComment($recomId);
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($checkedId)->getData();
        $opinionMedia = Mage::helper('tim_recommendation')->getOpinionMediaPath($checkedId);
        $opinion['media'] = $opinionMedia;

        return $opinion;
    }
}