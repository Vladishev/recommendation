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
            array('product_name'=>'value', "Akceptacja" =>"IF (main_table.acceptance =1,'TAK','NIE')"));
        $collection->getSelect()->joinLeft(array('tru' =>'tim_recom_user'),
            'main_table.user_id = tru.customer_id',
            array('user_type','engage'));
        $collection->getSelect()->joinLeft(array('tut' =>'tim_user_type'),
            'tru.user_type = tut.user_type_id',
            array('user_type_name' => 'name'));
        $collection->getSelect()->where('main_table.parent IS NULL');
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
            'index' => 'user_type_name',
            'filter_index' => 'tut.name'
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
        $this->addColumn('Akceptacja', array(
            'header' => Mage::helper('tim_recommendation')->__('Akceptacja'),
            'width' => '20',
            'index' => 'Akceptacja',
            'filter_condition_callback' => array($this, '_acceptanceFilter'),
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

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
        if ($value = $column->getFilter()->getValue())
        {
            if($value == 'TAK')
            {
                $this->getCollection()->getSelect()->where(
                    "main_table.acceptance = 1");
                return $this;
            }
            if($value == 'NIE')
            {
                $this->getCollection()->getSelect()->where(
                    "main_table.acceptance = 0");
                return $this;
            }
        }else{
            return $this;
        }
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('recom_id');
        $this->getMassactionBlock()->setFormFieldName('acceptance');

        $this->getMassactionBlock()->addItem('yes', array(
            'label'=> Mage::helper('tim_recommendation')->__('Akceptacja Tak'),
            'url'  => $this->getUrl('*/*/massAcceptanceYes', array('' => '')),
            'confirm' => Mage::helper('tim_recommendation')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('no', array(
            'label'=> Mage::helper('tim_recommendation')->__('Akceptacja Nie'),
            'url'  => $this->getUrl('*/*/massAcceptanceNo', array('' => '')),
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