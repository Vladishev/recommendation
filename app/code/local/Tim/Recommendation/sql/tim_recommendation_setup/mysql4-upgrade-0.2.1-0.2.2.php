<?php
/**
 * Change column type
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()->changeColumn(
    $installer->getTable('tim_recommendation/note'),
    'date_add',
    'date_add',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TIMESTAMP,
        'nullable' => true,
        'default' => null,
    )
);

$installer->endSetup();