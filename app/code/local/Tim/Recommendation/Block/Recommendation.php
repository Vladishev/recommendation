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
     * Checks if opinion exist
     * @param int$productId
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
}