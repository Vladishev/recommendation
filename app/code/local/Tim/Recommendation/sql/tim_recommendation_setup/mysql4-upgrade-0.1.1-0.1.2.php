<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */

$installer = $this;

$installer->startSetup();

$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/user'),
        'avatar',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'default' => '',
            'comment' => 'avatar'
        )
    );
$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/user'),
        'description',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 255,
            'nullable' => false,
            'default' => '',
            'comment' => 'description'
        )
    );


$installer->endSetup();