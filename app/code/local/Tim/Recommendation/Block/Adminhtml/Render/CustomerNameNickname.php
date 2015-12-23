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
        $customerId = $row->getData($this->getColumn()->getIndex());
        $customerNickname = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getNick();
        $customer = Mage::getModel('customer/customer')->load($customerId);
        $name = $customer->getFirstname() . ' ' . $customer->getLastname() . ' (' . $customerNickname . ')';

        return $name;
    }
}