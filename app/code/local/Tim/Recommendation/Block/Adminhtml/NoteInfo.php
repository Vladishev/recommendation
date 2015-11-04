<?php

class Tim_Recommendation_Block_Adminhtml_NoteInfo extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Get array with note data
     * @return array
     */
    public function getNoteData()
    {
        $objectId = $this->getRequest()->getParam('id');
        $noteCollection = Mage::getModel('tim_recommendation/note')->getCollection();
        $noteCollection->addFieldToFilter('object_id', $objectId);
        $noteCollection->setOrder('date_add', 'DESC');
        $notes = $noteCollection->getData();

        //add admin username to collection
        $i = 0;
        foreach ($notes as $note) {
            $notes[$i]['admin_username'] = Mage::getModel('admin/user')->load($note['admin_id'])->getUsername();
            $i++;
        }

        return $notes;
    }
}