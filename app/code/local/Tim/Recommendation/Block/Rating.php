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
        $productData = Mage::getModel('tim_recommendation/rating')->getOpinionsInfo((int) $productId);
        return $productData;
    }

    /**
     * Returns product url with scroll anchor
     * @param object $product
     * @return string
     */
    public function getProductUrlPath($product)
    {
        $anchor = '#tim-general-add-opinion-button';
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