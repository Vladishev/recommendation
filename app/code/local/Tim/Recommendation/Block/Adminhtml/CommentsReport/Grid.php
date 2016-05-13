<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 */

/**
 * Class Tim_Recommendation_Block_Adminhtml_CommentsReport_Grid
 * Creates grid with comments
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_CommentsReport_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Acceptance markers
     */
    const ACCEPTANCE_YES = 1;

    /**
     * First name attribute id
     *
     * @var int
     */
    protected $_firstNameId;

    /**
     * Last name attribute id
     *
     * @var int
     */
    protected $_lastNameId;

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
        $this->_firstNameId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('customer', 'firstname');
        $this->_lastNameId = Mage::getResourceModel('eav/entity_attribute')
            ->getIdByCode('customer', 'lastname');
    }

    /**
     * Prepare grid collection object
     *
     * @return Mage_Adminhtml_Block_Widget_Grid
     * @throws Exception
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        if ($parentId = (int) $this->getRequest()->getParam('commentId')) {
            $collection->addFieldToFilter('parent', array( array('eq' => $parentId)));
        }
        if ($recomId = (int) $this->getRequest()->getParam('recomId')) {
            $collection->addFieldToFilter('recom_id', array( array('eq' => $recomId)));
        }
        $collection->getSelect()->join(array('cev2' => 'customer_entity_varchar'),
            "cev2.entity_id = main_table.user_id AND cev2.attribute_id = " . $this->_firstNameId,
            array("Akceptacja" => "IF (main_table.acceptance = " . self::ACCEPTANCE_YES . ",'TAK','NIE')"));
        $collection->getSelect()->join(array('cev3' => 'customer_entity_varchar'),
            "cev3.entity_id = main_table.user_id AND cev3.attribute_id = " . $this->_lastNameId,
            array("name" => "CONCAT( cev2.value,' ', cev3.value)"));
        $collection->getSelect()->joinLeft(array('cev' => 'customer_entity_varchar'),
            "cev.entity_id = main_table.user_id AND cev.attribute_id = " . $this->_firstNameId, array('customer_firstname' => 'value'));
        $collection->getSelect()->joinLeft(array('cev1' => 'customer_entity_varchar'),
            "cev1.entity_id = main_table.user_id AND cev1.attribute_id = " . $this->_lastNameId, array('customer_lastname' => 'value'));
        $collection->getSelect()->joinLeft(array('tru' => 'tim_recom_user'),
            'main_table.user_id = tru.customer_id',
            array('nick'));
        $collection->getSelect()->where('main_table.parent IS NOT NULL');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepares columns
     *
     * @return $this
     * @throws Exception
     */
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
        $this->addColumn('publication_date', array(
            'header' => Mage::helper('tim_recommendation')->__('Date of publication'),
            'index' => 'publication_date',
            'type' => 'datetime',
            'width' => '200',
            'filter_time' => true,
        ));
        $this->addColumn('comments', array(
            'header' => Mage::helper('tim_recommendation')->__('Text of comment'),
            'width' => '200',
            'index' => 'comment',
        ));
        $this->addColumn('add_method', array(
            'header' => Mage::helper('tim_recommendation')->__('Method of adding'),
            'width' => '50',
            'index' => 'add_method',
            'type' => 'options',
            'options' => array('Użytkownik' => $this->__('User'), 'Z pliku' => $this->__('From file')),
        ));
        $this->addColumn('acceptance', array(
            'header' => Mage::helper('tim_recommendation')->__('Acceptance'),
            'width' => '20',
            'index' => 'acceptance',
            'filter_index' => 'main_table.acceptance',
            'type' => 'options',
            'options' => array(0 => $this->__('No'), 1 => $this->__('Yes')),
        ));
        $this->addColumn('display_comment',
            array(
                'header' => Mage::helper('tim_recommendation')->__('Operation'),
                'width' => '70',
                'type' => 'action',
                'getter' => 'getId',
                'actions' => array(
                    array(
                        'caption' => Mage::helper('tim_recommendation')->__('Display comment'),
                        'url' => array('base' => '*/*/commentInfo'),
                        'field' => 'id'
                    )
                ),
                'filter' => false,
                'sortable' => false,
                'index' => 'stores',
                'is_system' => true,
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
        $this->getMassactionBlock()->addItem('modify', array(
            'label' => Mage::helper('tim_recommendation')->__('Modify'),
            'url' => $this->getUrl('*/*/modifyComment', array('' => ''))
        ));

        return $this;
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/commentsReport/commentInfo', array('id' => $row->getRecomId()));
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