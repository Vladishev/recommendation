<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vlad Verbitskiy <vladmsu@ukr.net>
 */
class Tim_Recommendation_Model_Resource_Malpractice_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize a collection, set model for it
     */
    protected function _construct()
    {
        $this->_init('tim_recommendation/malpractice');
    }
}