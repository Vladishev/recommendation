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

$recommendationNoteTable = $installer->getTable('tim_recommendation/note');

$installer->startSetup();

if (!$connection->isTableExists($recommendationNoteTable)) {
    $table = $connection->newTable($recommendationNoteTable)
        ->addColumn('note_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Recommendation Id')
        ->addColumn('object_name', Varien_Db_Ddl_Table::TYPE_TEXT, '255', array(
            'nullable' => true,
        ), 'Object name')
        ->addColumn('object_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => true,
        ), 'Object Id')
        ->addColumn('note', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => true,
        ), 'Note value')
        ->addColumn('date_add', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => true,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Date add')
        ->addColumn('admin_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'unsigned' => true,
        ), 'Admin Id');

    $connection->createTable($table);
}

$installer->endSetup();