<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy (vladomsu@gmail.com)
 */
class Tim_Recommendation_Block_Adminhtml_MalpracticeReport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_recommendation_malpractice_grid');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/malpractice')->getCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('malpractice_id', array(
            'header' => Mage::helper('tim_recommendation')->__('malpractice_id'),
            'width' => '10',
            'index' => 'malpractice_id',
            'filter_index' => 'malpractice_id',
            'sortable' => true
        ));
        $this->addColumn('recom_id', array(
            'header' => Mage::helper('tim_recommendation')->__('recom_id'),
            'width' => '100',
            'index' => 'recom_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_RenderCommentOpinion',
            'filter' => false,
            'sortable' => false
        ));
        /*$this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('date_add'),
            'width' => '10',
            'type' => 'datetime',
            'index' => 'date_add',
            'filter_index' => 'date_add',
            'sortable' => false
        ));
        $this->addColumn('sku', array(
            'header' => Mage::helper('tim_recommendation')->__('sku'),
            'width' => '10',
            'index' => 'sku',
            'filter_index' => 'sku',
            'sortable' => false
        ));
        $this->addColumn('value', array(
            'header' => Mage::helper('tim_recommendation')->__('value'),
            'width' => '30',
            'index' => 'value',
            'filter_index' => 'value',
            'sortable' => false
        ));
        $this->addColumn('images', array(
            'header' => Mage::helper('tim_recommendation')->__('images'),
            'width' => '5',
            'index' => 'recom_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_ProductData',
            'filter' => false,
            'sortable' => false
        ));
        $this->addColumn('user_id', array(
            'header' => Mage::helper('tim_recommendation')->__('user_id'),
            'width' => '10',
            'index' => 'user_id',
            'filter_index' => 'user_id',
            'sortable' => false
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_recommendation')->__('name'),
            'width' => '30',
            'index' => 'name',
            'filter_index' => 'name',
            'sortable' => false
        ));
        $this->addColumn('engage', array(
            'header' => Mage::helper('tim_recommendation')->__('engage'),
            'width' => '10',
            'index' => 'engage',
            'filter_index' => 'engage',
            'sortable' => false
        ));
        $this->addColumn('ocena_produktu', array(
            'header' => Mage::helper('tim_recommendation')->__('Ocena produktu'),
            'width' => '5',
            'index' => 'recom_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_Average',
            'filter' => false,
            'sortable' => false
        ));
        $this->addColumn('rating_price', array(
            'header' => Mage::helper('tim_recommendation')->__('rating_price'),
            'width' => '5',
            'index' => 'rating_price',
            'filter_index' => 'rating_price',
            'sortable' => false
        ));
        $this->addColumn('rating_durability', array(
            'header' => Mage::helper('tim_recommendation')->__('rating_durability'),
            'width' => '5',
            'index' => 'rating_durability',
            'filter_index' => 'rating_durability',
            'sortable' => false
        ));
        $this->addColumn('rating_failure', array(
            'header' => Mage::helper('tim_recommendation')->__('rating_failure'),
            'width' => '5',
            'index' => 'rating_failure',
            'filter_index' => 'rating_failure',
            'sortable' => false
        ));
        $this->addColumn('rating_service', array(
            'header' => Mage::helper('tim_recommendation')->__('rating_service'),
            'width' => '5',
            'index' => 'rating_service',
            'filter_index' => 'rating_service',
            'sortable' => false
        ));
        $this->addColumn('advantages', array(
            'header' => Mage::helper('tim_recommendation')->__('advantages'),
            'width' => '100',
            'index' => 'advantages',
            'filter_index' => 'advantages',
            'sortable' => false
        ));
        $this->addColumn('defects', array(
            'header' => Mage::helper('tim_recommendation')->__('defects'),
            'width' => '100',
            'index' => 'defects',
            'filter_index' => 'defects',
            'sortable' => false
        ));
        $this->addColumn('conclusion', array(
            'header' => Mage::helper('tim_recommendation')->__('conclusion'),
            'width' => '100',
            'index' => 'conclusion',
            'filter_index' => 'conclusion',
            'sortable' => false
        ));
        $this->addColumn('comment', array(
            'header' => Mage::helper('tim_recommendation')->__('comment'),
            'width' => '100',
            'index' => 'comment',
            'filter_index' => 'comment',
            'sortable' => false
        ));*/

        $this->addExportType('*/*/exportCsv', Mage::helper('sales')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('sales')->__('Excel XML'));

        return parent::_prepareColumns();
    }
}