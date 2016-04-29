<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_Render_RecomMedia
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_RecomMedia extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $recomId = (int) $row->getData($this->getColumn()->getIndex());
        $recomMedia = Mage::getModel('tim_recommendation/media')->load($recomId, 'recom_id')->getData();
        if (empty($recomMedia)) {
            return Mage::helper('tim_recommendation')->__('No');
        } else {
            return Mage::helper('tim_recommendation')->__('Yes');
        }
    }
}