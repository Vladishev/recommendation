<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @copyright Copyright (c) 2015 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_RecommendDesc extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $recommendationId = $row->getData($this->getColumn()->getIndex());
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($recommendationId);

        $description = $recommendationId;
        $advantages = $opinion->getAdvantages();
        $popupUrl = '</br><a id="recomId_' . $recommendationId . '" href="javascript:displayRecommendationPopup(' . $recommendationId . ');">' . Mage::helper('tim_recommendation')->__('View') . '</a>';
        if (!empty($advantages)) {
            if (strlen($advantages) > 100) {
                $description .= ': ' . substr($advantages, 0, 99) . '...';
            } else {
                $description .= ': ' . $advantages;
            }
        }
        $description .= $popupUrl;

        return $description;
    }
}