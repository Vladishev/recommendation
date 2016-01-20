<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy (vladomsu@gmail.com)
 */
class Tim_Recommendation_Block_Adminhtml_Render_RenderTitle extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Render type of content
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $recomId = $row->getRecomId();
        $recomRow = Mage::getModel('tim_recommendation/recommendation')->load($recomId, 'recom_id');
        if ($recomRow->getParent()) {
            $title = $this->__('Comment');
        } else {
            $title = $this->__('Opinion');
        }
        return $title;
    }
}