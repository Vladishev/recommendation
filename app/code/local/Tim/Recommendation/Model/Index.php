<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Model_Index. Methods for sort and pagination.
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Model_Index extends Mage_Core_Model_Abstract
{
    /**
     * Accepted value
     */
    const ACCEPTED = 1;

    /**
     * Returns recom_id list of opinions
     *
     * @param int $productId Native Magento product ID
     * @param string $order Sort order
     * @param string $field Table field
     * @param int $limit Opinions count per page
     * @param int $curPage Current page
     * @return bool or array
     */
    public function getOpinionsForProduct($productId, $limit = 10, $curPage = 1, $order = 'DESC', $field = 'date_add')
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('product_id', (int) $productId);
        $collection->addFieldToFilter('acceptance', self::ACCEPTED);
        $collection->getSelect()->where('parent IS NULL');
        $collection->addFieldToSelect('recom_id');
        $collection->addFieldToSelect('tim_ip');
        $collection->addFieldToSelect('tim_host');
        $collection->addFieldToSelect('user_id');
        $collection->setOrder($field, $order);
        $collection->setPageSize((int) $limit);
        $collection->setCurPage((int) $curPage);
        $data = $collection->getData();
        if (empty($data)) {
            return false;
        }
        return $data;
    }

    /**
     * Returns count of user's opinions
     *
     * @param int $userId Native Magento customer ID
     * @return int
     */
    public function getUserOpinionCount($userId)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('user_id', (int) $userId);
        $collection->addFieldToFilter('parent', array('null' => true));
        $collection->addFieldToFilter('acceptance', self::ACCEPTED);
        $collection->addFieldToSelect('recom_id');
        $count = $collection->getSize();

        return $count;
    }

    /**
     * Returns custom opinion data
     *
     * @param int $userId Native Magento customer ID
     * @param int $limit Records limit
     * @param int $curPage Current page
     * @param string $order Sort order
     * @param string $field Table field
     * @return array
     */
    public function getUserOpinionData($userId, $limit, $curPage, $order, $field)
    {
        $opinionCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $opinionCollection->addFieldToSelect(array('product_id','recom_id','average_rating'));
        $opinionCollection->addFieldToFilter('user_id', (int) $userId);
        $opinionCollection->addFieldToFilter('parent', array('null' => true));
        $opinionCollection->addFieldToFilter('acceptance', self::ACCEPTED);
        $opinionCollection->setOrder($field, $order);
        $opinionCollection->setPageSize((int) $limit);
        $opinionCollection->setCurPage((int) $curPage);
        $opinionData = $opinionCollection->getData();

        $userOpinionData = array();
        $i = 0;
        foreach ($opinionData as $item) {
            $productId = (int) $item['product_id'];
            $product = Mage::getModel('catalog/product')->load($productId);
            $userOpinionData[$i]['image'] = $product->getImageUrl();
            $userOpinionData[$i]['url'] = $product->getProductUrl();
            $userOpinionData[$i]['name'] = $product->getName();
            $userOpinionData[$i]['recom_id'] = (int) $item['recom_id'];
            $userOpinionData[$i]['rating'] = $item['average_rating'];

            $i++;
        }
        return $userOpinionData;
    }

    /**
     * Returns user's comments
     *
     * @param int $userId Native Magento customer ID
     * @param int $limit Records limit
     * @param int $curPage Current page
     * @param string $order Sort order
     * @return mixed
     */
    public function getOpinionComment($userId, $limit, $curPage, $order)
    {
        $recommendationCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $recommendationCollection->addFieldToFilter('acceptance', self::ACCEPTED);
        $recommendationCollection->addFieldToFilter('user_id', (int) $userId);
        $recommendationCollection->addFieldToFilter('parent', array('neq' => 'NULL'));
        $recommendationCollection->addFieldToSelect(array('comment', 'date_add', 'product_id'));
        $recommendationCollection->setOrder('date_add', $order);
        $recommendationCollection->setPageSize((int) $limit);
        $recommendationCollection->setCurPage((int) $curPage);
        $comments = $recommendationCollection->getData();
        $dateModel = Mage::getModel('core/date');

        $i = 0;
        foreach ($comments as $comment) {
            $productId = (int) $comment['product_id'];
            $product = Mage::getModel('catalog/product')->load($productId);
            $comments[$i]['name'] = $product->getName();
            $comments[$i]['url'] = $product->getProductUrl();
            $comments[$i]['date_add'] = date('Y-m-d H:i:s', $dateModel->timestamp($comment['date_add']));
            $i++;
        }

        $count = $this->getCommentsCount((int) $userId);
        $comments[0]['pagesCount'] = ceil($count / $limit);
        $comments[0]['curPage'] = (int) $curPage;

        return $comments;
    }

    /**
     * Evaluates comments count for particular user
     *
     * @param int $userId Native Magento customer ID
     * @return int
     */
    public function getCommentsCount($userId)
    {
        $recommendationCollection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $recommendationCollection->addFieldToFilter('acceptance', self::ACCEPTED);
        $recommendationCollection->addFieldToFilter('user_id', (int) $userId);
        $recommendationCollection->addFieldToFilter('parent', array('neq' => 'NULL'));
        $count = $recommendationCollection->getSize();

        return $count;
    }

    /**
     * Returns count of accepted opinions for product
     *
     * @param int $productId Native Magento product ID
     * @return int
     */
    public function getOpinionCount($productId)
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter('product_id', (int) $productId);
        $collection->addFieldToFilter('acceptance', self::ACCEPTED);
        $collection->getSelect()->where('parent IS NULL');
        $collection->addFieldToSelect('recom_id');
        $count = $collection->getSize();

        return $count;
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
                $mediaFiles = Mage::helper('tim_recommendation')->getOpinionMediaPath($recomId);
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
     * Gets points for adding image to the opinion
     *
     * @return int
     */
    public function getAddOpinionImagePoint()
    {
        $points = Mage::getStoreConfig('tim_settings/customer_points/image_to_opinion');
        return (int)$points;
    }
}