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
     * Customer id
     *
     * @var int
     */
    protected $_customerId;

    /**
     * User's data array
     *
     * @var array
     */
    protected $_userData;

    /**
     * Prepare array with user information
     * @param int $customerId
     * @return array
     */
    public function getUserData($customerId)
    {
        if ((int) $customerId !== $this->_customerId) {
            $user = Mage::getModel('tim_recommendation/user')
                ->getCollection()
                ->addFieldToFilter('customer_id', array( array('eq' => $customerId)))
                ->addFieldToSelect(array('www', 'avatar', 'description', 'ad', 'engage', 'user_type', 'customer_id'))
                ->getFirstItem();
            $userData = $user->getData();
            $userData['user_type_name'] = $this->getRecomHelper()->getUserTypeName($user['user_type']);
            $userData['customer_name'] = $this->getRecomHelper()->getCustomerNameOrNick((int) $customerId);
            $userData['customer_nick'] = Mage::helper('tim_recommendation')->getUserNick((int) $customerId);
            $userData['opinion_qty'] = $this->getRecomHelper()->getOpinionQty((int) $customerId);
            $userData['user_score'] = $this->getRecomHelper()->getUserScore((int) $customerId);
            $this->_userData = $userData;
            $this->_customerId = (int) $customerId;
        }

        return $this->_userData;
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
        $opinionMedia = $this->getRecomHelper()->getOpinionMediaPath($recomId);
        $opinion['date_add'] = Mage::getModel('core/date')->date('d-m-Y', strtotime($opinion['date_add']));
        if (!empty($opinionMedia['url/youtube'])) {
            $opinion['movie_url'] = $opinionMedia['url/youtube'];
        }
        $opinion['images'] = $this->getRecomHelper()->getImages($opinion['recom_id']);
        $opinion['comments'] = $this->getOpinionComments($opinion['recom_id']);
        $opinion['name'] = $this->getRecomHelper()->getCustomerNameOrNick($opinion['user_id']);

        return $opinion;
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
        $lastAddedOpinion = $opinionCollection->getFirstItem();

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
        $collection->addFieldToSelect(array('user_id', 'comment', 'date_add', 'recom_id'));
        $collection->addFieldToFilter('parent', $opinionId);
        $collection->addFieldToFilter('acceptance', 1);
        $collection->getSelect()->where('parent IS NOT NULL');
        $collection->setOrder('date_add', 'DESC');
        $data = $collection->getData();
        $comments = array();
        $dateModel = Mage::getModel('core/date');
        foreach ($data as $comment) {
            $comments[] = array(
                'name' => $this->getRecomHelper()->getCustomerNameOrNick($comment['user_id']),
                'comment' => $comment['comment'],
                'date_add' => date('Y-m-d H:i:s', $dateModel->timestamp($comment['date_add'])),
                'recom_id' => $comment['recom_id'],
            );
        }

        return $comments;
    }

    /**
     * Calculates the average rating of the last added opinion to the product
     * @param int $recomId
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
     * Gets custom opinion data
     * @return array
     */
    public function getProductOpinionData()
    {
        $productModel = Mage::getModel('catalog/product');
        $opinionModel = Mage::getModel('tim_recommendation/recommendation');
        $opinionCollection = $opinionModel->getCollection();
        $opinionCollection->addFieldToSelect('recom_id');
        $opinionCollection->getSelect()->where('parent IS NULL');
        $opinionCollection->setOrder('date_add', 'DESC');
        $opinionDataId = $opinionCollection->getData();
        $productData = array();

        $i = 0;
        foreach ($opinionDataId as $key => $value) {
            $productId = $opinionModel->load($value)->getProductId();
            $product = $productModel->load($productId);
            $productData[$i]['name'] = $product->getName();
            $productData[$i]['image'] = Mage::getModel('catalog/product_media_config')
                ->getMediaUrl($product->getImage());
            $productData[$i]['product_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $product->getUrlPath();
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
        $opinionCollection->addFieldToSelect($ratingFields);
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
     * and sort it by sum of opinions.
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
            $usersIdRating[$value] = $this->getRecomHelper()->getOpinionQty($value);
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
            $userData[$i]['name'] = $this->getRecomHelper()->getCustomerName($key);

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
     * @param $userId
     * @param int $limit
     * @param int $curPage
     * @param string $order
     * @param string $field
     * @return array
     */
    public function getUserOpinionData($userId, $limit = 10, $curPage = 1, $order = 'DESC', $field = 'date_add')
    {
        $userOpinionData = Mage::getModel('tim_recommendation/index')->getUserOpinionData($userId, $limit, $curPage, $order, $field);
        return $userOpinionData;
    }

    /**
     * Returns user's comments
     * @param $userId
     * @param int $limit
     * @param int $curPage
     * @param string $order
     * @return mixed
     */
    public function getOpinionComment($userId, $limit = 10, $curPage = 1, $order = 'DESC')
    {
        $result = Mage::getModel('tim_recommendation/index')->getOpinionComment($userId, $limit, $curPage, $order);
        return $result;
    }

    /**
     * Returns count of accepted opinions for current product
     * @return mixed
     */
    public function getOpinionCount()
    {
        $productId = Mage::registry('current_product')->getId();
        $opinionCount = Mage::getModel('tim_recommendation/index')->getOpinionCount($productId);
        return $opinionCount;
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
        $salt = $this->getRecomHelper()->getSalt();
        $md5 = $this->getRecomHelper()->getRecommendationMd5($requestArray['id']);
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
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $_helper = $this->getRecomHelper();
            $customerId = $customer->getId();
            $customerInfo['opinionQty'] = $_helper->getOpinionQty($customerId);
            $customerInfo['customerName'] = $_helper->getCustomerNickname($customerId);
            $customerTypeId = $_helper->getCustomerUserTypeId($customerId);
            $customerInfo['customerTypeName'] = $_helper->getUserTypeName($customerTypeId);
            $customerInfo['avatar'] = $_helper->getCustomerAvatar();
            $customerInfo['editUrl'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation' . DS . 'user' . DS . 'edit';
            $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
            $customerInfo['engage'] = $user->getEngage();
            $customerInfo['user_score'] = $_helper->getUserScore($customerId);
            return $customerInfo;
        } else {
            return false;
        }
    }

    /**
     * Parses youtube url and return video ID
     * @param $url
     * @return bool|string
     */
    public function getYoutubeVideoId($url)
    {
        $regExp = "/(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^\"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+/";
        preg_match($regExp, $url, $matches);
        if (isset($matches[0])) {
            $videoId = $matches[0];
        } else {
            return false;
        }
        return $videoId;
    }

    /**
     * @return Tim_Recommendation_Helper_Data
     */
    public function getRecomHelper()
    {
        return Mage::helper('tim_recommendation');
    }

    /**
     * Get all fields from system configuration (tim_settings/required_opinion_fields)
     * @return array
     */
    public function getRequiredFields()
    {
        return $this->getRecomHelper()->getOpinionRequiredFields();
    }

    /**
     * Returns count of user's opinions
     * @param $userId
     * @return mixed
     */
    public function getUserOpinionCount($userId)
    {
        $opinionCount = Mage::getModel('tim_recommendation/index')->getUserOpinionCount($userId);
        return $opinionCount;
    }

    /**
     * Evaluates comments count for particular user
     * @param $userId
     * @return int
     */
    public function getCommentsCount($userId)
    {
        $commentsCount = Mage::getModel('tim_recommendation/index')->getCommentsCount($userId);
        return $commentsCount;
    }

    /**
     * Check user profile for required fields
     * @param $customerId
     * @return int
     */
    public function getProfileStatus($customerId)
    {
        if ($customerId) {
            $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
            $fields = array();
            $userTypes = Mage::helper('tim_recommendation')->getNonAdminUserTypes();

            if ($user) {
                $fields[] = $user->getNick();
                $fields[] = $user->getAvatar();
                if (!empty($userTypes)) {
                    $fields[] = $user->getUserType();
                }

                foreach ($fields as $field) {
                    if (empty($field)) {
                        return 0;
                    }
                }
                return 1;
            }
            return 0;
        } else {
            //for preventing show popup "edit you profile"
            return 2;
        }
    }

    /**
     * Prepare text for opinion placeholder
     * @param array $opinionLimitCharacters
     * @return string
     */
    public function getOpinionPlaceholder($opinionLimitCharacters)
    {
        $placeholderText = '';
        if (!empty($opinionLimitCharacters['min'])) {
            $placeholderText .= $this->__('The minimum number of characters is ') . $opinionLimitCharacters['min'] . '<br>';
        }
        if (!empty($opinionLimitCharacters['max'])) {
            $placeholderText .= $this->__('The maximum number of characters is ') . $opinionLimitCharacters['max'];
        }
        return $placeholderText;
    }

    /**
     * Prepare classes for validation opinion textareas
     * @param array $opinionLimitCharacters
     * @return string
     */
    public function getOpinionTextareaValidationClass($opinionLimitCharacters)
    {
        $class = '';
        if (!empty($opinionLimitCharacters['min'])) {
            $class .= ' min-length-opinion';
        }
        if (!empty($opinionLimitCharacters['max'])) {
            $class .= ' max-length-opinion';
        }
        return $class;
    }

    /**
     * Prepare text for comment placeholder
     * @param array $commentLimitCharacters
     * @return string
     */
    public function getCommentPlaceholder($commentLimitCharacters)
    {
        $placeholderText = '';
        if (!empty($commentLimitCharacters['min'])) {
            $placeholderText .= $this->__('The minimum number of characters is %s', $commentLimitCharacters['min']) . '<br>';
        }
        if (!empty($commentLimitCharacters['max'])) {
            $placeholderText .= $this->__('The maximum number of characters is %s', $commentLimitCharacters['max']);
        }
        return $placeholderText;
    }

    /**
     * Prepare classes for validation comment textareas
     * @param array $commentLimitCharacters
     * @return string
     */
    public function getCommentTextareaValidationClass($commentLimitCharacters)
    {
        $class = '';
        if (!empty($commentLimitCharacters['min'])) {
            $class .= ' min-length-comment';
        }
        if (!empty($commentLimitCharacters['max'])) {
            $class .= ' max-length-comment';
        }
        return $class;
    }

    /**
     * Add http protocol to raw link or return null
     * @param string $site
     * @return null|string
     */
    public function getFormatSiteLink($site)
    {
        if (!empty($site)) {
            if (!strstr($site, 'http://') && !strstr($site, 'https://')) {
                $site = 'http://' . $site;
            }
        } else {
            $site = null;
        }
        return $site;
    }

    /**
     * Return qty of pages for opinions
     * @param int $opinionCount
     * @param int $limitPerPage
     * @return float|int
     */
    public function getOpinionPagesCount($opinionCount, $limitPerPage)
    {
        $pagesCount = ceil($opinionCount / $limitPerPage);
        return $pagesCount;
    }

    /**
     * Return qty of pages for comments
     * @param int $commentsCount
     * @param int $limitPerPage
     * @return float|int
     */
    public function getCommentsPagesCount($commentsCount, $limitPerPage)
    {
        $commentsPagesCount = ceil($commentsCount / $limitPerPage);
        return $commentsPagesCount;
    }

    /**
     * Returns user data using id from request
     *
     * @return array
     * @throws Exception
     */
    public function getUserDataByRequest()
    {
        $userId = $this->getRequest()->getParam('id');
        return $this->getUserData($userId);
    }
}