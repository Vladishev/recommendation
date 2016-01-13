<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @copyright Copyright (c) 2015 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_Comments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $recommendationId = $row->getRecomId();
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($recommendationId);
        if (!$opinion->getParent()) {
            $advantages = $opinion->getAdvantages();
            if (strlen($advantages) > 100) {
                $advantages = substr($advantages, 0, 99) . '...';
            }
            return $advantages;
        }
    }
}