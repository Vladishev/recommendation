<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Model_Resource_UserLevel extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Initialize a resource model, set main table and ID field name
     */
    protected function _construct()
    {
        $this->_init('tim_recommendation/level', 'user_level_id');
    }
}