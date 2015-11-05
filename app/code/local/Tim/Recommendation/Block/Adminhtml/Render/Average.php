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
        if($record->getRatingPrice()){
            $ratingPrice = $record->getRatingPrice();
        }
        if($record->getRatingDurability()) {
            $ratingDurability = $record->getRatingDurability();
        }
        if($record->getRatingFailure()) {
            $ratingFailure = $record->getRatingFailure();
        }
        if($record->getRatingService()) {
            $ratingService = $record->getRatingService();
        }
        $summ = $ratingPrice + $ratingDurability + $ratingFailure + $ratingService;
        if (!empty($summ)) {
            $average = round(($summ)/4, 1);
            return $average;
        } else {
            $average = '0';
            return $average;
        }
    }
}