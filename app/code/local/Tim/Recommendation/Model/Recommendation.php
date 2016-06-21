<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Model_Recommendation
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Model_Recommendation extends Mage_Core_Model_Abstract
{
    /**
     * Initialize recommendation model, set resource model for it
     */
    public function _construct()
    {
        parent::_construct();
        $this->_init('tim_recommendation/recommendation');
    }

    /**
     * Gets md5 hash from tim_recommendation table
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @return string
     */
    public function getRecommendationMd5($recomId)
    {
        $object = $this->load((int)$recomId);
        $md5hash = $object->getMd5();

        return $md5hash;
    }

}