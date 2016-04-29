<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @copyright Copyright (c) 2015 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_CustomerNameNickname extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $customerId =(int) $row->getData($this->getColumn()->getIndex());
        if (empty($customerId)) {
            $name = $row->getEmail();
            $name .= '<br>(' . Mage::helper('tim_recommendation')->__('Not logged') . ')';
        } else {
            $customerNickname = $row->getNick();
            $name = $row->getCustomerFirstname() . ' ' . $row->getCustomerLastname();
            if ($customerNickname) {
                $name .= ' (' . $customerNickname . ')';
            }
        }

        return $name;
    }
}