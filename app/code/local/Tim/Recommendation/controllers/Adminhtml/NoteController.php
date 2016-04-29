<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Adminhtml_NoteController. Actions for note logic.
 *
 * @category  Tim
 * @package   Tim_Recommendation
 * @author    Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Adminhtml_NoteController extends Mage_Adminhtml_Controller_Action
{
    /**
     * Set popup content
     */
    public function showNotePopupAction()
    {
        $this->loadLayout();
        $noteBody = $this->getLayout()->createBlock('tim_recommendation/adminhtml_note')->toHtml();
        $this->getResponse()->setBody($noteBody);
    }

    /**
     * Save note
     */
    public function addNoteAction()
    {
        $recomId = $this->getRequest()->getParam('recomId');
        $noteText = $this->getRequest()->getParam('noteText');
        $adminId = $this->getRequest()->getParam('adminId');
        $objectName = $this->getRequest()->getParam('objectName');
        $noteModel = Mage::getModel('tim_recommendation/note')
            ->setObjectName($objectName)
            ->setObjectId($recomId)
            ->setNote($noteText)
            ->setDateAdd(date('Y-m-d H:i:s'))
            ->setAdminId($adminId);
        try {
            $noteModel->save();
            //response for ajax request
            echo true;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
        }
    }

    /**
     * Check is allowed access to action
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}