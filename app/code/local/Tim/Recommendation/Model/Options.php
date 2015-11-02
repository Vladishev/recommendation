<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Model_Options
{
    public function toOptionArray()
    {
        return array(
            array('value'=>1, 'label'=>Mage::helper('tim_recommendation')->__('Yes')),
            array('value'=>0, 'label'=>Mage::helper('tim_recommendation')->__('No')),
        );
    }
}