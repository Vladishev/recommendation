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
        $this->setDefaultSort('date_add');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/malpractice')->getCollection();
        $collection->getSelect()->joinLeft(array('tru' => 'tim_recom_user'), 'main_table.user_id = tru.customer_id', array('nick'));
        $collection->getSelect()->joinLeft(array('cev' => 'customer_entity_varchar'),
            "cev.entity_id = main_table.user_id AND cev.attribute_id = 5", array('customer_firstname' => 'value'));
        $collection->getSelect()->joinLeft(array('cev1' => 'customer_entity_varchar'),
            "cev1.entity_id = main_table.user_id AND cev1.attribute_id = 7", array('customer_lastname' => 'value'));
        $collection->getSelect()->joinLeft(array('tr' => 'tim_recommendation'), 'main_table.recom_id= tr.recom_id', array('parent'));
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('malpractice_id', array(
            'header' => Mage::helper('tim_recommendation')->__('ID'),
            'width' => '10',
            'index' => 'malpractice_id',
            'filter_index' => 'malpractice_id',
            'align' => 'center',
            'sortable' => true
        ));
        $this->addColumn('opinion_comment', array(
            'header' => Mage::helper('tim_recommendation')->__('Opinion/Comment'),
            'width' => '10',
            'index' => 'parent',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_RenderTitle',
            'type' => 'options',
            'align' => 'center',
            'options' => array('Opinion' => $this->__('Opinion'), 'Comment' => $this->__('Comment')),
            'filter_condition_callback' => array($this, '_opinionRecomFilter'),
        ));
        $this->addColumn('recom_id', array(
            'header' => Mage::helper('tim_recommendation')->__('Comment ID / Opinion ID'),
            'width' => '100',
            'index' => 'main_table.recom_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_RenderCommentOpinion',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_recommendation')->__('Customer name'),
            'width' => '100',
            'index' => 'user_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_CustomerNameNickname',
            'filter_index' => 'CONCAT(cev.value, \' \', cev1.value, \' \', tru.nick)'
        ));
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('Date Added'),
            'width' => '10',
            'type' => 'datetime',
            'index' => 'date_add',
            'filter_index' => 'main_table.date_add',
            'sortable' => true,
            'filter_time' => true,
        ));
        $this->addColumn('malpractice_text', array(
            'header' => Mage::helper('tim_recommendation')->__('Malpractice text'),
            'width' => '100',
            'index' => 'comment',
            'filter_index' => 'main_table.comment',
            'sortable' => true
        ));
        $this->addColumn('tim_ip', array(
            'header' => Mage::helper('tim_recommendation')->__('IP'),
            'width' => '10',
            'index' => 'tim_ip',
            'filter_index' => 'main_table.tim_ip',
            'sortable' => true
        ));
        $this->addColumn('tim_host', array(
            'header' => Mage::helper('tim_recommendation')->__('host'),
            'width' => '15',
            'index' => 'tim_host',
            'filter_index' => 'main_table.tim_host',
            'sortable' => true
        ));
        $this->addColumn('detail', array(
            'header' => Mage::helper('tim_recommendation')->__('Detail'),
            'width' => '70',
            'type' => 'text',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_RenderDetailActions',
            'index' => 'recom_id',
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
    protected function _opinionRecomFilter($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            if ($value == 'Opinion') {
                $collection->getSelect()->where('tr.parent IS NULL');
                return $collection;
            }
            if ($value == 'Comment') {
                $collection->getSelect()->where('tr.parent IS NOT NULL');
                return $collection;
            }
        } else {
            return $collection;
        }
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }
}