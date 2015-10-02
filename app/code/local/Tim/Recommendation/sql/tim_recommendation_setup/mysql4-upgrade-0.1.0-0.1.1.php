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
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$staticBlocks = array(
    array(
        'title' => 'tim_recommendation_one',
        'identifier' => 'tim_recommendation_one',
        'content' => 'tim_recommendation_one',
        'is_active' => 1,
        'stores' => array(0),
    ),
    array(
        'title' => 'tim_recommendation_two',
        'identifier' => 'tim_recommendation_two',
        'content' => 'tim_recommendation_two',
        'is_active' => 1,
        'stores' => array(0),
    )
);

foreach ($staticBlocks as $data) {
    Mage::getModel('cms/page')->setData($data)->save();
}

$installer->endSetup();