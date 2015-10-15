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

$installer->startSetup();

$installer->getConnection()->modifyColumn($installer->getTable('tim_recommendation/user'), 'user_type', 'smallint'
);

$installer->getConnection()->addForeignKey(
    $installer->getFkName('tim_recommendation/user', 'user_type', 'tim_recommendation/user_type', 'user_type_id'),
    $installer->getTable('tim_recommendation/user'),
    'user_type',
    $installer->getTable('tim_recommendation/user_type'),
    'user_type_id',
    Varien_Db_Ddl_Table::ACTION_SET_NULL,
    Varien_Db_Ddl_Table::ACTION_SET_NULL
);

$installer->endSetup();