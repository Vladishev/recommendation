<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_TimToolbarController extends Mage_Core_Controller_Front_Action
{
    /**
     * Describe type of sorting
     */
    const TOP_RATED = 'topRated';
    const LOW_RATED = 'lowRated';
    const OLDEST = 'oldest';

    /**
     * Collects data for sorted list
     */
    public function sortUserPageAction()
    {
        $params = $this->getRequest()->getParams();
        $limit = $params['countPerPage'];
        $curPage = $params['pageNumber'];
        switch ($params['sortBy']) {
            case self::TOP_RATED:
                $order = 'DESC';
                $field = 'average_rating';
                break;
            case self::LOW_RATED:
                $order = 'ASC';
                $field = 'average_rating';
                break;
            case self::OLDEST:
                $order = 'ASC';
                $field = 'date_add';
                break;
            default:
                $order = 'DESC';
                $field = 'date_add';
        }

        $recommendationBlock = $this->getLayout()->createBlock('tim_recommendation/recommendation');
        $opinionData = $recommendationBlock->getUserOpinionData($params['userId'], $limit, $curPage, $order, $field);
        $opinionsCount = $recommendationBlock->getUserOpinionCount($params['userId']);
        $opinionData[0]['pagesCount'] = ceil($opinionsCount / $limit);
        $opinionData[0]['curPage'] = $curPage;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($opinionData));
    }

    /**
     * Gets params for creating right array and sends it by json
     */
    public function showAction()
    {
        $params = $this->getRequest()->getParams();
        $limit = $params['countPerPage'];
        $curPage = $params['pageNumber'];
        switch ($params['sortBy']) {
            case self::TOP_RATED:
                $order = 'DESC';
                $field = 'average_rating';
                break;
            case self::LOW_RATED:
                $order = 'ASC';
                $field = 'average_rating';
                break;
            case self::OLDEST:
                $order = 'ASC';
                $field = 'date_add';
                break;
            default:
                $order = 'DESC';
                $field = 'date_add';
        }
        $recomIdSet = Mage::getModel('tim_recommendation/index')->getOpinionsForProduct($params['productId'], $limit, $curPage, $order, $field);
        $opinionsArray = $this->_getOpinionsArray($recomIdSet);
        $opinionsCount = Mage::getModel('tim_recommendation/index')->getOpinionCount($params['productId']);
        $opinionsArray[0]['pagesCount'] = ceil($opinionsCount / $limit);
        $opinionsArray[0]['curPage'] = $curPage;
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($opinionsArray));
    }

    /**
     * Creates array with sorted comment list
     */
    public function sortUserCommentsAction()
    {
        $params = $this->getRequest()->getParams();
        $limit = $params['countPerPage'];
        $curPage = $params['pageNumber'];

        if ($params['sortBy'] == self::OLDEST) {
            $order = 'ASC';
        } else {
            $order = 'DESC';
        }

        $recommendationBlock = $this->getLayout()->createBlock('tim_recommendation/recommendation');
        $commentData = $recommendationBlock->getOpinionComment($params['userId'], $limit, $curPage, $order);

        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($commentData));
    }

    /**
     * Collect array of opinions
     * @param (arr) $recomIdSet
     * @return (arr) mixed
     */
    protected function _getOpinionsArray($recomIdSet)
    {
        $i = 0;
        $recommendationBlock = $this->getLayout()->getBlockSingleton('tim_recommendation/recommendation');
        foreach ($recomIdSet as $opinion) {
            $recomIdSet[$i]['opinionData'] = $recommendationBlock->getOpinionData($opinion['recom_id']);
            if(isset($recomIdSet[$i]['opinionData']['movie_url'])){
                $recomIdSet[$i]['opinionData']['youtubeVideoId'] = $recommendationBlock->getYoutubeVideoId($recomIdSet[$i]['opinionData']['movie_url']);
            }
            $recomIdSet[$i]['userData'] = $recommendationBlock->getUserData($recomIdSet[$i]['opinionData']['user_id']);
            $i++;
        }
        return $recomIdSet;
    }
}