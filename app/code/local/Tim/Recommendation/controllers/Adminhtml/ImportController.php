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
        $_helper = Mage::helper('tim_recommendation');
        $file = $_helper->getImportFilePath();
        $extension = pathinfo($file, PATHINFO_EXTENSION);
        $allowedExtensions = $_helper->getAllowedExtensions();
        if (in_array($extension, $allowedExtensions)) {
            switch ($extension) {
                case $allowedExtensions['xml']:
                    Mage::getModel('tim_recommendation/import')->saveImportData($allowedExtensions['xml']);
                    break;
                case $allowedExtensions['csv']:
                    Mage::getModel('tim_recommendation/import')->saveImportData($allowedExtensions['csv']);
                    break;
            }
        } else {
            Mage::getSingleton('core/session')->addError($_helper->__('This is not allowed file extension!'));
        }
    }
}