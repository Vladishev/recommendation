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

        $recommendationBlock =$this->getLayout()->createBlock('tim_recommendation/recommendation');
        $opinionData = $recommendationBlock->getUserOpinionData($params['userId'], $limit, $curPage, $order, $field);
        $opinionsCount = $recommendationBlock->getUserOpinionCount($params['userId']);
        $opinionData[0]['pagesCount'] = ceil($opinionsCount / $limit);
        $opinionData[0]['curPage'] = $curPage;
        die(json_encode($opinionData));
    }

    public function sortUserCommentsAction()
    {
        $params = $this->getRequest()->getParams();
        $limit = $params['countPerPage'];
        $curPage = $params['pageNumber'];

        switch ($params['sortBy']) {
            case 'oldest':
                $order = 'ASC';
                break;
            default:
                $order = 'DESC';
        }

        $recommendationBlock =$this->getLayout()->createBlock('tim_recommendation/recommendation');
        $commentData = $recommendationBlock->getOpinionComment($params['userId'], $limit, $curPage, $order);

        die(json_encode($commentData));
    }
}