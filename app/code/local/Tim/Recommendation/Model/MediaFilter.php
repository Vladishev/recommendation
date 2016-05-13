<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

class Tim_Recommendation_Model_MediaFilter extends Mage_Core_Model_Abstract
{
    /**
     * Filter collection by media
     *
     * @param $collection Tim_Recommendation_Model_Resource_Recommendation_Collection
     * @param $column Mage_Adminhtml_Block_Widget_Grid_Column
     * @return mixed
     */
    public function filterMedia($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            if ($value == 'Yes') {
                $collection->getSelect()->joinInner(array('trm' => 'tim_recom_media'), 'main_table.recom_id= trm.recom_id',
                    array('media_recom_id' => 'recom_id'));
                $collection->getSelect()->group('trm.recom_id');
            }
            if ($value == 'No') {
                $collection->getSelect()->joinLeft(array('trm' => 'tim_recom_media'), 'main_table.recom_id = trm.recom_id',
                    array('media_recom_id' => 'recom_id'));
                $collection->getSelect()->where('trm.recom_id IS NULL');
            }
        }

        return $collection;
    }
}