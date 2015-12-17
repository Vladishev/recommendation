<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @copyright Copyright (c) 2015 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_DateFormat extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $date = $row->getData($this->getColumn()->getIndex());
        $formatDate = date('d.m.Y H:i', strtotime($date));
        return $formatDate;
    }
}