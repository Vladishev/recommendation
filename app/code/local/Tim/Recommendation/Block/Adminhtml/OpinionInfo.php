<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_OpinionInfo
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_OpinionInfo extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Get array with opinion data
     *
     * @return array
     */
    public function getOpinionData()
    {
        $recomId = $this->getRequest()->getParam('id');
        $checkedId = Mage::helper('tim_recommendation')->checkForOpinionComment($recomId);
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($checkedId)->getData();
        $opinionMedia = Mage::helper('tim_recommendation')->getOpinionMediaPath($checkedId);
        $opinion['sku'] = Mage::getModel('catalog/product')->load($opinion['product_id'])->getSku();
        $opinion['movie_url'] = $opinionMedia['url/youtube'];
        if ($opinion['movie_url']) {
            unset($opinionMedia['url/youtube']);
        };
        $opinion['images'] = $opinionMedia;

        return $opinion;
    }
}