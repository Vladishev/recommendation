<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy (vladomsu@gmail.com)
 */
class Tim_Recommendation_Block_Adminhtml_Render_RenderCommentOpinion extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Accept recom_id and check what is it: comment or opinion.
     * If it's opinion - returns data from 'advantages' field.
     * If it's comment - returns data from 'comment' field.
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row)
    {
        $recomId = $row->getRecomId();
        $recomRow = Mage::getModel('tim_recommendation/recommendation')->load($recomId, 'recom_id');
        if ($advantages = $recomRow->getAdvantages()) {
            return $advantages;
        } else {
            $comment = $recomRow->getComment();
            return $comment;
        }
    }
}