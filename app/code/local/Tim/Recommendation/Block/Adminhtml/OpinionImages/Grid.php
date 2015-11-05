<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_OpinionImages_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('tim/widget/opinionImagesGrid.phtml');
        $this->setId('tim_recommendation_opinion_images_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/media')->getCollection();
        $collection->getSelect()->joinLeft(array('tr' => 'tim_recommendation'), 'main_table.recom_id = tr.recom_id',
            array('date_add'));
        $collection->getSelect()->where('main_table.type = "image/png"');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * @return Tim_Recommendation_Block_Adminhtml_OpinionImages_Grid
     */
    protected function _prepareColumns()
    {
//        $this->addColumn('recom_id', array(
//            'header' => Mage::helper('tim_recommendation')->__('ID'),
//            'width' => '10',
//            'index' => 'recom_id',
//        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_recommendation')->__('Name'),
            'width' => '100',
            'index' => 'name',
        ));
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('Date Added'),
            'index' => 'date_add',
            'type' => 'datetime',
            'width' => '100'
        ));

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/opinionReport/opinionInfo', array('id'=>$row->getRecomId()));
    }

    /**
     * Returns a grid URL
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}