<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_MalpracticeReport
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy (vladomsu@gmail.com)
 */
class Tim_Recommendation_Block_Adminhtml_MalpracticeReport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        $this->_blockGroup = 'tim_recommendation';
        $this->_controller = 'adminhtml_malpracticeReport';
        $this->_headerText = Mage::helper('tim_recommendation')->__('Abuse');

        parent::__construct();
        $this->_removeButton('add');
    }
}