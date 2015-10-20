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
    ->addColumn($installer->getTable('tim_recommendation/recommendation'), 'tim_ip', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'length' => 255,
        'comment' => 'Tim IP'
    ));
$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/recommendation'), 'tim_host', array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'nullable' => true,
        'comment' => 'Tim HOST'
    ));

$installer->endSetup();