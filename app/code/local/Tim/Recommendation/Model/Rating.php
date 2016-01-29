<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Model_Rating extends Mage_Core_Model_Abstract
{
    /**
     * Returns opinions info for product
     * @param (int)$productId
     * @return array
     */
    public function getOpinionsInfo($productId)
    {
        $productData = array();
        $productData['opinions_count'] = $this->_getOpinionCount($productId);
        $productData['rating'] = $this->_getProductRating($productId);
        return $productData;
    }

    /**
     * Returns count of opinions for product
     * @param (int)$productId
     * @return int
     */
    protected function _getOpinionCount($productId)
    {
        $opinionCollection = $this->_getOpinionCollection($productId);
        $opinionCollection->addFieldToSelect('recom_id');
        $count = count($opinionCollection->getData());
        return $count;
    }

    /**
     * Returns average product rating based on product's opinions
     * @param (int)$productId
     * @return float))
     */
    protected function _getProductRating($productId)
    {
        $opinionCollection = $this->_getOpinionCollection($productId);
        $opinionCount = $this->_getOpinionCount($productId);
        $average = 0;
        foreach ($opinionCollection as $row) {
            $ratings = array();
            $ratings[] = $row->getData('rating_price');
            $ratings[] = $row->getData('rating_durability');
            $ratings[] = $row->getData('rating_failure');
            $ratings[] = $row->getData('rating_service');
            $average += round(array_sum($ratings) / count($ratings), 1);
        }
        $rating = round($average / $opinionCount, 1);
        return $rating;
    }

    /**
     * Returns right collection
     * @param (int)$productId
     * @return object
     */
    protected function _getOpinionCollection($productId)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToFilter('product_id', $productId);
        $opinionCollection->addFieldToFilter('acceptance', 1);
        $opinionCollection->addFieldToFilter('parent', array('null' => true));
        $opinionCollection->addFieldToSelect(array(
            'rating_price',
            'rating_durability',
            'rating_failure',
            'rating_service',
            'recom_id'
        ));
        return $opinionCollection;
    }
}