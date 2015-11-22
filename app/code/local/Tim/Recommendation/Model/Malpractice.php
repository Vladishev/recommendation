<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vlad Verbitskiy <vladmsu@ukr.net>
 */
class Tim_Recommendation_Model_Malpractice extends Mage_Core_Model_Abstract
{
    /**
     * Initialize recommendation malpractice model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_recommendation/malpractice');
    }
}