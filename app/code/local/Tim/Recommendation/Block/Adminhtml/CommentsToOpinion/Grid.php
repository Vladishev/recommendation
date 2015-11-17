<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Adminhtml_CommentsToOpinion_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Init grid
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('tim_recommendation_comments_to_opinion_grid');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection()
    {
        $id = $this->getRequest()->getParam('id');
        $collection = Mage::getModel('tim_recommendation/recommendation')->getCollection();
        $collection->addFieldToFilter(array('recom_id', 'parent'),
            array(
                array('eq' => $id),
                array('eq' => $id))
        );

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Tim_Recommendation_Block_Adminhtml_CommentsToOpinion_Grid
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
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_CustomerName',
            'filter' => false
        ));
        $this->addColumn('date_add', array(
            'header' => Mage::helper('tim_recommendation')->__('Date Added'),
            'index' => 'date_add',
            'type' => 'datetime',
            'width' => '100'
        ));
        $this->addColumn('comment', array(
            'header' => Mage::helper('tim_recommendation')->__('Comments'),
            'width' => '250',
            'index' => 'comment',
        ));
        $this->addColumn('acceptance', array(
            'header' => Mage::helper('tim_recommendation')->__('Acceptance'),
            'width' => '50',
            'align' => 'center',
            'index' => 'acceptance',
            'renderer' => 'Tim_Recommendation_Block_Adminhtml_Render_YesNo',
            'filter_condition_callback' => array($this, '_acceptanceFilter'),
        ));

        return parent::_prepareColumns();
    }

    /**
     * Custom filter for acceptance field
     * @param (obj)$collection
     * @param (obj)$column
     * @return $this
     */
    protected function _acceptanceFilter($collection, $column)
    {
        if ($value = $column->getFilter()->getValue()) {
            if ($value == Mage::helper('tim_recommendation')->__('Yes')) {
                $this->getCollection()->getSelect()->where(
                    "main_table.acceptance = 1");
                return $this;
            }
            if ($value == Mage::helper('tim_recommendation')->__('No')) {
                $this->getCollection()->getSelect()->where(
                    "main_table.acceptance = 0");
                return $this;
            }
        } else {
            return $this;
        }
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