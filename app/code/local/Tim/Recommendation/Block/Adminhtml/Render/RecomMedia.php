<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @copyright Copyright (c) 2015 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_RecomMedia extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $productId = $row->getData($this->getColumn()->getIndex());
        $recomMedia = Mage::getModel('tim_recommendation/media')->load($productId,'recom_id')->getData();
        if (empty($recomMedia)) {
            return Mage::helper('tim_recommendation')->__('No');
        } else {
            return Mage::helper('tim_recommendation')->__('Yes');
        }
    }
}