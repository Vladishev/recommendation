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
    public function saveUserType()
    {
        $userTypes = unserialize(Mage::getStoreConfig('tim_recommendation/user_type/values'));
        if (!empty($userTypes)) {
            if (is_array($userTypes)) {
                foreach ($userTypes as $id => $values) {
                    $model = Mage::getModel('tim_recommendation/userType');
                    if ($model->load($id, 'system_config_id')->getData()) {
                        $model->setName($values['user_type'])
                            ->setAdmin($values['administrator']);
                    } else {
                        $model->setSystemConfigId($id)
                            ->setName($values['user_type'])
                            ->setAdmin($values['administrator']);
                    }
                    try {
                        $model->save();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                        Mage::getSingleton('core/session')->addError(
                            Mage::helper('tim_recommendation')->__('Didn\'t save %s value.', $values['user_type']));
                    }
                }
                $ids = Mage::helper('tim_recommendation')->getUserTypeDiffIds($userTypes);
                foreach ($ids as $id) {
                    Mage::getModel('tim_recommendation/userType')->load($id, 'system_config_id')->delete();
                    try {
                        $model->save();
                    } catch (Exception $e) {
                        Mage::log($e->getMessage(), null, 'tim_recommendation.log');
                    }
                }
            }
        } else {
            $collection = Mage::getModel('tim_recommendation/userType')->getCollection();
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