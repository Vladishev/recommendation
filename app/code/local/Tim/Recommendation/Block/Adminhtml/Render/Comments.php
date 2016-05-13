<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_Render_Comments
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_Comments extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Count of characters which can be shown
     */
    const LENGTH_COUNT = 100;

    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $advantages = $row->getAdvantages();
        if (strlen($advantages) > self::LENGTH_COUNT) {
            $advantages = substr($advantages, 0, (self::LENGTH_COUNT - 1)) . '...';
        }

        return $advantages;
    }
}