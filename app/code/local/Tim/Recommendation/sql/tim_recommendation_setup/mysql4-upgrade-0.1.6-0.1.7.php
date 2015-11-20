<?php

/**
* Tim
*
* @category   Tim
* @package    Tim_Recommendation
* @copyright  Copyright (c) 2015 Tim (http://tim.pl)
* @author     Vlad Verbitskiy <vladmsu@ukr.net>
*/

$installer = $this;
$connection = $installer->getConnection();

$recomMalpracticeTable = $installer->getTable('tim_recommendation/malpractice');

$installer->startSetup();

if (!$connection->isTableExists($recomMalpracticeTable)) {
    $table = $connection->newTable($recomMalpracticeTable)
        ->addColumn('malpractice_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'identity' => true,
            'nullable' => false,
            'primary' => true,
        ), 'Malpractice Id')
        ->addColumn('recom_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            'unsigned' => true,
        ), 'Recommendation Id')
        ->addForeignKey($installer->getFkName('tim_recommendation/malpractice', 'recom_id', 'tim_recommendation/recommendation', 'recom_id'),
            'recom_id', $installer->getTable('tim_recommendation/recommendation'), 'recom_id',
            Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
        ->addColumn('user_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
            'nullable' => false,
            'unsigned' => true,
        ), 'User Id')
        ->addColumn('date_add', Varien_Db_Ddl_Table::TYPE_TIMESTAMP, null, array(
            'nullable' => false,
            'default' => Varien_Db_Ddl_Table::TIMESTAMP_INIT,
        ), 'Date add')
        ->addColumn('comment', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => true,
        ), 'Comment')
        ->addColumn('tim_ip', Varien_Db_Ddl_Table::TYPE_TEXT, '100', array(
            'nullable' => true,
        ), 'IP')
        ->addColumn('tim_host', Varien_Db_Ddl_Table::TYPE_TEXT, '64k', array(
            'nullable' => true,
        ), 'Host');


    $connection->createTable($table);
}

$installer->endSetup();