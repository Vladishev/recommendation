<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_Render_RenderCommentOpinion
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Vladislav Verbitskiy (vladomsu@gmail.com)
 */
class Tim_Recommendation_Block_Adminhtml_Render_RenderCommentOpinion extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * Accept recom_id and check what is it: comment or opinion
     * If it's opinion - returns data from 'advantages' field and recom_id
     * If it's comment - returns data from 'comment' field and recom_id
     *
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row)
    {
        $recomId = $row->getRecomId();
        $recomRow = Mage::getModel('tim_recommendation/recommendation')->load($recomId, 'recom_id');
        if ($advantages = $recomRow->getAdvantages()) {
            if (strlen($advantages) > 100) {
                $string = '<p align="center"><b>' . $recomId . '</b></p>' . substr($advantages, 0, 99) . '...';
            } else {
                $string = '<p align="center"><b>' . $recomId . '</b></p>' . $advantages;
            }
        } else {
            $comment = $recomRow->getComment();
            if (strlen($comment) > 100) {
                $string = '<p align="center"><b>' . $recomId . '</b></p>' . substr($comment, 0, 99) . '...';
            } else {
                $string = '<p align="center"><b>' . $recomId . '</b></p>' . $comment;
            }
        }
        return $string;
    }
}