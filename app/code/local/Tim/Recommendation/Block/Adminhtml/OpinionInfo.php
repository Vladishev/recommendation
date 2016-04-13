<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_OpinionInfo extends Mage_Adminhtml_Block_Template
{
    /**
     * Get array with opinion data
     * @return array
     */
    public function getOpinionData()
    {
        $recomId = $this->getRequest()->getParam('id');
        $checkedId = Mage::helper('tim_recommendation')->checkForOpinionComment($recomId);
        $opinion = Mage::getModel('tim_recommendation/recommendation')->load($checkedId)->getData();
        $opinionMedia = Mage::helper('tim_recommendation')->getOpinionMediaPath($checkedId);
        $product = Mage::getModel('catalog/product')->load($opinion['product_id']);
        $opinion['sku'] = $product->getSku();
        $opinion['product_name'] = $product->getName();
        $opinion['movie_url'] = '';
        if (!empty($opinionMedia['url/youtube'])) {
            $opinion['movie_url'] = $opinionMedia['url/youtube'];
            unset($opinionMedia['url/youtube']);
        }
        $opinion['images'] = $opinionMedia;

        return $opinion;
    }

    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(
                    array(
                        'label' => Mage::helper('tim_recommendation')->__('Back'),
                        'onclick' => "window.location.href = '" . $this->getUrl('*/*') . "'",
                        'class' => 'back'
                    )
                )
        );
        $this->setChild('add_note_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('tim_recommendation')->__('Note'),
                    'onclick' => 'addNotePopup();',
                    'class' => 'task'
                ))
        );
        $this->setChild('modify_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('tim_recommendation')->__('Modify'),
                    'onclick' => 'window.location = \'' . $this->getUrl('*/*/modifyOpinion', array(
                            'acceptance' => $this->getRequest()->getParam('id')
                        )) . '\'',
                    'class' => 'task'
                ))
        );
        $this->setChild('not_accept_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('tim_recommendation')->__('Not accept'),
                    'onclick' => 'window.location = \'' . $this->getUrl('*/*/massAcceptanceNo', array(
                            'acceptance' => $this->getRequest()->getParam('id')
                        )) . '\'',
                    'class' => 'task'
                ))
        );
        $this->setChild('accept_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label' => Mage::helper('tim_recommendation')->__('Accept'),
                    'onclick' => 'window.location = \'' . $this->getUrl('*/*/massAcceptanceYes', array(
                            'acceptance' => $this->getRequest()->getParam('id')
                        )) . '\'',
                    'class' => 'task'
                ))
        );

        return parent::_prepareLayout();
    }

    /**
     * Retrieve Back Button HTML
     *
     * @return string
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Retrieve Add note Button HTML
     *
     * @return string
     */
    public function getAddNoteButtonHtml()
    {
        return $this->getChildHtml('add_note_button');
    }

    /**
     * Retrieve Modify Button HTML
     *
     * @return string
     */
    public function getModifyButtonHtml()
    {
        return $this->getChildHtml('modify_button');
    }

    /**
     * Retrieve Not accept Button HTML
     *
     * @return string
     */
    public function getNotAcceptButtonHtml()
    {
        return $this->getChildHtml('not_accept_button');
    }

    /**
     * Retrieve Accept Button HTML
     *
     * @return string
     */
    public function getAcceptButtonHtml()
    {
        return $this->getChildHtml('accept_button');
    }
}