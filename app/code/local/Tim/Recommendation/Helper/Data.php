<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * @return array
     */
    public function getNonAdminUserTypes()
    {
        $collection = Mage::getModel('tim_recommendation/userType')->getCollection();
        $collection->addFieldToFilter('admin', 0);
        $data = $collection->getData();

        return $data;
    }

    /**
     * @param int $customerId
     * @return string
     */
    public function getCustomerUserTypeId($customerId)
    {
        $user = Mage::getModel('tim_recommendation/user')->load((int) $customerId, 'customer_id');
        $userTypeId = $user->getUserType();

        return $userTypeId;
    }

    public function getRecommendationDataUrl()
    {
        $url = "recommendation/user/data";
        return $url;
    }

    /**
     * Gets md5 hash from tim_recommendation table
     * @param int $recomId
     * @return string
     */
    public function getRecommendationMd5($recomId)
    {
        $object = Mage::getModel('tim_recommendation/recommendation')->load((int) $recomId);
        $md5hash = $object->getMd5();

        return $md5hash;
    }

    /**
     * @return mixed
     */
    public function getSiteUrl()
    {
        $customerId = (int) Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getWww();
        return $siteUrl;
    }

    /**
     * If passed $customerId - returns nick for passed id.
     * If $customerId not passed - returns current customer nick.
     * @return mixed
     */
    public function getUserNick($customerId = null)
    {
        if (empty($customerId)) {
            $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
            $nick = Mage::getModel('tim_recommendation/user')->load((int) $customerId, 'customer_id')->getNick();
        } else {
            $nick = Mage::getModel('tim_recommendation/user')->load((int) $customerId, 'customer_id')->getNick();
        }
        return $nick;
    }

    /**
     * @return bool|string
     */
    public function getCustomerBanner()
    {
        $customerId = (int) Mage::helper('customer')->getCustomer()->getEntityId();
        $banner = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getAd();
        if (!empty($banner)) {
            $banner = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $banner;
            return $banner;
        } else {
            return false;
        }
    }

    /**
     * @return bool|string
     */
    public function getCustomerAvatar()
    {
        $customerId = (int) Mage::helper('customer')->getCustomer()->getEntityId();
        $avatar = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getAvatar();
        if (!empty($avatar)) {
            $avatar = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $avatar;
            return $avatar;
        } else {
            return false;
        }
    }

    public function getCustomerDescription()
    {
        $customerId = (int) Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getDescription();
        return $siteUrl;
    }

    /**
     * Concatinates customer first name and last name
     * @param int $customerId
     * @return string
     */
    public function getCustomerName($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load((int) $customerId);
        $name = $customer->getFirstname() . ' ' . $customer->getLastname();

        return $name;
    }

    /**
     * Get customer nickname
     * @param int $customerId
     * @return string
     */
    public function getCustomerNickname($customerId)
    {
        $recommendationUser = Mage::getModel('tim_recommendation/user')->load((int) $customerId, 'customer_id');
        $nickname = $recommendationUser->getNick();

        return $nickname;
    }

    /**
     * Get customer name or nickname
     * @param int $customerId
     * @return string
     */
    public function getCustomerNameOrNick($customerId)
    {
        $customerName = $this->getCustomerNickname((int) $customerId);
        if (empty($customerName)) {
            $customerName = $this->getCustomerName((int) $customerId);
        }

        return $customerName;
    }

    /**
     * Get name of user type
     * @param int $userTypeId
     * @return string
     */
    public function getUserTypeName($userTypeId)
    {
        $userType = Mage::getModel('tim_recommendation/userType')->load((int) $userTypeId, 'user_type_id');
        $userTypeName = $userType->getName();

        return $userTypeName;
    }

    /**
     * Get paths to opinion media files
     * @param int $opinionId
     * @return array
     */
    public function getOpinionMediaPath($opinionId)
    {
        $collection = Mage::getModel('tim_recommendation/media')->getCollection();
        $collection->addFieldToFilter('recom_id', (int) $opinionId);
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
     * Counts quantity of opinions
     * @param int|bool $customerId
     * @return int
     */
    public function getOpinionQty($customerId = false)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToSelect('user_id');
        $opinionCollection->getSelect()->where('parent IS NULL');
        if ($customerId) {
            $opinionCollection->addFieldToFilter('user_id', (int) $customerId);
        }
        $qty = count($opinionCollection->getData());

        return $qty;
    }

    /**
     * Compare values from system configuration (tim_recommendation/user_type/values)
     * and tim_recommendation/userType collection
     * @param array $userTypes
     * @return array
     */
    public function getUserTypeDiffIds($userTypes)
    {
        $formIds = array_keys($userTypes);
        $collectionData = Mage::getModel('tim_recommendation/userType')->getCollection()->getData();
        $configIds = array();
        foreach ($collectionData as $data) {
            $configIds[] .= $data['system_config_id'];
        }
        $diff = array_diff($configIds, $formIds);

        return $diff;
    }

    /**
     * Compare values from system configuration (tim_recommendation/user_level/level_values)
     * and tim_recommendation/userLevel collection
     * @param array $userLevel
     * @return array
     */
    public function getUserLevelDiffIds($userLevel)
    {
        $formIds = array_keys($userLevel);
        $collectionData = Mage::getModel('tim_recommendation/userLevel')->getCollection()->getData();
        $configIds = array();
        foreach ($collectionData as $data) {
            $configIds[] .= $data['system_config_id'];
        }
        $diff = array_diff($configIds, $formIds);

        return $diff;
    }

    /**
     * Get salt from system configuration
     * @return string
     */
    public function getSalt()
    {
        $salt = Mage::getStoreConfig('tim_salt/salt/value');
        return $salt;
    }

    /**
     * Checking for wrong data in GET method
     * @param (array)$requestArray
     * @return bool
     */
    public function checkForNoRoute($requestArray)
    {
        $salt = $this->getSalt();
        $md5 = $this->getRecommendationMd5($requestArray['id']);
        $request0 = sha1($salt . '0' . $md5);
        $request1 = sha1($salt . '1' . $md5);
        if ($requestArray['request'] == $request0) {
            return false;
        } elseif ($requestArray['request'] == $request1) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Save image to folder
     * @param string $varName
     * @param string $path
     * @param string $postName
     */
    public function saveImage($varName, $path, $postName)
    {
        $uploader = new Varien_File_Uploader($postName);
        $uploader->setAllowedExtensions(array('png', 'gif', 'jpeg', 'jpg'));
        $uploader->setAllowCreateFolders(true);
        $uploader->setAllowRenameFiles(false);
        $uploader->setFilesDispersion(false);
        $uploader->save($path, $varName);
    }

    /**
     * Gets row by recomId and check is it opinion or comment.
     * If opinion - returns recomId, if comment - returns 'parent' column data.
     * @param (int)$recomId
     * @return integer
     */
    public function checkForOpinionComment($recomId)
    {
        $recommendationRow = Mage::getModel('tim_recommendation/recommendation')->load((int) $recomId);
        if ($parent = $recommendationRow->getParent()) {
            return $parent;
        } else {
            return $recomId;
        }
    }

    /**
     * Return all path to avatars from system configuration (tim_avatar_settings/default_avatars)
     * @return array
     */
    public function getDefaultAvatarsPath()
    {
        $path = Mage::getBaseUrl('media') . 'tim/recommendation' . DS;
        $avatars = array();
        for ($i = 1; $i <= 5; $i++) {
            $fileName = Mage::getStoreConfig('tim_avatar_settings/default_avatars/avatar' . $i);
            if ($fileName) {
                $avatars[] = $path . $fileName;
            }
        }
        return $avatars;
    }

    /**
     * Return all file name to avatars from system configuration (tim_avatar_settings/default_avatars)
     * @return array
     */
    public function getDefaultAvatarsFile()
    {
        $avatars = array();
        for ($i = 1; $i <= 5; $i++) {
            $fileName = Mage::getStoreConfig('tim_avatar_settings/default_avatars/avatar' . $i);
            if ($fileName) {
                $avatars[] = $fileName;
            }
        }
        return $avatars;
    }

    /**
     * Gets customer type name to array
     * @return array
     */
    public function getCustomerTypeName()
    {
        $collection = Mage::getModel('tim_recommendation/userType')->getCollection();
        $options = array();
        foreach ($collection->getData() as $val) {
            $options[$val['name']] = $val['name'];
        }
        return $options;
    }

    /**
     * Get all fields from system configuration (tim_settings/required_opinion_fields)
     * @return array
     */
    public function getOpinionRequiredFields()
    {
        $fieldValues = Mage::getStoreConfig('tim_settings/required_opinion_fields');
        return $fieldValues;
    }

    /**
     * Takes two types:
     * - opinion - returns settings for opinion
     * - comment - returns settings for comment
     * @param (string)$type
     * @return array
     */
    public function getMaxMinCharacters($type)
    {
        $limit['max'] = Mage::getStoreConfig('tim_settings/max_min_length/tim_' . $type . '_max');
        $limit['min'] = Mage::getStoreConfig('tim_settings/max_min_length/tim_' . $type . '_min');

        return $limit;
    }

    /**
     * Gets points for adding opinion
     * @return int
     */
    public function getAddOpinionPoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/add_opinion');
        return (int) $points;
    }

    /**
     * Gets points for adding image to the opinion
     * @return int
     */
    public function getAddOpinionImagePoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/image_to_opinion');
        return (int) $points;
    }

    /**
     * Gets points for adding movie to the opinion
     * @return int
     */
    public function getAddOpinionMoviePoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/movie_to_opinion');
        return (int) $points;
    }

    /**
     * Gets points for adding comment to the opinion
     * @return int
     */
    public function getAddComentPoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/add_comment');
        return (int) $points;
    }

    /**
     * Gets info about user level for client
     * @return array
     */
    public function getUserLevelClient()
    {
        $data = unserialize(Mage::getStoreConfig('tim_recommendation/user_level/client'));
        return $data;
    }

    /**
     * Gets info about user level for expert
     * @return array
     */
    public function getUserLevelExpert()
    {
        $data = array();
        $values = unserialize(Mage::getStoreConfig('tim_recommendation/user_level/expert'));
        $i = 0;
        foreach ($values as $item) {
            $data[$i]['point'] = $item['point'];
            $data[$i]['email_addresses'] = explode(',', str_replace(' ', '', $item['email_addresses']));
            $i++;
        }
        return $data;
    }

    /**
     * Gets user score
     * @param $customerId
     * @return string
     */
    public function getUserScore($customerId)
    {
        $customer = Mage::getModel('customer/customer')->load((int) $customerId);
        $user = Mage::getModel('tim_recommendation/user')->load((int) $customerId, 'customer_id');
        $customerPoints = $user->getPoints();
        $customerLevel = $user->getLevel();
        $userLevelsClient = $this->getUserLevelClient();
        $userLevelsExpert = $this->getUserLevelExpert();
        $point = '';
        foreach ($userLevelsExpert as $userLevel) {
            if (in_array($customer->getEmail(), $userLevel['email_addresses'])) {
                $point = $userLevel['point'];
            }
        }
        if (empty($point)) {
            if (empty($customerLevel) && !empty($customerPoints)) {
                foreach ($userLevelsClient as $userLevel) {
                    if ($customerPoints >= $userLevel['from'] && $customerPoints <= $userLevel['to']) {
                        $point = $userLevel['point'];
                        break;
                    }
                }
            } elseif (!empty($customerLevel) && !empty($customerPoints)) {
                $level = 0;
                foreach ($userLevelsClient as $userLevel) {
                    if ($customerPoints >= $userLevel['from'] && $customerPoints <= $userLevel['to']) {
                        $level = $userLevel['point'];
                        break;
                    }
                }
                if ($level > $customerLevel) {
                    $point = $level;
                } else {
                    $point = $customerLevel;
                }
            } elseif (!empty($customerLevel) && empty($customerPoints)) {
                $point = $customerLevel;
            }
        }
        return $point;
    }

    /**
     * Gets image path without url/youtube path
     * @param int $recomId
     * @return array
     */
    public function getImages($recomId)
    {
        $opinionMedia = $this->getOpinionMediaPath((int) $recomId);
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
     * Save points to user for adding opinion or comment
     * @param object $recommendationModel
     */
    public function savePointsForCustomer($recommendationModel)
    {
        //check on acceptance for opinion or comment
        if (!$recommendationModel->getAcceptance()) {
            $recomId = (int) $recommendationModel->getRecomId();
            $userId = (int) $recommendationModel->getUserId();
            $userModel = Mage::getModel('tim_recommendation/user')->load($userId, 'customer_id');
            $mediaModel = Mage::getModel('tim_recommendation/media')->load($recomId, 'recom_id');
            $opinionOrComment = $this->checkOpinionOrComment($recomId);
            //check is it opinion or comment
            if ($opinionOrComment == 'opinion') {
                $userModel->setPoints($userModel->getPoints() + $this->getAddOpinionPoint());
            } elseif ($opinionOrComment == 'comment') {
                $userModel->setPoints($userModel->getPoints() + $this->getAddComentPoint());
            }
            //check on media files
            if ($mediaData = $mediaModel->getData()) {
                $mediaFiles = $this->getOpinionMediaPath($recomId);
                if (array_key_exists('url/youtube', $mediaFiles)) {
                    $userModel->setPoints($userModel->getPoints() + $this->getAddOpinionMoviePoint());
                }
                if (isset($mediaFiles[0])) {
                    $userModel->setPoints($userModel->getPoints() + $this->getAddOpinionImagePoint());
                }
            }
            try {
                $userModel->save();
            } catch (Exception $i) {
                Mage::log($i->getMessage(), null, 'tim_recommendation.log');
            }
        }
    }

    /**
     * Check opinion or comment by recom_id
     * @param $recomId
     * @return string
     */
    function checkOpinionOrComment($recomId)
    {
        $parent = Mage::getModel('tim_recommendation/recommendation')->load((int) $recomId)->getParent();
        if ($parent) {
            return 'comment';
        } else {
            return 'opinion';
        }
    }
}
