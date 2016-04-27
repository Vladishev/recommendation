<?php

/**
 * Tim
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @copyright Copyright (c) 2015 Tim (http://tim.pl)
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Render_DisplayNote extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
    public function render(Varien_Object $row)
    {
        $return = '';
        $recomId = (int) $row->getRecomId();
        $url = Mage::helper("adminhtml")->getUrl("adminhtml/noteReport", array('id' => $recomId));
        $title = Mage::helper('tim_recommendation')->__('Display note');
        $note = $this->getNote($recomId);
        if (!empty($note)) {
            $return .= $note;
            $return .= '</br>';
        }
        $return .= <<<HTML
<a target="_blank" href="{$url}">{$title}</a>
HTML;
        return $return;
    }

    /**
     * Gets last added note to recommendation
     * @param $recomId
     * @return mixed
     */
    private function getNote($recomId)
    {
        $note = Mage::getModel('tim_recommendation/note')->getCollection()
            ->addFieldToFilter('object_id', array('eq' => $recomId))
            ->setOrder('date_add', 'ASC')
            ->getLastItem()
            ->getNote();
        return $note;
    }
}