<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Model_UserType
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Model_UserType extends Mage_Core_Model_Abstract
{
    /**
     * Value for filter not admin user
     */
    const NOT_ADMIN = 0;

    /**
     * Initialize recommendation user model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_recommendation/userType');
    }

    /**
     * Return all user types except admin type
     *
     * @return object Tim_Recommendation_Model_UserType
     */
    public function getNonAdminUserTypes()
    {
        $collection = $this->getCollection();
        $collection->addFieldToFilter('admin', self::NOT_ADMIN);

        return $collection;
    }
}