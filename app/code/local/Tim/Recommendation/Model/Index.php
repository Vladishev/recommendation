<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vlad Verbitskiy <vladmsu@ukr.net>
 */
class Tim_Recommendation_Model_Index extends Mage_Core_Model_Abstract
{
    /**
     * Returns recom_id list of opinions
     * @param $productId
     * @param $order
     * @param $field
     * @param int $limit - opinions count per page
     * @param int $curPage - current page
     * @return bool or array
     */
    public function getOpinionsForProduct($productId, $limit = 10, $curPage = 1, $order = 'DESC', $field = 'date_add')
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('product_id', $productId);
        $collection->addFieldToFilter('acceptance', 1);
        $collection->getSelect()->where('parent IS NULL');
        $collection->addFieldToSelect('recom_id');
        $collection->setOrder($field, $order);
        $collection->setPageSize($limit); // It can be use for pagination
        $collection->setCurPage($curPage); // It can be use for pagination
        $data = $collection->getData();
        if (empty($data)) {
            return false;
        }
        return $data;
    }
}