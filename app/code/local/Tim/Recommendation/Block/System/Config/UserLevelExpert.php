<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Block_System_Config_UserLevelExpert extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('point', array(
            'label' => Mage::helper('tim_recommendation')->__('The number of stamps'),
            'class' => 'validate-not-negative-number required-entry',
            'style' => 'width:100px',
        ));

        $this->addColumn('email_addresses', array(
            'label' => Mage::helper('tim_recommendation')->__('E-mail addresses'),
            'class' => 'required-entry',
            'style' => 'width:320px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('tim_recommendation')->__('Add a new level');
    }
}