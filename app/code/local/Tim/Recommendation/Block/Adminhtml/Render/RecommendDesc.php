<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_Render_RecommendDesc
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_RecommendDesc extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Count of characters which can be shown
     */
    const LENGTH_COUNT = 100;

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $recommendationId = $row->getData($this->getColumn()->getIndex());
        $opinion = Mage::getModel('tim_recommendation/recommendation')
            ->getCollection()
            ->addFieldToFilter('recom_id', array( array('eq' => $recommendationId)))
            ->addFieldToSelect('advantages')
            ->getFirstItem();
        $description = '<p align="center">' . $recommendationId;
        $advantages = $opinion->getAdvantages();
        $popupUrl = '</br><a id="recomId_' . $recommendationId . '" href="javascript:displayRecommendationPopup(' . $recommendationId . ');">' . Mage::helper('tim_recommendation')->__('View') . '</a>';
        if (!empty($advantages)) {
            if (strlen($advantages) > self::LENGTH_COUNT) {
                $description .= '</br>' . substr($advantages, 0, (self::LENGTH_COUNT - 1)) . '...';
            } else {
                $description .= '</br>' . $advantages;
            }
        }
        $description .= $popupUrl . '</p>';

        return $description;
    }
}