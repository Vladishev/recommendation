<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Block_Adminhtml_System_Config_Form_Field_ImportButton extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('tim/recommendation/system/config/form/field/import_button.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element element
     *
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Generate Import button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        /* @var $buttonBlock Mage_Adminhtml_Block_Widget_Button */
        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id'        => 'import_button',
                'label'     => $this->helper('tim_recommendation')->__('Import'),
                'onclick'   => 'javascript:importFiles(); return false;'
            ));

        return $buttonBlock->toHtml();
    }

    /**
     * Return ajax url for import button
     *
     * @return string
     */
    public function getAjaxImportUrl()
    {
        return Mage::getSingleton('adminhtml/url')->getUrl('tim_recommendation_import/adminhtml_import/import');
    }
}