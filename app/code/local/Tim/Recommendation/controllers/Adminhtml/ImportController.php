<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action
{
    public function importAction()
    {
        $result = array(
            'message' => '',
            'success' => false,
        );

        $_helper = Mage::helper('tim_recommendation');
        $file = $_helper->getImportFilePath();
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $allowedExtensions = $_helper->getAllowedExtensions();
        if (in_array($extension, $allowedExtensions)) {
            switch ($extension) {
                case $allowedExtensions['xml']:
                    $response = Mage::getModel('tim_recommendation/import')->saveImportData($allowedExtensions['xml']);
                    break;
                case $allowedExtensions['csv']:
                    $response = Mage::getModel('tim_recommendation/import')->saveImportData($allowedExtensions['csv']);
                    break;
            }
            if ($response['success'] == true) {
                $result['message'] = $_helper->__('New data was successfully added.');
                $result['success'] = true;
            } elseif ($response['success'] == false) {
                $result['message'] = $_helper->__('Can\'t add new data.');
            }
        } else {
            $result['message'] = $_helper->__('This is not allowed file extension!');
        }

        $messageBlock = Mage::getBlockSingleton('core/messages');
        if ($result['success']) {
            $messageBlock->addSuccess($result['message']);
        } else {
            $messageBlock->addError($result['message']);
        }

        $result['html'] = $messageBlock->getGroupedHtml();
        $this->getResponse()->setBody(json_encode($result));
    }
}