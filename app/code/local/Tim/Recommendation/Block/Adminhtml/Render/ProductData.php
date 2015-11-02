<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @copyright Copyright (c) 2015 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_ProductData extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $productId = $row->getData($this->getColumn()->getIndex());
        $product = Mage::getModel('catalog/product')->load($productId);
        $productMedia = $product->getMediaGalleryImages()->getItems();
        if (empty($productMedia)) {
            return Mage::helper('tim_recommendation')->__('No');
        } else {
            return Mage::helper('tim_recommendation')->__('Yes');
        }
    }
}