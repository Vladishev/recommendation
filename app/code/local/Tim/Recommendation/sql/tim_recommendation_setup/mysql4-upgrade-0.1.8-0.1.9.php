<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = $this;

$installer->startSetup();
$installer->getConnection()
    ->addColumn($installer->getTable('tim_recommendation/recommendation'),
        'average_rating',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_FLOAT,
            'nullable' => true,
            'default' => null,
            'comment' => 'Average rating',
            'after' => 'rating_service'
        )
    );

$installer->endSetup();