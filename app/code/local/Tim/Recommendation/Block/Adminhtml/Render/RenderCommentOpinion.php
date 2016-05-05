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
     * Count of characters which can be shown
     */
    const LENGTH_COUNT = 100;

    /**
     * Accept recom_id and check what is it: comment or opinion.
     * If it's opinion - returns data from 'advantages' field and recom_id.
     * If it's comment - returns data from 'comment' field and recom_id.
     * @param Varien_Object $row
     * @return mixed
     */
    public function render(Varien_Object $row)
    {
        $recomId = $row->getRecomId();
        $recomRow = Mage::getModel('tim_recommendation/recommendation')
            ->getCollection()
            ->addFieldToFilter('recom_id', array( array('eq' => $recomId)))
            ->addFieldToSelect(array('advantages', 'comment'))
            ->getFirstItem();
        if ($advantages = $recomRow->getAdvantages()) {
            $string = $this->_getString($advantages, $recomId);
        } else {
            $comment = $recomRow->getComment();
            $string = $this->_getString($comment, $recomId);
        }
        return $string;
    }

    /**
     * Returns formed html string
     *
     * @param string $content
     * @param int $recomId Id from tim_recommendation table(recom_id)
     * @return string
     */
    protected function _getString($content, $recomId)
    {
        if (strlen($content) > self::LENGTH_COUNT) {
            $string = '<p align="center"><b>' . $recomId . '</b></p>' . substr($content, 0, (self::LENGTH_COUNT - 1)) . '...';
        } else {
            $string = '<p align="center"><b>' . $recomId . '</b></p>' . $content;
        }

        return $string;
    }
}