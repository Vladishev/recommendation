<?php

class Tim_Recommendation_Block_Adminhtml_Note extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        /* sets data for popup */
        parent::__construct();
        $this->_blockGroup = 'tim_recommendation';
        $this->_controller = 'adminhtml';
        $this->_mode = 'note';
        $this->_headerText = '';

        $this->removeButton('reset');
        $this->removeButton('back');
        $this->_updateButton('save', 'label', $this->helper('tim_recommendation')->__('Save'));
        $this->_updateButton('save', 'class', 'tim_recommendation');
        $this->_updateButton('save', 'id', 'tim_recommendation');
        $this->_updateButton('save', 'onclick', "saveNote();");

        $this->_formScripts[] = "function saveNote() {
            var recom = parent.document.getElementById('recomId');
            var recomId = recom.value;
            var note = document.getElementById('note');
            var adminId = document.getElementById('admin_id').value;
            var noteText = note.value;
            if (noteText == '') { alert('" . $this->helper('tim_recommendation')->__('Please enter note text.') . "'); return false;}
            var xmlhttp;
            xmlhttp = new XMLHttpRequest();
            var body = '?recomId=' + encodeURIComponent(recomId) +
            '&noteText=' + encodeURIComponent(noteText) +
            '&adminId=' + encodeURIComponent(adminId);
            var url = '" . $this->getUrl('adminhtml/note/addNote', array('_secure' => true)) . "'+body;

            xmlhttp.open('GET', url, true)
            xmlhttp.onreadystatechange = function() {

                if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                    if(xmlhttp.responseText == true){
                        alert('Notka została dodana.');
                        parent.closePopup();
                    }else{
                        alert('Uwaga nie została dodana. Proszę sprawdzić rejestr i spróbuj ponownie.');
                    }
                }
            }
            xmlhttp.send();
        }";
    }

    protected function _prepareLayout()
    {
        $this->setChild('form', $this->getLayout()->createBlock('tim_recommendation/adminhtml_note_form'));
    }

    protected function _toHtml()
    {
        $css = file_get_contents(Mage::getBaseDir('skin') . '/adminhtml/default/default/css/tim/recommendation/style.css');
        $content = parent::_toHtml();
        return '<style>' . $css . '</style>' . $content;
    }
}
