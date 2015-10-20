<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Bogdan Bakalov <bakalov.bogdan@gmail.com>
 */
class Tim_Recommendation_Block_Recommendation extends Mage_Core_Block_Template
{
    /* Gets user data from tables */
    public function getUserInformation($userId)
    {
        $collection = Mage::getModel('tim_recommendation/user')->getCollection();
        $collection->getSelect()
            ->join(array('cev' => 'customer_entity_varchar'), 'cev.entity_id = main_table.customer_id', array('name' => 'value'))
            ->join(array('cev1' => 'customer_entity_varchar'), 'cev1.entity_id = main_table.customer_id', array('surname' => 'value'))
            ->join(array('ce' => 'customer_entity'), 'ce.entity_id = main_table.customer_id', 'email')
            ->where('main_table.customer_id = ' . $userId . '')
            ->where('cev.attribute_id = 5')
            ->where('cev1.attribute_id = 7');

        $userData = $collection->getFirstItem()->getData();
        return $userData;
    }

    /**
     * Gets host name by ip
     * @param string $ip
     * @return string
     */
    public function getHost($ip)
    {
        if (strstr($ip, ', ')) {
            $ips = explode(', ', $ip);
            $ip = $ips[0];
        }
        try {
            $hostName = gethostbyaddr($ip);
        } catch (Exception $e) {
            $hostName = '';
        }

        return $hostName;
    }
}