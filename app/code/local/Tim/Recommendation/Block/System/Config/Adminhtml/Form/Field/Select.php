<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_System_Config_Adminhtml_Form_Field_Select
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_System_Config_Adminhtml_Form_Field_Select extends Mage_Core_Block_Html_Select
{
    /**
     * Render HTML
     *
     * @return string
     */
    public function _toHtml()
    {
        $options = Mage::getSingleton('adminhtml/system_config_source_yesno')
            ->toOptionArray();
        foreach (array_reverse($options) as $option) {
            $this->addOption($option['value'], $option['label']);
        }

        return parent::_toHtml();
    }

    /**
     * @param mixed $value
     * @return mixed
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }
}
