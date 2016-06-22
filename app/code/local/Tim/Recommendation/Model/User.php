<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Model_User
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Model_User extends Mage_Core_Model_Abstract
{
    /**
     * Initialize recommendation user model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_recommendation/user');
    }

    /**
     * Returns customer nick
     *
     * @param int|null $customerId
     * @return string
     */
    public function getUserNick($customerId = null)
    {
        if (empty($customerId)) {
            $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
            $nick = $this->load((int)$customerId, 'customer_id')->getNick();
        } else {
            $nick = $this->load((int)$customerId, 'customer_id')->getNick();
        }
        return $nick;
    }

    /**
     * Return user type ID
     *
     * @param string|int $customerId Native Magento customer ID
     * @return string
     */
    public function getCustomerUserTypeId($customerId)
    {
        $user = $this->load((int)$customerId, 'customer_id');
        $userTypeId = $user->getUserType();

        return $userTypeId;
    }

    /**
     * Returns user site url
     *
     * @return string
     */
    public function getSiteUrl()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = $this->load($customerId, 'customer_id')->getWww();
        return $siteUrl;
    }
}