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
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->getSelect()->joinLeft(array('cpe' => 'catalog_product_entity'), 'main_table.product_id = cpe.entity_id',
            array('sku'));
        $collection->getSelect()->joinLeft(array('cpef' =>'catalog_product_entity_varchar'),
            'main_table.product_id = cpef.entity_id AND cpef.attribute_id = 71',
            array('product_name'=>'value'));
        $collection->getSelect()->joinLeft(array('tru' =>'tim_recom_user'),
            'main_table.user_id = tru.customer_id',
            array('user_type','engage'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Tim_Recommendation_Block_Adminhtml_Report_Grid
     */
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
            'index' => 'user_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_CustomerName',
            'filter' => false
        ));
        $this->addColumn('product_sku', array(
            'header' => Mage::helper('tim_recommendation')->__('Product SKU'),
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
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('Date Added'),
            'index' => 'date_add',
            'type' => 'datetime',
            'width' => '100'
        ));
        $this->addColumn('comments', array(
            'header' => Mage::helper('tim_recommendation')->__('Comments'),
            'width' => '200',
            'index' => 'recom_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_Comments',
            'filter' => false
        ));
        $this->addColumn('media', array(
            'header' => Mage::helper('tim_recommendation')->__('Media'),
            'width' => '30',
            'index' => 'product_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_ProductData',
            'filter' => false
        ));
        $this->addColumn('user_type', array(
            'header' => Mage::helper('tim_recommendation')->__('User type'),
            'width' => '50',
            'index' => 'user_type',
            'filter_index' => 'user_type'
        ));
        $this->addColumn('user_level', array(
            'header' => Mage::helper('tim_recommendation')->__('User level'),
            'width' => '50',
            'index' => 'engage',
            'filter_index' => 'engage'
        ));
        $this->addColumn('display_opinion',
            array(
                'header'    =>  Mage::helper('tim_recommendation')->__('Display opinion'),
                'width'     => '70',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('tim_recommendation')->__('Opinion'),
                        'onclick' => 'alert("Soon you\'ll see the opinion view");'
//                        'url'       => array('base'=> '*/*/edit'),
//                        'field'     => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
            ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

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