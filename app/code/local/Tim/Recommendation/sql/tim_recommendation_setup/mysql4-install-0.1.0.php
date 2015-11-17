<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */

$installer = $this;
$connection = $installer->getConnection();

$recommendationTable = $installer->getTable('tim_recommendation/recommendation');
$recommendationUserTable = $installer->getTable('tim_recommendation/user');
$recommendationMediaTable = $installer->getTable('tim_recommendation/media');
$recommendationUserTypeTable = $installer->getTable('tim_recommendation/user_type');
$recommendationLevelTable = $installer->getTable('tim_recommendation/level');

$installer->startSetup();

if (!$connection->isTableExists($recommendationTable)) {
    $table = $connection->newTable($recommendationTable)
        ->addColumn('recom_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Recommendation Id')
        ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
        ), 'User Id')
        ->addColumn('parent', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true,
        ), 'Parent')
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
            'nullable' => true,
        ), 'Product Id')
        ->addForeignKey($installer->getFkName('tim_recommendation/recommendation', 'product_id', 'catalog/product', 'entity_id'),
            'product_id', $installer->getTable('catalog/product'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
        ), 'Product Id')
        ->addColumn('date_add', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Date add')
        ->addColumn('title', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'title')
        ->addColumn('advantages', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Advantages')
        ->addColumn('defects', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Defects')
        ->addColumn('conclusion', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Conclusion')
        ->addColumn('rating_price', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Rating price')
        ->addColumn('rating_durability', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Rating durability')
        ->addColumn('rating_failure', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Rating failure')
        ->addColumn('rating_service', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'unsigned' => true,
            'nullable' => false,
            'default' => '0',
        ), 'Rating service')
        ->addColumn('recommend', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
            'nullable' => true,
        ), 'Recommend')
        ->addColumn('by_it', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
            'nullable' => true,
        ), 'By it');

    $connection->createTable($table);
}

if (!$connection->isTableExists($recommendationUserTable)) {
    $table = $connection->newTable($recommendationUserTable)
        ->addColumn('recom_user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Recommendation user Id')
        ->addColumn('customer_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
        ), 'Customer Id')
        ->addForeignKey($installer->getFkName('tim_recommendation/user', 'customer_id', 'customer/entity', 'entity_id'),
            'customer_id', $installer->getTable('customer/entity'), 'entity_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addColumn('www', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'www')
        ->addColumn('ad', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'ad')
        ->addColumn('avatar', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'Avatar')
        ->addColumn('description', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Description')
        ->addColumn('user_type', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
            'nullable' => false,
        ), 'User type')
        ->addColumn('engage', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
        ), 'Engage');

    $connection->createTable($table);
}

if (!$connection->isTableExists($recommendationMediaTable)) {
    $table = $connection->newTable($recommendationMediaTable)
        ->addColumn('recom_media_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Recommendation media Id')
        ->addColumn('recom_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
        ), 'Recommendation Id')
        ->addForeignKey($installer->getFkName('tim_recommendation/media', 'recom_id',
            'tim_recommendation/recommendation', 'recom_id'),
            'recom_id', $installer->getTable('tim_recommendation/recommendation'), 'recom_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'Name')
        ->addColumn('type', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'Type');

    $connection->createTable($table);
}

if (!$connection->isTableExists($recommendationUserTypeTable)) {
    $table = $connection->newTable($recommendationUserTypeTable)
        ->addColumn('user_type_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'User type Id')
        ->addColumn('system_config_id', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => false,
        ), 'Configuration Id')
        ->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
            'nullable' => false,
        ), 'Name')
        ->addColumn('admin', Varien_Db_Ddl_Table::TYPE_BOOLEAN, null, array(
            'nullable' => true,
        ), 'Admin');

    $connection->createTable($table);
}

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