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

$recommendationMediaTable = $installer->getTable('tim_recommendation/media');

$installer->startSetup();

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
        ), 'type');

    $connection->createTable($table);
}

$installer->endSetup();