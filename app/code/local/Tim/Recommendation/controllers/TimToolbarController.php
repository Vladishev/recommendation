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
     * Collects data for sorted list
     */
    public function sortUserPageAction()
    {
        $params = $this->getRequest()->getParams();
        $limit = $params['countPerPage'];
        $curPage = $params['pageNumber'];
        switch ($params['sortBy']) {
            case 'topRated':
                $order = 'DESC';
                $field = 'average_rating';
                break;
            case 'lowRated':
                $order = 'ASC';
                $field = 'average_rating';
                break;
            case 'oldest':
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
        die(json_encode($opinionData));
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
            case 'topRated':
                $order = 'DESC';
                $field = 'average_rating';
                break;
            case 'lowRated':
                $order = 'ASC';
                $field = 'average_rating';
                break;
            case 'oldest':
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
        die(json_encode($opinionsArray));
    }

    /**
     * Creates array with sorted comment list
     */
    public function sortUserCommentsAction()
    {
        $params = $this->getRequest()->getParams();
        $limit = $params['countPerPage'];
        $curPage = $params['pageNumber'];

        if ($params['sortBy'] == 'oldest') {
            $order = 'ASC';
        } else {
            $order = 'DESC';
        }

        $recommendationBlock = $this->getLayout()->createBlock('tim_recommendation/recommendation');
        $commentData = $recommendationBlock->getOpinionComment($params['userId'], $limit, $curPage, $order);

        die(json_encode($commentData));
    }

    /**
     * Collect array of opinions
     * @param (arr) $recomIdSet
     * @return (arr) mixed
     */
    protected function _getOpinionsArray($recomIdSet)
    {
        $i = 0;
        foreach ($recomIdSet as $opinion) {
            $recommendationBlock = $this->getLayout()->createBlock('tim_recommendation/recommendation');
            $recomIdSet[$i]['opinionData'] = $recommendationBlock->getOpinionData($opinion['recom_id']);
            $recomIdSet[$i]['opinionData']['youtubeVideoId'] = $recommendationBlock->parseYoutubeUrl($recomIdSet[$i]['opinionData']['movie_url']);
            $recomIdSet[$i]['userData'] = $recommendationBlock->getUserData($recomIdSet[$i]['opinionData']['user_id']);
            $i++;
        }
        return $recomIdSet;
    }
}