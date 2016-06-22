<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Model_Rating
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Model_Rating extends Mage_Core_Model_Abstract
{
    /**
     * Value for acceptance yes
     */
    const ACCEPTANCE_YES = 1;

    /**
     * Returns opinions info for product
     *
     * @param int $productId Native Magento product ID
     * @return array
     */
    public function getOpinionsInfo($productId)
    {
        $productData = array();
        $opinionCollection = $this->_getOpinionCollection((int) $productId);
        $productData['opinions_count'] = $this->_getOpinionCount($opinionCollection);
        $productData['rating'] = $this->_getProductRating($opinionCollection);
        return $productData;
    }

    /**
     * Returns count of opinions for product
     *
     * @param  Mage_Core_Model_Resource_Db_Collection_Abstract $opinionCollection Tim_Recommendation_Model_Resource_Recommendation_Collection
     * @return int
     */
    protected function _getOpinionCount(Mage_Core_Model_Resource_Db_Collection_Abstract $opinionCollection)
    {
        $opinionCollection->addFieldToSelect('recom_id');
        $count = count($opinionCollection->getData());
        return $count;
    }

    /**
     * Returns average product rating based on product's opinions
     *
     * @param Mage_Core_Model_Resource_Db_Collection_Abstract $opinionCollection Tim_Recommendation_Model_Resource_Recommendation_Collection
     * @return float
     */
    protected function _getProductRating(Mage_Core_Model_Resource_Db_Collection_Abstract $opinionCollection)
    {
        $opinionCount = $this->_getOpinionCount($opinionCollection);
        $average = 0;
        $rating = 0;
        foreach ($opinionCollection as $row) {
            $ratings = array();
            $ratings[] = $row->getData('rating_price');
            $ratings[] = $row->getData('rating_durability');
            $ratings[] = $row->getData('rating_failure');
            $ratings[] = $row->getData('rating_service');
            $average += round(array_sum($ratings) / count($ratings), 1);
        }
        if ((bool) $opinionCount !== false) {
            $rating = round($average / $opinionCount, 1);
        }

        return $rating;
    }

    /**
     * Returns right collection
     *
     * @param  int $productId Native Magento product ID
     * @return Tim_Recommendation_Model_Resource_Recommendation_Collection
     */
    protected function _getOpinionCollection($productId)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToFilter('product_id', (int) $productId);
        $opinionCollection->addFieldToFilter('acceptance', self::ACCEPTANCE_YES);
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