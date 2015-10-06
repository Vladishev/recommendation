<?php

class Tim_Recommendation_Helper_Recommendation extends Mage_Core_Helper_Abstract
{
    public function renderAddOpinionForm()
    {
        return Mage::app()->getLayout()
            ->createBlock('recomendation/recomendation')
            ->setTemplate('tim/recomendation/add.phtml')
            ->toHtml();
    }
}
