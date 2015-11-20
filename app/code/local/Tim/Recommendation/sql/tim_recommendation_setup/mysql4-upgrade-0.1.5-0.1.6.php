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
$installer->getConnection()->modifyColumn($installer->getTable('tim_recommendation/user'), 'description', 'text'
);

$installer->endSetup();