<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_Note_Form
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_Note_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Prepare form before rendering HTML
     */
    protected function _prepareForm()
    {
        /* Adding form for popup */
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
