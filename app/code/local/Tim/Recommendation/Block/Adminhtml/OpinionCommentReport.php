<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Block_Adminhtml_OpinionCommentReport extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init grid container
     */
    public function __construct()
    {
        $this->_blockGroup = 'tim_recommendation';
        $this->_controller = 'adminhtml_opinionCommentReport';
        $this->_headerText = Mage::helper('tim_recommendation')->__('Opinion and comment');

        parent::__construct();
        $this->_removeButton('add');
    }
}