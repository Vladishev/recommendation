<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getRecommendationDataUrl()
    {
        $url = "recommendation/user/data";
        return $url;
    }

    public function getPhotoData()
    {
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $photo = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getPhoto();
        return $photo;
    }

    public function getSiteUrl()
    {
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getSiteUrl();
        return $siteUrl;
    }
}
