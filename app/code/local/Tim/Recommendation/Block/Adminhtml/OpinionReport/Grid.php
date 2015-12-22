<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_OpinionReport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_recommendation_opinion_grid');
        $this->setDefaultSort('recom_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->getSelect()->joinLeft(array('cpe' => 'catalog_product_entity'), 'main_table.product_id = cpe.entity_id',
            array('sku'));
        $collection->getSelect()->joinLeft(array('cpef' => 'catalog_product_entity_varchar'),
            'main_table.product_id = cpef.entity_id AND cpef.attribute_id = 71',//71 = name
            array('product_name' => 'value'));
        $collection->getSelect()->joinLeft(array('tru' => 'tim_recom_user'),
            'main_table.user_id = tru.customer_id',
            array('user_type', 'engage', 'nick'));
        $collection->getSelect()->joinLeft(array('tut' => 'tim_user_type'),
            'tru.user_type = tut.user_type_id',
            array('user_type_name' => 'name'));
        $collection->getSelect()->joinLeft(array('cpei' => 'catalog_product_entity_int'),
            'main_table.product_id = cpei.entity_id AND cpei.attribute_id = 81',//81 = manufacturer
            array('manufacturer_id' => 'value'));
        $collection->getSelect()->joinLeft(array('eaov' => 'eav_attribute_option_value'), 'cpei.value = eaov.option_id',
            array('manufacturer_name' => 'value'));
        $collection->getSelect()->joinLeft(array('cev' => 'customer_entity_varchar'),
            "cev.entity_id = main_table.user_id AND cev.attribute_id = 5", array('customer_firstname' => 'value'));
        $collection->getSelect()->joinLeft(array('cev1' => 'customer_entity_varchar'),
            "cev1.entity_id = main_table.user_id AND cev1.attribute_id = 7", array('customer_lastname' => 'value'));
        $collection->getSelect()->where('main_table.parent IS NULL');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Tim_Recommendation_Block_Adminhtml_OpinionReport_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('recom_id', array(
            'header' => Mage::helper('tim_recommendation')->__('ID'),
            'width' => '10',
            'index' => 'recom_id',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_recommendation')->__('Customer Name'),
            'width' => '100',
            'index' => 'user_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_CustomerNameNickname',
            'filter_index' => 'CONCAT(cev.value, \' \', cev1.value, \' \', tru.nick)'
        ));
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('tim_recommendation')->__('Index TIM'),
            'width' => '100',
            'index' => 'sku',
            'filter_index' => 'sku'
        ));
        $this->addColumn('product_name', array(
            'header' => Mage::helper('tim_recommendation')->__('Product Name'),
            'width' => '150',
            'index' => 'product_name',
            'filter_index' => 'cpef.value'
        ));
        $this->addColumn('manufacturer_name', array(
            'header' => Mage::helper('tim_recommendation')->__('Manufacturer'),
            'width' => '100',
            'index' => 'manufacturer_name',
            'filter_index' => 'eaov.value'
        ));
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('Date Added'),
            'index' => 'date_add',
            'type' => 'datetime',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_DateFormat',
            'width' => '100'
        ));
        $this->addColumn('comments', array(
            'header' => Mage::helper('tim_recommendation')->__('Comments'),
            'width' => '200',
            'index' => 'advantages',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_Comments',
        ));
        $this->addColumn('media', array(
            'header' => Mage::helper('tim_recommendation')->__('Media'),
            'width' => '30',
            'index' => 'recom_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_RecomMedia',
            'type' => 'options',
            'options' => array('No' => $this->__('No'), 'Yes' => $this->__('Yes')),
            'filter_condition_callback' => array($this, '_mediaFilter'),
            'sortable' => false
        ));
        $this->addColumn('user_type', array(
            'header' => Mage::helper('tim_recommendation')->__('User type'),
            'width' => '50',
            'index' => 'user_type_name',
            'filter_index' => 'tut.name',
            'type' => 'options',
            'options' => Mage::helper('tim_recommendation')->getCustomerTypeName(),
        ));
        $this->addColumn('user_level', array(
            'header' => Mage::helper('tim_recommendation')->__('User level'),
            'width' => '50',
            'index' => 'engage',
            'filter_index' => 'engage'
        ));
        $this->addColumn('tim_ip', array(
            'header' => Mage::helper('tim_recommendation')->__('IP'),
            'width' => '50',
            'index' => 'tim_ip',
            'filter_index' => 'tim_ip'
        ));
        $this->addColumn('tim_host', array(
            'header' => Mage::helper('tim_recommendation')->__('HOST'),
            'width' => '50',
            'index' => 'tim_host',
            'filter_index' => 'tim_host'
        ));
        $this->addColumn('acceptance', array(
            'header' => Mage::helper('tim_recommendation')->__('Acceptance'),
            'width' => '20',
            'index' => 'acceptance',
            'filter_index' => 'main_table.acceptance',
            'type' => 'options',
            'options' => array(0 => $this->__('No'), 1 => $this->__('Yes')),
        ));
        $this->addColumn('display_opinion',
            array(
                'header' => Mage::helper('tim_recommendation')->__('Operation'),
                'width' => '70',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('tim_recommendation')->__('Display opinion'),
                        'url' => array('base' => '*/*/opinionInfo'),
                        'target' => '_blank',
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
            ));
        $this->addColumn('add_note', array(
            'header' => Mage::helper('tim_recommendation')->__('Operation'),
            'width' => '50',
            'index' => 'recom_id',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_AddNote',
        ));
        $this->addColumn('display_note',
            array(
                'header' => Mage::helper('tim_recommendation')->__('Operation'),
                'width' => '70',
                'index' => 'recom_id',
                'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_DisplayNote',
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Custom filter for media field
     * @param (obj)$collection
     * @param (obj)$column
     * @return $this
     */
    protected function _mediaFilter($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            if ($value == 'Yes') {
                $collection->getSelect()->joinInner(array('trm' => 'tim_recom_media'), 'main_table.recom_id= trm.recom_id',
                    array('media_recom_id' => 'recom_id'));
                $collection->getSelect()->group('trm.recom_id');
                return $collection;
            }
            if ($value == 'No') {
                $collection->getSelect()->joinLeft(array('trm' => 'tim_recom_media'), 'main_table.recom_id = trm.recom_id',
                    array('media_recom_id' => 'recom_id'));
                $collection->getSelect()->where('trm.recom_id IS NULL');
                return $collection;
            }
        } else {
            return $collection;
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