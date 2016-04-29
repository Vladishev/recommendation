<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_CommentsToOpinion
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_CommentsToOpinion extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    /**
     * Init grid container
     * TODO remove this grid till 01/02/2016. Reason: useless. The same functionality in grid @see Tim_Recommendation_Block_Adminhtml_CommentsReport_Grid
     */
    public function __construct()
    {
        $this->_blockGroup = 'tim_recommendation';
        $this->_controller = 'adminhtml_commentsToOpinion';
        $this->_headerText = Mage::helper('tim_recommendation')->__('All comments to opinion');

        parent::__construct();
        $this->_removeButton('add');
    }
}