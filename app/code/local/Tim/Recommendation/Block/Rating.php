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
     * Returns product url with scroll anchor
     * @param object $product
     * @return string
     */
    public function getProductUrlPath($product)
    {
        $anchor = '#tim-scroll-anchor';
        $productUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $product->getRequestPath() . DS . $anchor;
        return $productUrl;
    }

    /**
     * Returns product url with add opinion anchor
     * @param object $product
     * @return string
     */
    public function getAddOpinionUrl($product)
    {
        $anchor = '#add-opinion';
        $productUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $product->getRequestPath() . DS . $anchor;
        return $productUrl;
    }
}