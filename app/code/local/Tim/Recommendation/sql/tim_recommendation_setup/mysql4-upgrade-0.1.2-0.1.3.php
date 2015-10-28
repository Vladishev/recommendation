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

$installer->getConnection()->changeColumn(
    $installer->getTable('tim_recommendation/recommendation'),
    'title',
    'comment',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => '64k',
        'nullable' => true,
    )
);

$installer->endSetup();