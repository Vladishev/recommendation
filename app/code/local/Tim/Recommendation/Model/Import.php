<?php
/**
 * Tim
 *
 * @category   Tim
 * @package    Tim_Recommendation
 * @copyright  Copyright (c) 2015 Tim (http://tim.pl)
 * @author     Vladislav Verbitskiy
 */
class Tim_Recommendation_Model_Import extends Mage_Core_Model_Abstract
{
    /**
     * Constants mean characters using administrator
     * for marking new comments/opinions and existing
     * comments in imported file
     */
    const NEW_RECOM = 'n';
    const EXIST_RECOM = 'a';
    /**
     * Returns array with added opinions or comments
     * from xml file
     * @return array
     */
    public function readXmlFile()
    {
        $_helper = Mage::helper('tim_recommendation');
        $xmlData = array();
        $xml = simplexml_load_file($_helper->getImportFilePath());
        $item_total = count($xml->Worksheet->Table->Row);
        $headerObj = $xml->Worksheet->Table->Row[0];
        $headerCount = count($xml->Worksheet->Table->Row[0]);
        $header = array();
        for($k = 0; $k < $headerCount; $k++) {
            $header[] = (string)$headerObj->Cell[$k]->Data;
        }
        for ($i = 1; $i < $item_total; $i++) {
            $data = $xml->Worksheet->Table->Row[$i];
            $recomId = (string)$data->Cell[0]->Data;
            $parent = (string)$data->Cell[1]->Data;

            if (strstr($recomId, self::NEW_RECOM) or strstr($parent, self::NEW_RECOM) or strstr($parent, self::EXIST_RECOM)) {
                for ($z = 0; $z < $headerCount; $z++) {
                    $xmlData[$i][$header[$z]] = (string)$data->Cell[$z]->Data;
                }
            }
        }
        return $xmlData;
    }

    /**
     * Returns array with added opinions or comments
     * from csv file
     * @return array
     */
    public function readCsvFile()
    {
        $_helper = Mage::helper('tim_recommendation');
        $csvData = array();
        $path = $_helper->getImportFilePath();
        if (($handle = fopen($path, "r")) !== false) {
            $headerObj = fgetcsv($handle, 1000, ",");
            $headerCount = count($headerObj);
            $i = 0;
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                $recomId = $data[0];
                $parent = $data[1];
                if (strstr($recomId, self::NEW_RECOM) or strstr($parent, self::NEW_RECOM) or strstr($parent, self::EXIST_RECOM)) {
                    for ($c = 0; $c < $headerCount; $c++) {
                        $csvData[$i][$headerObj[$c]] = $data[$c];
                    }
                }
                $i++;
            }
            fclose($handle);
        }
        return $csvData;
    }

    /**
     * Saves data from imported file
     */
    public function saveImportData($fileType)
    {
        $_helper = Mage::helper('tim_recommendation');
        $allowedExtensions = $_helper->getAllowedExtensions();
        switch ($fileType) {
            case $allowedExtensions['xml']:
                $dataArray = $this->readXmlFile();
                break;
            case $allowedExtensions['csv']:
                $dataArray = $this->readCsvFile();
                break;
        }

        if (!empty($dataArray)) {
            foreach ($dataArray as $items) {
                if (!empty($items['recom_id'])) {
                    $tmpRecomId = $items['recom_id'];
                    $productId = Mage::getModel("catalog/product")->getIdBySku($items['sku']);
                    $response = $this->_addNewOpinion($items, $productId);
                }

                if (!empty($items['parent']) and ($items['parent'] == $tmpRecomId)) {
                    $response = $this->_addNewComment($items, $productId, $response['opinionId']);
                }

                if (!empty($items['parent']) and ($items['parent']{0} == self::EXIST_RECOM)) {
                    $response = $this->_addExistingComment($items);
                }
            }
        }
        Mage::log($response);
        return $response;
    }

    /**
     * Adds new opinion to tim_recommendation table
     * @param (arr)$items
     * @param (int)$productId
     * @param (obj)$_helper
     * @return integer
     */
    protected function _addNewOpinion($items, $productId)
    {
        $response = array();
        $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
            ->setUserId($items['user_id'])
            ->setProductId($productId)
            ->setAdvantages($items['advantages'])
            ->setDefects($items['defects'])
            ->setConclusion($items['conclusion'])
            ->setRatingPrice($items['rating_price'])
            ->setRatingDurability($items['rating_durability'])
            ->setRatingFailure($items['rating_failure'])
            ->setRatingService($items['rating_service']);
        try {
            $recommendationModel->save();
            $response['opinionId'] = $recommendationModel->getRecomId();
            $response['success'] = true;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            $response['success'] = false;
        }
        return $response;
    }

    /**
     * Adds new comment to tim_recommendation table
     * @param (arr)$items
     * @param (int)$productId
     * @param (int)$opinionId
     * @param (obj)$_helper
     */
    protected function _addNewComment($items, $productId, $opinionId)
    {
        $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
            ->setUserId($items['user_id'])
            ->setParent($opinionId)
            ->setProductId($productId)
            ->setComment($items['comment']);
        try {
            $recommendationModel->save();
            $response['success'] = true;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            $response['success'] = false;
        }
        return $response;
    }

    /**
     * Adds new comment for existing opinion to tim_recommendation table
     * @param (arr)$items
     * @param (obj)$_helper
     */
    protected function _addExistingComment($items)
    {
        $productId = Mage::getModel('tim_recommendation/recommendation')
            ->load(substr($items['parent'], 1))
            ->getProductId();
        $recommendationModel = Mage::getModel('tim_recommendation/recommendation')
            ->setUserId($items['user_id'])
            ->setParent(substr($items['parent'], 1))
            ->setProductId($productId)
            ->setComment($items['comment']);
        try {
            $recommendationModel->save();
            $response['success'] = true;
        } catch (Exception $e) {
            Mage::log($e->getMessage(), null, 'tim_recommendation.log');
            $response['success'] = false;
        }
        return $response;
    }
}