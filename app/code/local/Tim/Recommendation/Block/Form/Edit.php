<?php

class Tim_Recommendation_Block_Form_Edit extends Mage_Customer_Block_Form_Edit
{
    /**
     * Return user type ID
     *
     * @param string|int $customerId Native Magento customer ID
     * @return string
     */
    public function getCustomerUserTypeId($customerId)
    {
        return Mage::getModel('tim_recommendation/user')->getCustomerUserTypeId($customerId);
    }

    /**
     * Returns user nick
     *
     * If passed $customerId - returns nick for passed id
     * If $customerId not passed - returns current customer nick
     *
     * @param null $customerId Native Magento customer ID
     * @return string
     */
    public function getUserNick($customerId = null)
    {
        return Mage::getModel('tim_recommendation/user')->getUserNick($customerId);
    }

    /**
     * Returns user banner
     *
     * @return bool|string
     */
    public function getCustomerBanner()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $banner = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getAd();
        if (!empty($banner)) {
            $banner = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $banner;
            return $banner;
        } else {
            return false;
        }
    }

    /**
     * Returns user avatar
     *
     * @return bool|string
     */
    public function getCustomerAvatar()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $avatar = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getAvatar();
        if (!empty($avatar)) {
            $avatar = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $avatar;
            return $avatar;
        } else {
            return false;
        }
    }

    /**
     * Returns user description
     *
     * @return mixed
     */
    public function getCustomerDescription()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getDescription();
        return $siteUrl;
    }
}