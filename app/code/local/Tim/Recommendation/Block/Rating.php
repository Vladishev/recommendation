<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Block_Rating extends Mage_Core_Block_Template
{
    /**
     * Returns product's opinions data
     * @param (int)$productId
     * @return (arr)mixed
     */
    public function getProductOpinionInfo($productId)
    {
        $productData = Mage::getModel('tim_recommendation/rating')->getOpinionsInfo($productId);
        return $productData;
    }

    /**
     * Returns current product id
     *
     * @return mixed
     */
    public function getCurrentProductId()
    {
        return Mage::registry('current_product')->getId();
    }
}