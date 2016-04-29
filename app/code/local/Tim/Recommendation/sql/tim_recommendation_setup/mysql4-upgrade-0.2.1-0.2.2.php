<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2016 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy <vladomsu@gmail.com>
 */

$installer = $this;
$installer->startSetup();

$installer->getConnection()
    ->addIndex(
        $installer->getTable('tim_recommendation/recommendation'),
        'IDX_RECOM_PARENT',
        'parent'
    );

$installer->endSetup();