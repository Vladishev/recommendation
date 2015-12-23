<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_NoteReport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_recommendation_note_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('tim_recommendation/note')->getCollection();
        $collection->addFieldToFilter('object_id',$id);
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     * @return Tim_Recommendation_Block_Adminhtml_NoteReport_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('admin_username', array(
            'header' => Mage::helper('tim_recommendation')->__('Admin'),
            'width' => '30',
            'index' => 'admin_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_AdminUsername',
            'filter' => false
        ));
        $this->addColumn('note', array(
            'header' => Mage::helper('tim_recommendation')->__('Note'),
            'width' => '200',
            'index' => 'note',
            'filter_index' => 'main_table.note',

        ));
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('Date Added'),
            'index' => 'date_add',
            'type' => 'datetime',
            'width' => '100',
            'filter_index' => 'main_table.date_add',
            'filter_time' => true,
        ));

        return parent::_prepareColumns();
    }

    /**
     * Returns a grid URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}