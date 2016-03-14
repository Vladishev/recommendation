<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Model_Methods
{
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label' => Mage::helper('tim_recommendation')->__('Cc')),
            array('value' => 2, 'label' => Mage::helper('tim_recommendation')->__('Bcc')),
        );
    }
}