<?php

/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Model_Observer
{
    /**
     * CRUD value in table tim_user_type
     */
    public function saveUserLevel()
    {
        $userLevels = unserialize(Mage::getStoreConfig('tim_recommendation/user_level/level_values'));
        if (!empty($userLevels)) {
            if (is_array($userLevels)) {
                foreach ($userLevels as $id => $values) {
                    $model = Mage::getModel('tim_recommendation/userLevel');
                    if ($model->load($id, 'system_config_id')->getData()) {
                        $model->setPoint($values['point'])
                            ->setFrom($values['from'])
                            ->setTo($values['to']);
                    } else {
                        $model->setSystemConfigId($id)
                            ->setPoint($values['point'])
                            ->setFrom($values['from'])
                            ->setTo($values['to']);
                    }
                    try {
                        $model->save();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                        Mage::getSingleton('core/session')->addError(
                            Mage::helper('tim_recommendation')->__('There was error during the saving.'));
                    }
                }
                $ids = Mage::helper('tim_recommendation')->getUserLevelDiffIds($userLevels);
                foreach ($ids as $id) {
                    $forDelete = Mage::getModel('tim_recommendation/userLevel')->load($id, 'system_config_id');
                    try {
                        $forDelete->delete();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                    }
                }
            }
        } else {
            $collection = Mage::getModel('tim_recommendation/userLevel')->getCollection();
            foreach ($collection as $item) {
                try {
                    $item->delete();
                } catch (Exception $e) {
                    Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                }
            }
        }
    }
}