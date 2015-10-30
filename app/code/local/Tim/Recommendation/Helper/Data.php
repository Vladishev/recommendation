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
    /**
     * @return array
     */
    public function getNonAdminUserTypes()
    {
        $collection = Mage::getModel('tim_recommendation/userType')->getCollection();
        $collection->addFieldToFilter('admin', 0);
        $data = $collection->getData();

        return $data;
    }

    /**
     * @param string|int $customerId
     * @return string
     */
    public function getCustomerUserTypeId($customerId)
    {
        $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $userTypeId = $user->getUserType();

        return $userTypeId;
    }

    public function getRecommendationDataUrl()
    {
        $url = "recommendation/user/data";
        return $url;
    }

    public function getSiteUrl()
    {
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getWww();
        return $siteUrl;
    }

    public function getCustomerDescription()
    {
        $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getDescription();
        return $siteUrl;
    }

    /**
     * Concatinates customer first name and last name
     * @param int $customerId
     * @return string
     */
    public function getCustomerName($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $name = $customer->getFirstname() . ' ' . $customer->getLastname();

        return $name;
    }

    /**
     * Get name of user type
     * @param int $userTypeId
     * @return string
     */
    public function getUserTypeName($userTypeId)
    {
        $userType = Mage::getModel('tim_recommendation/userType')->load($userTypeId, 'user_type_id');
        $userTypeName = $userType->getName();

        return $userTypeName;
    }

    /**
     * Counts quantity of opinions
     * @param int|bool $customerId
     * @return int
     */
    public function getOpinionQty($customerId = false)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->getSelect()->where('parent IS NULL');
        if ($customerId) {
            $opinionCollection->addFieldToFilter('user_id', $customerId);
        }
        $qty = count($opinionCollection->getData());

        return $qty;
    }

    /**
     * Compare values from system configuration (tim_recommendation/user_type/values)
     * and tim_recommendation/userType collection
     * @param array $userTypes
     * @return array
     */
    public function getUserTypeDiffIds($userTypes)
    {
        $formIds = array_keys($userTypes);
        $collectionData = Mage::getModel('tim_recommendation/userType')->getCollection()->getData();
        $configIds = array();
        foreach ($collectionData as $data) {
            $configIds[] .= $data['system_config_id'];
        }
        $diff = array_diff($configIds, $formIds);

        return $diff;
    }

    /**
     * Compare values from system configuration (tim_recommendation/user_level/level_values)
     * and tim_recommendation/userLevel collection
     * @param array $userLevel
     * @return array
     */
    public function getUserLevelDiffIds($userLevel)
    {
        $formIds = array_keys($userLevel);
        $collectionData = Mage::getModel('tim_recommendation/userLevel')->getCollection()->getData();
        $configIds = array();
        foreach ($collectionData as $data) {
            $configIds[] .= $data['system_config_id'];
        }
        $diff = array_diff($configIds, $formIds);

        return $diff;
    }

    /**
     * Get salt from system configuration
     * @return string
     */
    public function getSalt()
    {
        $salt = Mage::getStoreConfig('tim_salt/salt/value');
        return $salt;
    }
}
