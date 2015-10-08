<?php
$installer = Mage::getModel('tim_recommendation/user');


$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/user'),
        'banner', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'banner');

$installer->endSetup();