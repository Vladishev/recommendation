<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Block_System_Config_UserLevelClient extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('point', array(
            'label' => Mage::helper('tim_recommendation')->__('The number of stamps'),
            'class' => 'validate-greater-than-zero required-entry',
            'style' => 'width:100px',
        ));

        $this->addColumn('from', array(
            'label' => Mage::helper('tim_recommendation')->__('From'),
            'class' => 'validate-not-negative-number required-entry',
            'style' => 'width:100px',
        ));

        $this->addColumn('to', array(
            'label' => Mage::helper('tim_recommendation')->__('To'),
            'class' => 'validate-not-negative-number required-entry',
            'style' => 'width:100px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('tim_recommendation')->__('Add a new level');
    }
}