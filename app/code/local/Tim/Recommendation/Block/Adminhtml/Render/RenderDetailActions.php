<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy (vladomsu@gmail.com)
 */
class Tim_Recommendation_Block_Adminhtml_Render_RenderDetailActions extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Display links to comment or opinion
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $recomId = $row->getRecomId();
        if ($row->getParent()) {
            $string = '<a href="' . Mage::helper('adminhtml')->getUrl('*/commentsReport/commentInfo', array('id' => $recomId)) . '">' . Mage::helper('tim_recommendation')->__('Display comment') . '</a>';
        } else {
            $string = '<a href="' . Mage::helper('adminhtml')->getUrl('*/opinionReport/opinionInfo', array('id' => $recomId)) . '">' . Mage::helper('tim_recommendation')->__('Display opinion') . '</a>';
        }
        return $string;
    }
}