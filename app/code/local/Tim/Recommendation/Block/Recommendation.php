<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Recommendation extends Mage_Core_Block_Template
{
    /**
     * Prepare array with user information
     * @param int $customerId
     * @return array
     */
    public function getUserData($customerId)
    {
        $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $userData = $user->getData();
        $userData['user_type_name'] = Mage::helper('tim_recommendation')->getUserTypeName($user['user_type']);
        $userData['customer_name'] = Mage::helper('tim_recommendation')->getCustomerName($customerId);
        $userData['opinion_qty'] = Mage::helper('tim_recommendation')->getOpinionQty($customerId);

        return $userData;
    }

    /**
     * Gets host name by ip
     * @param string $ip
     * @return string
     */
    public function getHost($ip)
    {
        if (strstr($ip, ', ')) {
            $ips = explode(', ', $ip);
            $ip = $ips[0];
        }
        try {
            $hostName = gethostbyaddr($ip);
        } catch (Exception $e) {
            $hostName = '';
        }

        return $hostName;
    }

    /**
     * Get array with opinion information
     * @param int $productId
     * @return array
     */
    public function getOpinionData($productId)
    {
        $lastAddedOpinion = $this->getLastAddedOpinion($productId);
        $opinionMedia = $this->getOpinionMediaPath($lastAddedOpinion['recom_id']);
        $lastAddedOpinion['date_add'] = date('d-m-Y', strtotime($lastAddedOpinion['date_add']));
        $lastAddedOpinion['media'] = $opinionMedia;
        $lastAddedOpinion['comments'] = $this->getOpinionComments($lastAddedOpinion['recom_id']);

        return $lastAddedOpinion;
    }

    /**
     * Get last added opinion by date
     * @param int $productId
     * @return array
     */
    public function getLastAddedOpinion($productId)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToFilter('product_id', $productId);
        $opinionCollection->getSelect()->where('parent IS NULL');
        $opinionCollection->setOrder('date_add', 'DESC');
        $lastAddedOpinion = $opinionCollection->getData()[0];

        return $lastAddedOpinion;
    }

    /**
     * Get last added opinion id
     * @param int $productId
     * @return int
     */
    public function getLastAddedOpinionId($productId)
    {
        $opinion = $this->getLastAddedOpinion($productId);
        $opinionId = $opinion['recom_id'];
        return $opinionId;
    }

    /**
     * Get paths to opinion media files
     * @param int $opinionId
     * @return array
     */
    public function getOpinionMediaPath($opinionId)
    {
        $collection = Mage::getModel('tim_recommendation/media')->getCollection();
        $collection->addFieldToFilter('recom_id', $opinionId);
        $data = $collection->getData();
        $mediaPaths = array();
        foreach ($data as $item) {
            if ($item['type'] == 'url/youtube') {
                $mediaPaths['url/youtube'] = $item['name'];
                continue;
            }
            $mediaPaths[] .= $item['name'];
        }

        return $mediaPaths;
    }

    /**
     * Get comments to opinion
     * @param int $opinionId
     * @return array
     */
    public function getOpinionComments($opinionId)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('parent', $opinionId);
        $collection->getSelect()->where('parent IS NOT NULL');
        $collection->setOrder('date_add', 'DESC');
        $data = $collection->getData();
        $comments = array();
        foreach ($data as $comment) {
            $comments[] = array(
                'name' => Mage::helper('tim_recommendation')->getCustomerName($comment['user_id']),
                'comment' => $comment['comment'],
                'date_add' => $comment['date_add'],
            );
        }

        return $comments;
    }

    /**
     * Calculates the average rating of the last added opinion to the product
     * @param int $productId
     * @return float
     */
    public function getProductEvaluation($productId)
    {
        $data = $this->getLastAddedOpinion($productId);
        $ratings = array();
        $ratings[] = $data['rating_price'];
        $ratings[] = $data['rating_durability'];
        $ratings[] = $data['rating_failure'];
        $ratings[] = $data['rating_service'];
        $evaluation = round(array_sum($ratings) / count($ratings), 1);

        return $evaluation;
    }

    /**
     * Checks if opinion exist
     * @param int $productId
     * @return bool
     */
    public function isOpinionExist($productId)
    {
        $productOpinion = Mage::getModel('tim_recommendation/recommendation')->load($productId, 'product_id');
        $opinionData = $productOpinion->getData();
        if (empty($opinionData)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Gets custom opinion data
     * @return array
     */
    public function getProductOpinionData()
    {
        $productCollection = Mage::getModel('catalog/product');
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToSelect('product_id');
        $opinionCollection->getSelect()->where('parent IS NULL');
        $opinionCollection->setOrder('date_add', 'DESC');
        $productDataId = $opinionCollection->getData();
        $productData = array();


        $productsId = $this->_getUniqueArray($productDataId);

        $i = 0;
        foreach($productsId as $key=>$value)
        {
            $productData[$i]['name'] = $productCollection->load($value)->getName();
            $productData[$i]['image'] = $productCollection->load($value)->getImageUrl();
            $productData[$i]['average'] = $this->getAverage($value);
            $i++;
        }

        return $productData;
    }

    /**
     * Calculate average from all product rating
     * @param (int)$prodId
     * @return float|int
     */
    public function getAverage($prodId)
    {
        $ratingFields = array('rating_price','rating_durability','rating_failure','rating_service');
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->getSelect()->where('parent IS NULL');
        $opinions = $opinionCollection->addFieldToFilter('product_id', $prodId)->getData();
        $opinionCount = count($opinions);
        $rating = 0;

        foreach($opinions as $opinion)
        {
            foreach($ratingFields as $field)
            {
                $rating += $opinion[$field]/4;
            }
        }
        $rating = round(($rating/$opinionCount),1);

        return $rating;
    }

    /**
     * Gets information of user who writes opinions
     * and sort it by summ of opinions.
     * @return array
     */
    public function getUserSummaryInformation()
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToSelect('user_id');
        $opinionCollection->getSelect()->where('parent IS NULL');
        $usersId = $opinionCollection->getData();
        $singleId = $this->_getUniqueArray($usersId);
        $userData = array();

        foreach($singleId as $key=>$value)
        {
            $usersIdRating[$value] = Mage::helper('tim_recommendation')->getOpinionQty($value);
        }
        arsort($usersIdRating);

        $i = 0;
        foreach($usersIdRating as $key=>$value)
        {
            $userData[$i]['rating'] = $value;

            $collection = Mage::getModel('tim_recommendation/user')->getCollection();
            $collection->addFieldToFilter('customer_id', $key);
            $data = $collection->getData();
            $userData[$i]['avatar'] = Mage::getBaseUrl().$data[0]['avatar'];

            $userData[$i]['name'] = Mage::helper('tim_recommendation')->getCustomerName($key);

            $i++;
        }
        return $userData;
    }

    /**
     * return array with unique values
     * @param (obj, arr)$data
     * @return array
     */
    private function _getUniqueArray($data)
    {
        foreach($data as $item)
        {
            foreach($item as $key=>$value) {
                $arrayId[] = $value;
            }
        }
        $arrayId = array_unique($arrayId);

        return $arrayId;
    }
}