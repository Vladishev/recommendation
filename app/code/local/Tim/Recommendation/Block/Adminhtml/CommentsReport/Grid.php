<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Block_Adminhtml_CommentsReport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_recommendation_comment_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->getSelect()->join(array('val' => 'customer_entity_varchar'),
            "val.entity_id = main_table.user_id AND val.attribute_id = (SELECT ea.attribute_id FROM eav_attribute ea JOIN eav_entity_type eat ON eat.entity_type_id = ea.entity_type_id WHERE ea.attribute_code IN ('firstname') AND eat.entity_type_code= 'customer')",
            array("Akceptacja" => "IF (main_table.acceptance =1,'TAK','NIE')"));
        $collection->getSelect()->join(array('val1' => 'customer_entity_varchar'),
            "val1.entity_id = main_table.user_id AND val1.attribute_id = (SELECT ea.attribute_id FROM eav_attribute ea JOIN eav_entity_type eat ON eat.entity_type_id = ea.entity_type_id WHERE ea.attribute_code IN ('lastname') AND eat.entity_type_code= 'customer')",
            array("name" => "CONCAT( val.value,' ', val1.value)"));
        $collection->getSelect()->where('main_table.parent IS NOT NULL');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('recom_id', array(
            'header' => Mage::helper('tim_recommendation')->__('ID'),
            'width' => '10',
            'index' => 'recom_id',
            'filter_index' => 'recom_id'
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_recommendation')->__('Customer Name'),
            'width' => '100',
            'index' => 'name',
            'filter' => false
        ));
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('Date Added'),
            'index' => 'date_add',
            'type' => 'datetime',
            'filter_index' => 'date_add',
            'width' => '100',
            'filter_time' => true,
        ));
        $this->addColumn('comments', array(
            'header' => Mage::helper('tim_recommendation')->__('Comment'),
            'width' => '200',
            'index' => 'comment',
            'filter' => false
        ));
        $this->addColumn('Akceptacja', array(
            'header' => Mage::helper('tim_recommendation')->__('Akceptacja'),
            'width' => '20',
            'index' => 'Akceptacja',
            'filter_condition_callback' => array($this, '_acceptanceFilter'),
        ));
        $this->addColumn('add_note', array(
            'width' => '50',
            'index' => 'recom_id',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_AddNote',
        ));
        $this->addColumn('display_note',
            array(
                'width' => '70',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('tim_recommendation')->__('Display note'),
                        'url' => array('base' => '*/noteReport'),
                        'target' => '_blank',
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    /**
     * Custom filter for Akceptacja field
     * @param (obj)$collection
     * @param (obj)$column
     * @return $this
     */
    protected function _acceptanceFilter($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            if ($value == 'TAK') {
                $this->getCollection()->getSelect()->where(
                    "main_table.acceptance = 1");
                return $this;
            }
            if ($value == 'NIE') {
                $this->getCollection()->getSelect()->where(
                    "main_table.acceptance = 0");
                return $this;
            }
        } else {
            return $this;
        }
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('recom_id');
        $this->getMassactionBlock()->setFormFieldName('acceptance');

        $this->getMassactionBlock()->addItem('yes', array(
            'label' => Mage::helper('tim_recommendation')->__('Akceptacja Tak'),
            'url' => $this->getUrl('*/*/massAcceptanceYes', array('' => '')),
            'confirm' => Mage::helper('tim_recommendation')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('no', array(
            'label' => Mage::helper('tim_recommendation')->__('Akceptacja Nie'),
            'url' => $this->getUrl('*/*/massAcceptanceNo', array('' => '')),
            'confirm' => Mage::helper('tim_recommendation')->__('Are you sure?')
        ));

        return $this;
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