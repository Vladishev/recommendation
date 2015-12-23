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
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/malpractice')->getCollection();
        $collection->getSelect()->joinLeft(array('tr' => 'tim_recommendation'), 'main_table.recom_id= tr.recom_id');
        $collection->getSelect()->joinLeft(array('tru' => 'tim_recom_user'), 'main_table.user_id = tru.customer_id', array('nick'));
        $collection->getSelect()->joinLeft(array('cev' => 'customer_entity_varchar'),
            "cev.entity_id = main_table.user_id AND cev.attribute_id = 5", array('customer_firstname' => 'value'));
        $collection->getSelect()->joinLeft(array('cev1' => 'customer_entity_varchar'),
            "cev1.entity_id = main_table.user_id AND cev1.attribute_id = 7", array('customer_lastname' => 'value'));
        $this->setCollection($collection);
//var_dump($collection->getData());die;
        echo $collection->getSelect();
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
//            'sortable' => false
        ));
        $this->addColumn('recom_id', array(
            'header' => Mage::helper('tim_recommendation')->__('Comment ID / Opinion ID'),
            'width' => '100',
            'index' => 'recom_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_RenderCommentOpinion',
//            'sortable' => false
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_recommendation')->__('Customer name'),
            'width' => '100',
            'index' => 'user_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_CustomerNameNickname',
//            'filter_index' => 'CONCAT(cev.value, \' \', cev1.value, \' \', tru.nick)'
            'filter_index' => 'cev.value'
        ));
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('date_add'),
            'width' => '10',
            'type' => 'datetime',
            'index' => 'date_add',
            'filter_index' => 'date_add',
            'sortable' => true
        ));
        $this->addColumn('comment', array(
            'header' => Mage::helper('tim_recommendation')->__('comment'),
            'width' => '100',
            'index' => 'comment',
            'filter_index' => 'comment',
            'sortable' => true
        ));
        $this->addColumn('tim_ip', array(
            'header' => Mage::helper('tim_recommendation')->__('IP'),
            'width' => '10',
            'index' => 'tim_ip',
            'filter_index' => 'tim_ip',
            'sortable' => true
        ));
        $this->addColumn('tim_host', array(
            'header' => Mage::helper('tim_recommendation')->__('host'),
            'width' => '15',
            'index' => 'tim_host',
            'filter_index' => 'tim_host',
            'sortable' => true
        ));
        $this->addColumn('detail',
            array(
                'header' => Mage::helper('tim_recommendation')->__('Detail'),
                'width' => '70',
                'type' => 'action',
                'getter' => 'getRecomId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('tim_recommendation')->__('Detail'),
                        'url' => array('base' => '*/opinionReport/opinionInfo'),
                        'target' => '_blank',
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
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
//                $collection->getSelect()->joinInner(array('tr' => 'tim_recommendation'), 'main_table.recom_id= tr.recom_id');
                $collection->getSelect()->where('tr.parent IS NULL');
                return $collection;
            }
            if ($value == 'Comment') {
//                $collection->getSelect()->joinInner(array('tr' => 'tim_recommendation'), 'main_table.recom_id= tr.recom_id');
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