<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Rating
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Block_Rating extends Mage_Core_Block_Template
{
    /**
     * Returns product's opinions data
     *
     * @param int $productId Native Magento product ID
     * @return array|mixed
     */
    public function getProductOpinionInfo($productId)
    {
        $productData = Mage::getModel('tim_recommendation/rating')->getOpinionsInfo($productId);
        return $productData;
    }
}