<?php

class Tim_Recommendation_Block_Adminhtml_Render_Average extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Calculate average rating
     * @param Varien_Object $row
     * @return float|string
     */
    public function render(Varien_Object $row)
    {
        $recomId = $row->getData($this->getColumn()->getIndex());
        $record = Mage::getModel('tim_recommendation/recommendation')->load($recomId);
        $ratingPrice = $record->getRatingPrice();
        $ratingDurability = $record->getRatingDurability();
        $ratingFailure = $record->getRatingFailure();
        $ratingService = $record->getRatingService();

        $sum = $ratingPrice + $ratingDurability + $ratingFailure + $ratingService;
        $average = round(($sum)/4, 1);

        return $average;
    }
}