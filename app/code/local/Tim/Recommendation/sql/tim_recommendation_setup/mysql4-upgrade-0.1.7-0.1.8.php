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
    ->addColumn($installer->getTable('tim_recommendation/recommendation'),
        'add_method',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'comment' => 'Method of add'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/recommendation'),
        'publication_date',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
            'nullable' => true,
            'default' => null,
            'comment' => 'Date of publication',
            'after' => 'date_add'
        )
    );

$installer->endSetup();