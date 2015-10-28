<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Model_UserLevel extends Mage_Core_Model_Abstract
{
    /**
     * Initialize recommendation user model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_recommendation/userLevel');
    }
}