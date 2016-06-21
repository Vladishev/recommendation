<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Recommendation
 * Includes common module methods and logic
 *
 * @category   Tim
 * @package    Tim_Recommendation
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
     *
     * @param int $customerId Native Magento customer ID
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
            $userData['user_type_name'] = $this->getUserTypeName($user['user_type']);
            $userData['customer_name'] = $this->getCustomerNameOrNick((int) $customerId);
            $userData['customer_nick'] = $this->_getUserNick((int) $customerId);
            $userData['opinion_qty'] = $this->getRecomHelper()->getOpinionQty((int) $customerId);
            $userData['user_score'] = $this->getRecomHelper()->getUserScore((int) $customerId);
            $userData['user_access'] = $this->getRecomHelper()->getUserLevelAccess((int) $customerId);
            $this->_userData = $userData;
            $this->_customerId = (int) $customerId;
        }

        return $this->_userData;
    }

    /**
     * Returns customer nick
     *
     * @param int|null $customerId
     * @return string
     */
    protected function _getUserNick($customerId = null)
    {
        return Mage::getModel('tim_recommendation/user')->getUserNick($customerId);
    }

    /**
     * Gets host name by ip
     *
     * @param string $ip IP-address
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
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @return array
     */
    public function getOpinionData($recomId)
    {
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load((int) $recomId, 'recom_id')->getData();
        $opinionMedia = $this->getRecomHelper()->getOpinionMediaPath((int) $recomId);
        $opinion['date_add'] = Mage::getModel('core/date')->date('d-m-Y', strtotime($opinion['date_add']));
        if (!empty($opinionMedia['url/youtube'])) {
            $opinion['movie_url'] = $opinionMedia['url/youtube'];
        }
        $opinion['images'] = $this->getRecomHelper()->getImages((int) $opinion['recom_id']);
        $opinion['comments'] = $this->getOpinionComments((int) $opinion['recom_id']);
        $opinion['name'] = $this->getCustomerNameOrNick((int) $opinion['user_id']);

        return $opinion;
    }

    /**
     * Get last added opinion by date
     *
     * @param int $productId Native Magento product ID
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
     *
     * @param int $productId Native Magento product ID
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
     *
     * @param int $opinionId ID from tim_recommendation table(recom_id)
     * @return array
     */
    public function getOpinionComments($opinionId)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToSelect(array('user_id', 'comment', 'date_add', 'recom_id'));
        $collection->addFieldToFilter('parent', (int) $opinionId);
        $collection->addFieldToFilter('acceptance', 1);
        $collection->getSelect()->where('parent IS NOT NULL');
        $collection->setOrder('date_add', 'DESC');
        $data = $collection->getData();
        $comments = array();
        $dateModel = Mage::getModel('core/date');
        foreach ($data as $comment) {
            $comments[] = array(
                'name' => $this->getCustomerNameOrNick((int) $comment['user_id']),
                'comment' => $comment['comment'],
                'date_add' => date('Y-m-d H:i:s', $dateModel->timestamp($comment['date_add'])),
                'recom_id' => (int) $comment['recom_id'],
            );
        }

        return $comments;
    }

    /**
     * Calculates the average rating of the last added opinion to the product
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @return float
     */
    public function getProductEvaluation($recomId)
    {
        $data = Mage::getModel('tim_recommendation/recommendation')->load((int) $recomId, 'recom_id');
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
     *
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
            $productData[$i]['image'] = $product->getImageUrl();
            $productData[$i]['product_url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $product->getUrlPath();
            $productData[$i]['average'] = $this->getAverage($value);
            $i++;
        }

        return $productData;
    }

    /**
     * Calculate average from each product rating
     *
     * @param int $opinionId ID from tim_recommendation table(recom_id)
     * @return float|int
     */
    public function getAverage($opinionId)
    {
        $ratingFields = array('rating_price', 'rating_durability', 'rating_failure', 'rating_service');
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToSelect($ratingFields);
        $opinionCollection->getSelect()->where('parent IS NULL');
        $opinions = $opinionCollection->addFieldToFilter('recom_id', (int) $opinionId)->getData();
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
     *
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
            $userData[$i]['name'] = $this->getCustomerName($key);

            $i++;
        }
        return $userData;
    }

    /**
     * Return array with unique values
     *
     * @param array $data
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
     *
     * @param int $userId Native Magento customer ID
     * @param int $limit Limit records per page
     * @param int $curPage Current page
     * @param string $order Sort order
     * @param string $field Field name for sorting
     * @return array
     */
    public function getUserOpinionData($userId, $limit = 10, $curPage = 1, $order = 'DESC', $field = 'date_add')
    {
        $userOpinionData = Mage::getModel('tim_recommendation/index')->getUserOpinionData((int) $userId, (int) $limit, (int) $curPage, $order, $field);
        return $userOpinionData;
    }

    /**
     * Returns user's comments
     *
     * @param int $userId Native Magento customer ID
     * @param int $limit Limit records per page
     * @param int $curPage Current page
     * @param string $order Sort order
     * @return mixed
     */
    public function getOpinionComment($userId, $limit = 10, $curPage = 1, $order = 'DESC')
    {
        $result = Mage::getModel('tim_recommendation/index')->getOpinionComment((int) $userId, (int) $limit, (int) $curPage, $order);
        return $result;
    }

    /**
     * Returns count of accepted opinions for current product
     *
     * @return mixed
     */
    public function getOpinionCount()
    {
        $productId = Mage::registry('current_product')->getId();
        $opinionCount = Mage::getModel('tim_recommendation/index')->getOpinionCount((int) $productId);
        return $opinionCount;
    }

    /**
     * Get acceptance status from last added opinion to the product
     *
     * @param int $productId Native Magento product ID
     * @return int
     */
    public function opinionAcceptanceStatus($productId)
    {
        $opinionId = $this->getLastAddedOpinionId((int) $productId);
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load((int) $opinionId);
        $acceptance = (int) $opinion->getAcceptance();

        return $acceptance;
    }

    /**
     * Check GET data for wrong data
     * If data true, return array with status and url
     *
     * @return array
     * @throws Exception
     */
    public function getConfirmData()
    {
        $requestArray = $this->getRequest()->getParams();//['request'],['id']
        $salt = $this->getRecomHelper()->getSalt();
        $md5 = Mage::getModel('tim_recommendation/recommendation')->getRecommendationMd5($requestArray['id']);
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
     * Returns logged in user info or false if user not logged in
     *
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
            $customerInfo['customerName'] = $this->getCustomerNickname($customerId);
            $customerTypeId = Mage::getModel('tim_recommendation/user')->getCustomerUserTypeId($customerId);
            $customerInfo['customerTypeName'] = $this->getUserTypeName($customerTypeId);
            $customerInfo['avatar'] = $this->getCustomerAvatar();
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
     * Concatenates customer first name and last name
     *
     * @param int $customerId Native Magento customer ID
     * @return string
     */
    public function getCustomerName($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load((int)$customerId);
        $name = $customer->getFirstname() . ' ' . $customer->getLastname();

        return $name;
    }

    /**
     * Get customer name or nickname
     *
     * @param int $customerId Native Magento customer ID
     * @return string
     */
    public function getCustomerNameOrNick($customerId)
    {
        $customerName = $this->getCustomerNickname((int)$customerId);
        if (empty($customerName)) {
            $customerName = $this->getCustomerName((int)$customerId);
        }

        return $customerName;
    }

    /**
     * Get customer nickname
     *
     * @param int $customerId Native Magento customer ID
     * @return string
     */
    public function getCustomerNickname($customerId)
    {
        $recommendationUser = Mage::getModel('tim_recommendation/user')->load((int)$customerId, 'customer_id');
        $nickname = $recommendationUser->getNick();

        return $nickname;
    }

    /**
     * Get name of user type
     *
     * @param int $userTypeId
     * @return string
     */
    public function getUserTypeName($userTypeId)
    {
        $userType = Mage::getModel('tim_recommendation/userType')->load((int)$userTypeId, 'user_type_id');
        $userTypeName = $userType->getName();

        return $userTypeName;
    }

    /**
     * Parses youtube url and return video ID
     *
     * @param string $url URL
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
     *
     * @return array
     */
    public function getRequiredFields()
    {
        return $this->getRecomHelper()->getOpinionRequiredFields();
    }

    /**
     * Returns count of user's opinions
     *
     * @param int $userId Native Magento customer ID
     * @return mixed
     */
    public function getUserOpinionCount($userId)
    {
        $opinionCount = Mage::getModel('tim_recommendation/index')->getUserOpinionCount($userId);
        return $opinionCount;
    }

    /**
     * Evaluates comments count for particular user
     *
     * @param int $userId Native Magento customer ID
     * @return int
     */
    public function getCommentsCount($userId)
    {
        $commentsCount = Mage::getModel('tim_recommendation/index')->getCommentsCount((int) $userId);
        return $commentsCount;
    }

    /**
     * Check user profile for required fields
     *
     * @param int $customerId Native Magento customer ID
     * @return int
     */
    public function getProfileStatus($customerId)
    {
        if ($customerId) {
            $user = Mage::getModel('tim_recommendation/user')->load((int) $customerId, 'customer_id');
            $fields = array();
            $userTypes = $this->_getNonAdminUserTypes();

            if ($user) {
                $fields[] = $user->getNick();
                $fields[] = $user->getAvatar();
                if (!$this->_isEmpty($userTypes)) {
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
     * Check if object empty
     *
     * @param $obj
     * @return bool
     */
    protected function _isEmpty($obj)
    {
        $result = true;
        foreach ($obj as $item) {
            $name = $item->getName();
            if (!empty($name)) {
                $result = false;
            }
            break;
        }

        return $result;
    }

    /**
     * Return all user types except admin type
     *
     * @return object Tim_Recommendation_Model_UserType
     */
    protected function _getNonAdminUserTypes()
    {
        return Mage::getModel('tim_recommendation/userType')->getNonAdminUserTypes();
    }

    /**
     * Returns user avatar
     *
     * @return bool|string
     */
    public function getCustomerAvatar()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $avatar = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getAvatar();
        if (!empty($avatar)) {
            $avatar = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $avatar;
            return $avatar;
        } else {
            return false;
        }
    }

    /**
     * Prepare text for opinion placeholder
     *
     * @param array $opinionLimitCharacters Max and min limit of characters
     * @return string
     */
    public function getOpinionPlaceholder($opinionLimitCharacters)
    {
        $placeholderText = '';
        if (!empty($opinionLimitCharacters['min'])) {
            $placeholderText .= $this->getRecomHelper()->__('The minimum number of characters is ') . $opinionLimitCharacters['min'] . '<br>';
        }
        if (!empty($opinionLimitCharacters['max'])) {
            $placeholderText .= $this->getRecomHelper()->__('The maximum number of characters is ') . $opinionLimitCharacters['max'];
        }
        return $placeholderText;
    }

    /**
     * Prepare classes for validation opinion textareas
     *
     * @param array $opinionLimitCharacters Max and min limit of characters
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
     *
     * @param array $commentLimitCharacters Max and min limit of characters
     * @return string
     */
    public function getCommentPlaceholder($commentLimitCharacters)
    {
        $placeholderText = '';
        if (!empty($commentLimitCharacters['min'])) {
            $placeholderText .= $this->getRecomHelper()->__('The minimum number of characters is %s', $commentLimitCharacters['min']) . '<br>';
        }
        if (!empty($commentLimitCharacters['max'])) {
            $placeholderText .= $this->getRecomHelper()->__('The maximum number of characters is %s', $commentLimitCharacters['max']);
        }
        return $placeholderText;
    }

    /**
     * Prepare classes for validation comment textareas
     *
     * @param array $commentLimitCharacters Max and min limit of characters
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
     *
     * @param string $site URL
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
     *
     * @param int $opinionCount Count of opinions
     * @param int $limitPerPage Records limit per page
     * @return float|int
     */
    public function getOpinionPagesCount($opinionCount, $limitPerPage)
    {
        $pagesCount = ceil($opinionCount / $limitPerPage);
        return $pagesCount;
    }

    /**
     * Return qty of pages for comments
     *
     * @param int $commentsCount Count of comments
     * @param int $limitPerPage Records limit per page
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