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
     * Returns list of opinions
     * @param $productId
     * @param int $limit - opinions count per page
     * @param int $curPage - current page
     * @return bool or array
     */
    public function getOpinionsForProduct($productId, $limit = 10, $curPage = 1)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('product_id', $productId);
        $collection->addFieldToFilter('acceptance', 1);
        $collection->getSelect()->where('parent IS NULL');
        $collection->setOrder('date_add', 'DESC');
        $collection->setPageSize($limit); // It can be use for pagination
        $collection->setCurPage($curPage); // It can be use for pagination
        $data = $collection->getData();
        if (empty($data)) {
            return false;
        }
        return $data;
    }

    /**
     * Returns count of user's opinions
     * @param $userId
     * @return int
     */
    public function getUserOpinionCount($userId)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('user_id', $userId);
        $collection->addFieldToFilter('parent', array('null' => true));
        $collection->addFieldToFilter('acceptance', 1);
        $collection->addFieldToSelect('recom_id');
        $count = count($collection->getData());
        return $count;
    }

    /**
     * Returns custom opinion data
     * @param $userId
     * @param $limit
     * @param $curPage
     * @param $order
     * @param $field
     * @return array
     */
    public function getUserOpinionData($userId, $limit, $curPage, $order, $field)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToFilter('user_id', $userId);
        $opinionCollection->addFieldToFilter('parent', array('null' => true));
        $opinionCollection->addFieldToFilter('acceptance', 1);
        $opinionCollection->setOrder($field, $order);
        $opinionCollection->setPageSize($limit);
        $opinionCollection->setCurPage($curPage);
        $opinionData = $opinionCollection->getData();

        $userOpinionData = array();
        $i = 0;
        foreach ($opinionData as $item) {
            $productId = $item['product_id'];
            $product = Mage::getModel('catalog/product')->load($productId);
            $userOpinionData[$i]['image'] = $product->getImageUrl();
            $userOpinionData[$i]['url'] = $product->getProductUrl();
            $userOpinionData[$i]['name'] = $product->getName();
            $userOpinionData[$i]['recom_id'] = $item['recom_id'];
            $userOpinionData[$i]['rating'] = $item['average_rating'];

            $i++;
        }
        return $userOpinionData;
    }
}