<?php

$installer = $this;
$connection = $installer->getConnection();

$recommendationLevelTable = $installer->getTable('tim_recommendation/level');

$installer->startSetup();

if (!$connection->isTableExists($recommendationLevelTable)) {
    $table = $connection->newTable($recommendationLevelTable)
        ->addColumn('user_level_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Recommendation user Id')
        ->addColumn('system_config_id', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Configuration Id')
        ->addColumn('point', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        ), 'Recommendation point')
        ->addColumn('from', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        ), 'Recommendation from')
        ->addColumn('to', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        ), 'Recommendation to');

    $connection->createTable($table);
}

$installer->endSetup();