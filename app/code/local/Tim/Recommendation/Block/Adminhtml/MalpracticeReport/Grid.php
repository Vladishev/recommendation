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
        $this->addColumn('user_id', array(
            'header' => Mage::helper('tim_recommendation')->__('user_id'),
            'width' => '30',
            'index' => 'user_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_CustomerName',
            'filter' => false,
            'sortable' => false
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
}