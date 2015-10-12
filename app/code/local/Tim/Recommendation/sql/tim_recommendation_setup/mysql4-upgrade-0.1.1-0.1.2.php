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

$recommendationUserTypeTable = $installer->getTable('tim_recommendation/user_type');

$installer->startSetup();

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

$installer->endSetup();