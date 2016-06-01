<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Helper_Data. Common useful methods.
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Helper_Data extends Mage_Core_Helper_Abstract
{
    /**
     * Return all user types except admin type
     *
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
     * Return user type ID
     *
     * @param string|int $customerId Native Magento customer ID
     * @return string
     */
    public function getCustomerUserTypeId($customerId)
    {
        $user = Mage::getModel('tim_recommendation/user')->load((int)$customerId, 'customer_id');
        $userTypeId = $user->getUserType();

        return $userTypeId;
    }

    /**
     * Return recommendation/user/data handler
     *
     * @return string
     */
    public function getRecommendationDataUrl()
    {
        $url = "recommendation/user/data";
        return $url;
    }

    /**
     * Gets md5 hash from tim_recommendation table
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @return string
     */
    public function getRecommendationMd5($recomId)
    {
        $object = Mage::getModel('tim_recommendation/recommendation')->load((int)$recomId);
        $md5hash = $object->getMd5();

        return $md5hash;
    }

    /**
     * Returns user site url
     *
     * @return mixed
     */
    public function getSiteUrl()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getWww();
        return $siteUrl;
    }

    /**
     * Returns user nick
     *
     * If passed $customerId - returns nick for passed id
     * If $customerId not passed - returns current customer nick
     *
     * @param null $customerId Native Magento customer ID
     * @return mixed
     */
    public function getUserNick($customerId = null)
    {
        if (empty($customerId)) {
            $customerId = Mage::helper('customer')->getCustomer()->getEntityId();
            $nick = Mage::getModel('tim_recommendation/user')->load((int)$customerId, 'customer_id')->getNick();
        } else {
            $nick = Mage::getModel('tim_recommendation/user')->load((int)$customerId, 'customer_id')->getNick();
        }
        return $nick;
    }

    /**
     * Returns user banner
     *
     * @return bool|string
     */
    public function getCustomerBanner()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $banner = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getAd();
        if (!empty($banner)) {
            $banner = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $banner;
            return $banner;
        } else {
            return false;
        }
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
     * Returns user description
     *
     * @return mixed
     */
    public function getCustomerDescription()
    {
        $customerId = (int)Mage::helper('customer')->getCustomer()->getEntityId();
        $siteUrl = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id')->getDescription();
        return $siteUrl;
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
     * Get paths to opinion media files
     *
     * @param int $opinionId ID from tim_recommendation table(recom_id)
     * @return array
     */
    public function getOpinionMediaPath($opinionId)
    {
        $collection = Mage::getModel('tim_recommendation/media')->getCollection();
        $collection->addFieldToFilter('recom_id', (int)$opinionId);
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
     * Counts quantity of opinions and comments
     *
     * @param int|bool $customerId Native Magento customer ID
     * @return int
     */
    public function getOpinionQty($customerId = false)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToSelect('user_id');
        $opinionCollection->addFieldToFilter('acceptance', 1);
        if ($customerId) {
            $opinionCollection->addFieldToFilter('user_id', (int)$customerId);
        }
        $qty = count($opinionCollection->getData());

        return $qty;
    }

    /**
     * Compare values from system configuration (tim_recommendation/user_type/values) and tim_recommendation/userType collection
     *
     * @param array $userTypes Array with user types
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
     * Compare values from system configuration (tim_recommendation/user_level/level_values) and tim_recommendation/userLevel collection
     *
     * @param array $userLevel Array with user levels
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
     *
     * @return string
     */
    public function getSalt()
    {
        $salt = Mage::getStoreConfig('tim_salt/salt/value');
        return $salt;
    }

    /**
     * Get email of admin for malpractice from system configuration
     *
     * @return string
     */
    public function getMalpracticeEmailTo()
    {
        $emailTo = Mage::getStoreConfig('tim_settings/confirm_set/tim_malpractice_email_to');
        return $emailTo;
    }

    /**
     * Get malpractice mail status from system configuration
     *
     * @return int
     */
    public function getMalpracticeEnabled()
    {
        $emailStatus = (int)Mage::getStoreConfig('tim_settings/confirm_set/tim_malpractice_enabled');
        return $emailStatus;
    }

    /**
     * Checking for wrong data in GET method
     *
     * @param array $requestArray Request from url
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
     *
     * @param string $varName Image name
     * @param string $path Path for save
     * @param string $postName Post name
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
     * Gets row by recomId and check is it opinion or comment
     *
     * If opinion - returns recomId
     * If comment - returns 'parent' column data
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @return int
     */
    public function checkForOpinionComment($recomId)
    {
        $recommendationRow = Mage::getModel('tim_recommendation/recommendation')->load((int)$recomId);
        if ($parent = $recommendationRow->getParent()) {
            return $parent;
        } else {
            return $recomId;
        }
    }

    /**
     * Return all path to avatars from system configuration (tim_avatar_settings/default_avatars)
     *
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
     *
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
     *
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
     *
     * @return array
     */
    public function getOpinionRequiredFields()
    {
        $fieldValues = Mage::getStoreConfig('tim_settings/required_opinion_fields');
        return $fieldValues;
    }

    /**
     * Returns Store Config data
     *
     * Takes two types:
     * opinion - returns settings for opinion
     * comment - returns settings for comment
     *
     * @param string $type
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
     *
     * @return int
     */
    public function getAddOpinionPoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/add_opinion');
        return (int)$points;
    }

    /**
     * Gets points for adding image to the opinion
     *
     * @return int
     */
    public function getAddOpinionImagePoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/image_to_opinion');
        return (int)$points;
    }

    /**
     * Gets points for adding movie to the opinion
     *
     * @return int
     */
    public function getAddOpinionMoviePoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/movie_to_opinion');
        return (int)$points;
    }

    /**
     * Gets points for adding comment to the opinion
     *
     * @return int
     */
    public function getAddComentPoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/add_comment');
        return (int)$points;
    }

    /**
     * Gets info about user level for client
     *
     * @return array
     */
    public function getUserLevelClient()
    {
        $data = unserialize(Mage::getStoreConfig('tim_recommendation/user_level/client'));
        return $data;
    }

    /**
     * Gets info about abuse confirmation expired time
     * @return int
     */
    public function getAbuseExpiredTime()
    {
        $data = Mage::getStoreConfig('tim_settings/expired_time/abuse');
        return (int)$data;
    }

    /**
     * Check expired time for abuse.
     * @param int|float $expiredTime
     * @param string(Y-m-d H:i:s) $abuseAddedTime
     * @return bool
     */
    public function checkAbuseExpiredDate($expiredTime, $abuseAddedTime)
    {
        $expiredTime = $expiredTime * 3600; //prepare timestamp
        $abuseAddedTime = Mage::getModel('core/date')->timestamp($abuseAddedTime); //prepare timestamp
        $abuseFinishTime = $expiredTime + $abuseAddedTime;
        if ($abuseFinishTime > Mage::getModel('core/date')->timestamp()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Gets access based on user level
     *
     * @param $customerId
     * @return array
     */
    public function getUserLevelAccess($customerId)
    {
        $userLevels = $this->getUserLevelClient();
        $currentUserLevel = $this->getUserScore($customerId);
        $userLevelExpert = $this->getUserLevelExpert();
        $accesses = array();
        //if user is expert, give all access to him
        if ($currentUserLevel >= $userLevelExpert[0]['point']) {
            $accesses['moderation'] = 1;
            $accesses['update_present_visit_card'] = 1;
        } else {
            foreach ($userLevels as $level) {
                if ($level['point'] == $currentUserLevel) {
                    $accesses['moderation'] = $level['moderation'];
                    $accesses['update_present_visit_card'] = $level['update_present_visit_card'];
                }
            }
        }
        return $accesses;
    }

    /**
     * Gets info about user level for expert
     *
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
     *
     * @param $customerId $customerId Native Magento customer ID
     * @return string
     */
    public function getUserScore($customerId)
    {
        $customerEmail = Mage::getModel('customer/customer')
            ->getCollection()
            ->addFieldToFilter('entity_id', array(array('eq' => $customerId)))
            ->getFirstItem()
            ->getEmail();
        $user = Mage::getModel('tim_recommendation/user')->load($customerId, 'customer_id');
        $customerPoints = $user->getPoints();
        $customerLevel = $user->getLevel();
        $userLevelsClient = $this->getUserLevelClient();
        $userLevelsExpert = $this->getUserLevelExpert();
        $point = '';
        foreach ($userLevelsExpert as $userLevel) {
            if (in_array($customerEmail, $userLevel['email_addresses'])) {
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
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @return array
     */
    public function getImages($recomId)
    {
        $opinionMedia = $this->getOpinionMediaPath((int)$recomId);
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
     *
     * @param object $recommendationModel
     */
    public function savePointsForCustomer($recommendationModel)
    {
        //check on acceptance for opinion or comment
        if (!$recommendationModel->getAcceptance()) {
            $recomId = (int)$recommendationModel->getRecomId();
            $userId = (int)$recommendationModel->getUserId();
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
     *
     * @param int $recomId ID from tim_recommendation table(recom_id)
     * @return string
     */
    public function checkOpinionOrComment($recomId)
    {
        $parent = Mage::getModel('tim_recommendation/recommendation')->load((int)$recomId)->getParent();
        if ($parent) {
            return 'comment';
        } else {
            return 'opinion';
        }
    }

    /**
     * Sending email
     *
     * @param string $toEmail
     * @param array $templateVar
     * @param int $templateId
     * @param string $subject
     * @return bool
     */
    public function sendEmail($toEmail, $templateVar, $templateId, $subject)
    {
        $emailTemplate = Mage::getModel('core/email_template')->loadDefault($templateId);
        $processedTemplate = $emailTemplate->getProcessedTemplate($templateVar);
        $mail = Mage::getModel('core/email')
            ->setToEmail($toEmail)
            ->setBody($processedTemplate)
            ->setSubject(Mage::helper('tim_recommendation')->__($subject))
            ->setFromName(Mage::getStoreConfig('trans_email/ident_general/name'))
            ->setType('html');
        try {
            $mail->send();
            return true;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            return false;
        }
    }

    /**
     * Prepare url for modification opinion
     *
     * @param int $recomId
     * @return string
     */
    public function getModifyOpinionUrl($recomId)
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation/index/modifyOpinion/opinionId/' . $recomId;
        return $url;
    }

    /**
     * Prepare url for modification comment
     *
     * @param int $recomId
     * @return string
     */
    public function getModifyCommentUrl($recomId)
    {
        $url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'recommendation/index/modifyComment/commentId/' . $recomId;
        return $url;
    }

    /**
     * Compare received salt and project salt in sha1 encoding
     * @param sha1 string $receivedSalt
     * @return bool
     */
    public function checkRecommendationSalt($receivedSalt)
    {
        $recommendationSalt = sha1($this->getSalt());
        if ($recommendationSalt == $receivedSalt) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Remove directory recursively
     * @param $direction
     * @return bool
     */
    public function rmDir($direction)
    {
        $files = array_diff(scandir($direction), array('.', '..'));
        foreach ($files as $file) {
            if (is_dir("$direction/$file")) {
                $this->delDir("$direction/$file");
            } else {
                unlink("$direction/$file");
            }
        }
        return rmdir($direction);
    }

    /**
     * Resize image and move it to selected directory
     * @param string $imgFullPath
     * @param string $folderForFiles
     * @param string $fileName
     * @param int $width
     * @param null $height
     */
    public function moveAndResizeImage($imgFullPath, $folderForFiles, $fileName, $width, $height = null)
    {
        $imageWidth = getimagesize($imgFullPath)[0];
        if ($imageWidth <= $width) {
            $width = $imageWidth;
        }
        $resizePathFull = $folderForFiles . DS . $fileName;

        if (file_exists($imgFullPath) && !file_exists($resizePathFull)) {
            $imageObj = new Varien_Image($imgFullPath);
            $imageObj->keepAspectRatio(false);
            $imageObj->keepFrame(false);
            $imageObj->keepTransparency(TRUE);
            $imageObj->constrainOnly(TRUE);
            $imageObj->resize($width, $height);
            $imageObj->quality(60);
            try {
                $imageObj->save($resizePathFull);
            } catch (Exception $e) {
                Mage::log($e->getMessage(), NULL, 'tim_recommendation.log');
            }
        }
    }
}
