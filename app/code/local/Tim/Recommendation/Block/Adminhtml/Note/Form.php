<?php

class Tim_Recommendation_Block_Adminhtml_Note_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /* Adding form for popup */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $adminId = Mage::getSingleton('admin/session')->getUser()->getUserId();

        $fieldset = $form->addFieldset('base_note_fieldset', array(
            'legend' => ''
        ));
        $fieldset->addField('note', 'textarea', array(
            'label' => $this->helper('tim_recommendation')->__('Note'),
            'title' => $this->helper('tim_recommendation')->__('Note'),
            'name' => 'note',
            'required' => true,
            'class'     =>  'note-form',
        ));
        $fieldset->addField('admin_id', 'hidden', array(
            'name' => 'admin_id',
            'value' => $adminId,
        ));

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $this->setForm($form);
    }
}
