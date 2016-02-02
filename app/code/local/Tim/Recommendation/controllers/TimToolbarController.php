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
    public function showAction()
    {
        $params = $this->getRequest()->getParams();
        $limit = 10;
        $curPage = 1;
        switch ($params['sortBy']) {
            case 'topRated':
                $order = 'DESC';
                $field = 'average_rating';
                break;
            case 'lowRated':
                $order = 'ASC';
                $field = 'average_rating';
                break;
            default:
                $order = 'DESC';
                $field = 'date_add';
        }
        $recomIdSet = Mage::getModel('tim_recommendation/index')->getOpinionsForProduct($params['productId'], $limit, $curPage, $order, $field);
        $opinionsArray = $this->_getOpinionsArray($recomIdSet);

        echo json_encode($opinionsArray);
    }

    protected function _getOpinionsArray($recomIdSet)
    {
        $i = 0;
        foreach ($recomIdSet as $opinion) {
            $recommendationBlock =$this->getLayout()->createBlock('tim_recommendation/recommendation');
            $recomIdSet[$i]['opinionData'] = $recommendationBlock->getOpinionData($opinion['recom_id']);
            $i++;
        }
        return $recomIdSet;
    }
}