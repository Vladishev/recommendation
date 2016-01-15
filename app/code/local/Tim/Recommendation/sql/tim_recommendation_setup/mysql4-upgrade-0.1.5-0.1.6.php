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

$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/user'),
        'nick',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => false,
            'comment' => 'User nick'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/recommendation'),
        'manufacturer_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Manufacturer Id'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/recommendation'),
        'category_id',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_INTEGER,
            'nullable' => true,
            'comment' => 'Category Id'
        )
    );

$installer->getConnection()->modifyColumn($installer->getTable('tim_recommendation/user'), 'description', 'text'
);

$installer->endSetup();