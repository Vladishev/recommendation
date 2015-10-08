<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {

    }

    public function addAction()
    {
        $params = $this->getRequest()->getParams();
        $customerId = $params['customer_id'];
        $productId = $params['product_id'];
        $opinionTitle = $params['opinion-title'];
        $opinionAdvantages = $params['opinion-advantages'];
        $opinionDisadvantages = $params['opinion-disadvantages'];
        $opinionSummary = $params['opinion-summary'];
        $itemValueToMoney = $params['itemValuetomoney'];
        $itemDurability = $params['itemDurability'];
        $itemFailure = $params['itemFailure'];
        $itemEaseOfInstall = $params['itemEaseofinstall'];
        $itemDoYouReccomend = $params['itemDoyoureccomend'];
//        $linkToYoutube = $params['link_to_youtube'];
//        $timEmail = $params['tim-email'];
//        $timPassword = $params['tim-password'];
//        $timNick = $params['tim-nick'];

        $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
            ->setUserId($customerId)
            ->setProductId($productId)
            ->setTitle($opinionTitle)
            ->setAdvantages($opinionAdvantages)
            ->setDefects($opinionDisadvantages)
            ->setConclusion($opinionSummary)
            ->setRatingPrice($itemValueToMoney)
            ->setRatingDurability($itemDurability)
            ->setRatingFailure($itemFailure)
            ->setRatingService($itemEaseOfInstall)
            ->setRecomend($itemDoYouReccomend);
        try {
            $recommendationModel->save();
            Mage::getSingleton('core/session')->addSuccess(Mage::helper('tim_recommendation')->__('Opinion was successfully added.'));
        } catch (Exception $e) {
            Mage::log($e->getMessage(), NULL, 'tim_recommendation.log');
            Mage::getSingleton('core/session')->addError(Mage::helper('tim_recommendation')->__('Can\'t add opinion.'));
        }
        $this->_redirectReferer();
//        Save img to table
//        if (isset($_FILES['tim-recommendation-img']['name']) && $_FILES['tim-recommendation-img']['name'] != '') {
//            try {
//                $path = Mage::getBaseDir('media') . DS . 'tim' . DS . 'recommendation';
//                if(!is_dir($path)){
//                    mkdir($path, 0777, true);
//                }
//                $fileName = $_FILES['tim-recommendation-img']['name'] . time();
//                $pathToFile = 'media' . DS . 'tim' . DS . 'recommendation' . DS . $fileName;
//                $uploader = new Varien_File_Uploader('tim-recommendation-img');
//                $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
//                $uploader->setAllowRenameFiles(false);
//                $uploader->setFilesDispersion(false);
//                $uploader->save($path, $logoName);
//
//            } catch (Exception $e) {
//
//            }
//        }
    }

    public function landingAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function viewAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }
}
