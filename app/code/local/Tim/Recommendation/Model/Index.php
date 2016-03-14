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
        $collection->addFieldToSelect('tim_ip');
        $collection->addFieldToSelect('tim_host');
        $collection->addFieldToSelect('user_id');
        $collection->setOrder($field, $order);
        $collection->setPageSize($limit);
        $collection->setCurPage($curPage);
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

    /**
     * Returns user's comments
     * @param $userId
     * @param $limit
     * @param $curPage
     * @param $order
     * @return mixed
     */
    public function getOpinionComment($userId, $limit, $curPage, $order)
    {
        $recommendationCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $recommendationCollection->addFieldToFilter('acceptance', 1);
        $recommendationCollection->addFieldToFilter('user_id', $userId);
        $recommendationCollection->addFieldToFilter('parent', array('neq' => 'NULL'));
        $recommendationCollection->addFieldToSelect('comment');
        $recommendationCollection->addFieldToSelect('date_add');
        $recommendationCollection->addFieldToSelect('product_id');
        $recommendationCollection->setOrder('date_add', $order);
        $recommendationCollection->setPageSize($limit);
        $recommendationCollection->setCurPage($curPage);
        $comments = $recommendationCollection->getData();
        $dateModel = Mage::getModel('core/date');

        $i = 0;
        foreach ($comments as $comment) {
            $productId = $comment['product_id'];
            $product = Mage::getModel('catalog/product')->load($productId);
            $comments[$i]['name'] = $product->getName();
            $comments[$i]['url'] = $product->getProductUrl();
            $comments[$i]['date_add'] = date('Y-m-d H:i:s', $dateModel->timestamp($comment['date_add']));
            $i++;
        }

        $count = $this->getCommentsCount($userId);
        $comments[0]['pagesCount'] = ceil($count / $limit);
        $comments[0]['curPage'] = $curPage;

        return $comments;
    }

    /**
     * Evaluates comments count for particular user
     * @param $userId
     * @return int
     */
    public function getCommentsCount($userId)
    {
        $recommendationCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $recommendationCollection->addFieldToFilter('acceptance', 1);
        $recommendationCollection->addFieldToFilter('user_id', $userId);
        $recommendationCollection->addFieldToFilter('parent', array('neq' => 'NULL'));
        $count = count($recommendationCollection->getData());

        return $count;
    }

    /**
     * Returns count of accepted opinions for product
     * @param $productId
     * @return int
     */
    public function getOpinionCount($productId)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('product_id', $productId);
        $collection->addFieldToFilter('acceptance', 1);
        $collection->getSelect()->where('parent IS NULL');
        $collection->addFieldToSelect('recom_id');
        $count = count($collection->getData());
        return $count;
    }
}