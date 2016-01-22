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
        $userData['user_type_name'] = $this->getHelper()->getUserTypeName($user['user_type']);
        $userData['customer_name'] = $this->getHelper()->getCustomerNameOrNick($customerId);
        $userData['customer_nick'] = Mage::helper('tim_recommendation')->getUserNick($customerId);
        $userData['opinion_qty'] = $this->getHelper()->getOpinionQty($customerId);

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
     * @param int $recomId
     * @return array
     */
    public function getOpinionData($recomId)
    {
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($recomId, 'recom_id')->getData();
        $opinionMedia = $this->getHelper()->getOpinionMediaPath($recomId);
        $opinion['date_add'] = date('d-m-Y', strtotime($opinion['date_add']));
        if (!empty($opinionMedia['url/youtube'])) {
            $opinion['movie_url'] = $opinionMedia['url/youtube'];
        }
        $opinion['images'] = $this->getImages($opinion['recom_id']);
        $opinion['comments'] = $this->getOpinionComments($opinion['recom_id']);
        $opinion['name'] = $this->getHelper()->getCustomerNameOrNick($opinion['user_id']);

        return $opinion;
    }

    /**
     * Gets image path without url/youtube path
     * @param int $recomId
     * @return array
     */
    public function getImages($recomId)
    {
        $opinionMedia = $this->getHelper()->getOpinionMediaPath($recomId);
        $images = array();
        foreach ($opinionMedia as $key => $value) {
            if ($key === 'url/youtube') {
                continue;
            }
            $images[] .= $value;
        }
        return $images;
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
        $opinionCollection->addFieldToFilter('acceptance', 1);
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
     * Get comments to opinion
     * @param int $opinionId
     * @return array
     */
    public function getOpinionComments($opinionId)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('parent', $opinionId);
        $collection->addFieldToFilter('acceptance', 1);
        $collection->getSelect()->where('parent IS NOT NULL');
        $collection->setOrder('date_add', 'DESC');
        $data = $collection->getData();
        $comments = array();
        foreach ($data as $comment) {
            $comments[] = array(
                'name' => $this->getHelper()->getCustomerNameOrNick($comment['user_id']),
                'comment' => $comment['comment'],
                'date_add' => $comment['date_add'],
                'recom_id' => $comment['recom_id'],
            );
        }

        return $comments;
    }

    /**
     * Calculates the average rating of the last added opinion to the product
     * @param int $productId
     * @return float
     */
    public function getProductEvaluation($recomId)
    {
        $data = Mage::getModel('tim_recommendation/recommendation')->load($recomId, 'recom_id');
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
        $opinionCollectionForeach = Mage::getModel('tim_recommendation/recommendation');
        $productCollection = Mage::getModel('catalog/product');
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToSelect('recom_id');
        $opinionCollection->getSelect()->where('parent IS NULL');
        $opinionCollection->setOrder('date_add', 'DESC');
        $opinionDataId = $opinionCollection->getData();
        $productData = array();

        $i = 0;
        foreach ($opinionDataId as $key => $value) {
            $productId = $opinionCollectionForeach->load($value)->getProductId();

            $productData[$i]['name'] = $productCollection->load($productId)->getName();
            $productData[$i]['image'] = $productCollection->load($productId)->getImageUrl();
            $productData[$i]['product_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $productCollection->load($productId)->getUrlPath();
            $productData[$i]['average'] = $this->getAverage($value);
            $i++;
        }

        return $productData;
    }

    /**
     * Calculate average from each product rating
     * @param (int)$prodId
     * @return float|int
     */
    public function getAverage($opinionId)
    {
        $ratingFields = array('rating_price', 'rating_durability', 'rating_failure', 'rating_service');
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->getSelect()->where('parent IS NULL');
        $opinions = $opinionCollection->addFieldToFilter('recom_id', $opinionId)->getData();
        $rating = 0;

        foreach ($opinions as $opinion) {
            foreach ($ratingFields as $field) {
                $rating += $opinion[$field] / 4;
            }
        }
        $rating = round(($rating), 1);

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

        foreach ($singleId as $key => $value) {
            $usersIdRating[$value] = $this->getHelper()->getOpinionQty($value);
        }
        arsort($usersIdRating);

        $i = 0;
        foreach ($usersIdRating as $key => $value) {
            $userData[$i]['rating'] = $value;

            $collection = Mage::getModel('tim_recommendation/user')->getCollection();
            $collection->addFieldToFilter('customer_id', $key);
            $data = $collection->getData();
            $userData[$i]['avatar'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $data[0]['avatar'];
            $userData[$i]['customer_view_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation/user/profile/id/' . $key;
            $userData[$i]['name'] = $this->getHelper()->getCustomerName($key);

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
        foreach ($data as $item) {
            foreach ($item as $key => $value) {
                $arrayId[] = $value;
            }
        }
        $arrayId = array_unique($arrayId);

        return $arrayId;
    }

    /**
     * Returns custom opinion data
     * @param (int)$userId
     * @return array
     */
    public function getUserOpinionData($userId)
    {
        $ratingFields = array('rating_price', 'rating_durability', 'rating_failure', 'rating_service');
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->getSelect()->where('parent IS NULL')->where('user_id = ' . $userId);
        $opinionCollection->setOrder('date_add', 'DESC');
        $opinionData = $opinionCollection->getData();


        $userOpinionData = array();
        $i = 0;
        foreach ($opinionData as $item) {
            $rating = 0;
            $productId = $item['product_id'];
            $productCollection = Mage::getModel('catalog/product');
            $userOpinionData[$i]['image'] = $productCollection->load($productId)->getImageUrl();
            $userOpinionData[$i]['url'] = $productCollection->load($productId)->getProductUrl();
            $userOpinionData[$i]['name'] = $productCollection->load($productId)->getName();
            foreach ($ratingFields as $field) {
                $rating += $item[$field] / 5;
            }
            $userOpinionData[$i]['rating'] = round($rating, 1);

            $i++;
        }
        return $userOpinionData;
    }

    /**
     * Returns customer comments and date
     * @param (int)$userId
     * @return mixed
     */
    public function getOpinionComment($userId)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToFilter('acceptance', 1);
        $opinionCollection->getSelect()->where('parent IS NOT NULL')->where('user_id = ' . $userId);
        $opinionCollection->addFieldToSelect('comment');
        $opinionCollection->addFieldToSelect('date_add');
        $opinionCollection->setOrder('date_add', 'DESC');
        $result = $opinionCollection->getData();

        return $result;
    }

    /**
     * Get acceptance status from last added opinion to the product
     * @param int $productId
     * @return int
     */
    public function opinionAcceptanceStatus($productId)
    {
        $opinionId = $this->getLastAddedOpinionId($productId);
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($opinionId);
        $acceptance = (int)$opinion->getAcceptance();

        return $acceptance;
    }

    /**
     * Check GET data for wrong data.
     * If data true, return array with status and url
     * @return array
     * @throws Exception
     */
    public function getConfirmData()
    {
        $requestArray = $this->getRequest()->getParams();//['request'],['id']
        $salt = $this->getHelper()->getSalt();
        $md5 = $this->getHelper()->getRecommendationMd5($requestArray['id']);
        $request0 = sha1($salt . '0' . $md5);
        $request1 = sha1($salt . '1' . $md5);
        $resultData = array();
        if ($requestArray['request'] == $request0) {
            $resultData['status'] = '0';
            $resultData['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation/index/allow/request/' . $requestArray['request'] . '/id/' . $requestArray['id'];
        } elseif ($requestArray['request'] == $request1) {
            $resultData['status'] = '1';
            $resultData['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation/index/moderate/request/' . $requestArray['request'] . '/id/' . $requestArray['id'];
        }
        return $resultData;
    }

    /**
     * Returns logged in user info
     * or false if user not logged in
     * @return array|bool
     */
    public function getPersonalUserData()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerInfo = array();
            $_helper = $this->getHelper();
            $customerId = Mage::getSingleton('customer/session')->getCustomer()->getId();
            $customerInfo['opinionQty'] = $_helper->getOpinionQty($customerId);
            $customerInfo['customerName'] = $_helper->getCustomerName($customerId);
            $customerTypeId = $_helper->getCustomerUserTypeId($customerId);
            $customerInfo['customerTypeName'] = $_helper->getUserTypeName($customerTypeId);
            $customerInfo['avatar'] = $_helper->getCustomerAvatar($customerId);
            $customerInfo['editUrl'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation/user/profile/id/' . $customerId;
            $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
            $customerInfo['engage'] = $user->getEngage();
            return $customerInfo;
        } else {
            return false;
        }
    }

    /**
     * @return Tim_Recommendation_Helper_Data
     */
    public function getHelper()
    {
        return Mage::helper('tim_recommendation');
    }

    /**
     * Get all fields from system configuration (tim_settings/required_opinion_fields)
     * @return array
     */
    public function getRequiredFields()
    {
        return $this->getHelper()->getOpinionRequiredFields();
    }
}