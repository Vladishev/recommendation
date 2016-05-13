<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_Render_DisplayNote
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_DisplayNote extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Renders grid column
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $return = '';
        if ($row->getMalpracticeId()) {
            $id = (int) $row->getMalpracticeId();
            $note = $this->getMalpracticeNote($id);
            $url = Mage::helper("adminhtml")->getUrl("adminhtml/noteReport", array('malpracticeId' => $id));
        } else {
            $id = (int) $row->getRecomId();
            $note = $this->getRecommendationNote($id);
            $url = Mage::helper("adminhtml")->getUrl("adminhtml/noteReport", array('recomId' => $id));
        }
        $title = Mage::helper('tim_recommendation')->__('Display note');

        if (!empty($note)) {
            $return .= $note;
            $return .= '</br>';
        }
        $return .= <<<HTML
<a href="{$url}">{$title}</a>
HTML;

        return $return;
    }

    /**
     * Gets last added note to recommendation
     * @param $recomId
     * @return mixed
     */
    private function getRecommendationNote($recomId)
    {
        $note = Mage::getModel('tim_recommendation/note')->getCollection()
            ->addFieldToFilter('object_id', array('eq' => $recomId))
            ->addFieldToFilter('object_name', array('eq' => 'tim_recommendation'))
            ->setOrder('date_add', 'ASC')
            ->getLastItem()
            ->getNote();
        return $note;
    }

    /**
     * Gets last added note to malpractice
     * @param $malpracticeId
     * @return mixed
     */
    private function getMalpracticeNote($malpracticeId)
    {
        $note = Mage::getModel('tim_recommendation/note')->getCollection()
            ->addFieldToFilter('object_id', array('eq' => $malpracticeId))
            ->addFieldToFilter('object_name', array('eq' => 'tim_recom_malpractice'))
            ->setOrder('date_add', 'ASC')
            ->getLastItem()
            ->getNote();
        return $note;
    }
}