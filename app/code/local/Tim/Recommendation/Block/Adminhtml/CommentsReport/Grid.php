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
        $this->setDefaultSort('date_add');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        if ($recomId = $this->getRequest()->getParam('commentId')) {
            $collection->addFieldToFilter(array('parent'), array( array('eq' => $recomId)));
        }
        $collection->getSelect()->join(array('val' => 'customer_entity_varchar'),
            "val.entity_id = main_table.user_id AND val.attribute_id = (SELECT ea.attribute_id FROM eav_attribute ea JOIN eav_entity_type eat ON eat.entity_type_id = ea.entity_type_id WHERE ea.attribute_code IN ('firstname') AND eat.entity_type_code= 'customer')",
            array("Akceptacja" => "IF (main_table.acceptance =1,'TAK','NIE')"));
        $collection->getSelect()->join(array('val1' => 'customer_entity_varchar'),
            "val1.entity_id = main_table.user_id AND val1.attribute_id = (SELECT ea.attribute_id FROM eav_attribute ea JOIN eav_entity_type eat ON eat.entity_type_id = ea.entity_type_id WHERE ea.attribute_code IN ('lastname') AND eat.entity_type_code= 'customer')",
            array("name" => "CONCAT( val.value,' ', val1.value)"));
        $collection->getSelect()->joinLeft(array('cev' => 'customer_entity_varchar'),
            "cev.entity_id = main_table.user_id AND cev.attribute_id = 5", array('customer_firstname' => 'value'));
        $collection->getSelect()->joinLeft(array('cev1' => 'customer_entity_varchar'),
            "cev1.entity_id = main_table.user_id AND cev1.attribute_id = 7", array('customer_lastname' => 'value'));
        $collection->getSelect()->joinLeft(array('tru' => 'tim_recom_user'),
            'main_table.user_id = tru.customer_id',
            array('nick'));
        $collection->getSelect()->where('main_table.parent IS NOT NULL');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('comment_id', array(
            'header' => Mage::helper('tim_recommendation')->__('Comment ID'),
            'width' => '10',
            'index' => 'recom_id',
        ));
        $this->addColumn('recom_id', array(
            'header' => Mage::helper('tim_recommendation')->__('Recom ID'),
            'width' => '50',
            'index' => 'parent',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_RecommendDesc',
        ));
        $this->addColumn('name', array(
            'header' => Mage::helper('tim_recommendation')->__('Customer Name'),
            'width' => '100',
            'index' => 'user_id',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_CustomerNameNickname',
            'filter_index' => 'CONCAT(cev.value, \' \', cev1.value, \' \', tru.nick)'
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
        ));
        $this->addColumn('acceptance', array(
            'header' => Mage::helper('tim_recommendation')->__('Acceptance'),
            'width' => '20',
            'index' => 'acceptance',
            'filter_index' => 'main_table.acceptance',
            'type' => 'options',
            'options' => array(0 => $this->__('No'), 1 => $this->__('Yes')),
        ));
        $this->addColumn('add_note', array(
            'header' => Mage::helper('tim_recommendation')->__('Action'),
            'width' => '50',
            'index' => 'recom_id',
            'filter' => false,
            'sortable' => false,
            'is_system' => true,
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_AddNote',
        ));
        $this->addColumn('display_note',
            array(
                'header' => Mage::helper('tim_recommendation')->__('Action'),
                'width' => '70',
                'index' => 'recom_id',
                'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_DisplayNote',
                'filter' => false,
                'sortable' => false,
                'is_system' => true,
            ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('recom_id');
        $this->getMassactionBlock()->setFormFieldName('acceptance');

        $this->getMassactionBlock()->addItem('yes', array(
            'label' => Mage::helper('tim_recommendation')->__('Acceptance Yes'),
            'url' => $this->getUrl('*/*/massAcceptanceYes', array('' => '')),
            'confirm' => Mage::helper('tim_recommendation')->__('Are you sure?')
        ));
        $this->getMassactionBlock()->addItem('no', array(
            'label' => Mage::helper('tim_recommendation')->__('Acceptance No'),
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