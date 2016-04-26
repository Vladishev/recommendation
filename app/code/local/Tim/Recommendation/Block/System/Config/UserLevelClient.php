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
    protected $_itemModerationRenderer;
    protected $_itemUpdateRenderer;

    /**
     * Prepare to render
     */
    public function _prepareToRender()
    {
        $this->addColumn('point', array(
            'label' => Mage::helper('tim_recommendation')->__('The number of stamps'),
            'class' => 'validate-greater-than-zero required-entry',
            'style' => 'width:50px',
        ));

        $this->addColumn('from', array(
            'label' => Mage::helper('tim_recommendation')->__('From'),
            'class' => 'validate-not-negative-number required-entry',
            'style' => 'width:50px',
        ));

        $this->addColumn('to', array(
            'label' => Mage::helper('tim_recommendation')->__('To'),
            'class' => 'validate-not-negative-number required-entry',
            'style' => 'width:50px',
        ));

        $this->addColumn('moderation', array(
            'label' => Mage::helper('tim_recommendation')->__('Without moderation'),
            'style' => 'width:50px',
            'renderer' => $this->_getModerationRenderer(),
        ));

        $this->addColumn('update_present_visit_card', array(
            'label' => Mage::helper('tim_recommendation')->__('Can update and present visit card'),
            'style' => 'width:50px',
            'renderer' => $this->_getUpdateRenderer(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('tim_recommendation')->__('Add a new level');
    }

    /**
     * Prepare moderation renderer
     * @return Tim_Recommendation_Block_System_Config_Adminhtml_Form_Field_Select
     */
    protected function _getModerationRenderer()
    {
        if (!$this->_itemModerationRenderer) {
            $this->_itemModerationRenderer = $this->getLayout()->createBlock(
                'tim_recommendation/system_config_adminhtml_form_field_select', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemModerationRenderer;
    }
    /**
     * Prepare update renderer
     * @return Tim_Recommendation_Block_System_Config_Adminhtml_Form_Field_Select
     */
    protected function _getUpdateRenderer()
    {
        if (!$this->_itemUpdateRenderer) {
            $this->_itemUpdateRenderer = $this->getLayout()->createBlock(
                'tim_recommendation/system_config_adminhtml_form_field_select', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemUpdateRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getModerationRenderer()
                ->calcOptionHash($row->getData('moderation')),
            'selected="selected"'
        );
        $row->setData(
            'option_extra_attr_' . $this->_getUpdateRenderer()
                ->calcOptionHash($row->getData('update_present_visit_card')),
            'selected="selected"'
        );
    }
}