<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_System_Config_UserType
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_System_Config_UserType extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    /**
     * @var
     */
    protected $_itemRenderer;

    /**
     * Prepare to render
     */
    public function _prepareToRender()
    {
        $this->addColumn('user_type', array(
            'label' => Mage::helper('tim_recommendation')->__('User type'),
            'class' => 'required-entry'
        ));

        $this->addColumn('administrator', array(
            'label' => Mage::helper('tim_recommendation')->__('Administrator'),
            'renderer' => $this->_getRenderer(),
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('tim_recommendation')->__('Add new type');
    }

    /**
     * Get renderer
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _getRenderer()
    {
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                'tim_recommendation/system_config_adminhtml_form_field_select', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;
    }

    /**
     * Prepare existing row data object
     *
     * @param Varien_Object
     */
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer()
                ->calcOptionHash($row->getData('administrator')),
            'selected="selected"'
        );
    }
}