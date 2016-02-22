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
    ->addColumn($installer->getTable('tim_recommendation/malpractice'),
        'email',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Email',
        )
    );

$installer->getConnection()->changeColumn(
    $installer->getTable('tim_recommendation/recommendation'),
    'date_add',
    'date_add',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'default' => null,
    )
);

$installer->getConnection()->changeColumn(
    $installer->getTable('tim_recommendation/malpractice'),
    'date_add',
    'date_add',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'default' => null,
    )
);

$installer->endSetup();