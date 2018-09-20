<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Plugin\Setup;

class UpgradeData
{
    /**
     * @var \Rbofee\Base\Setup\SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    public function __construct(\Rbofee\Base\Setup\SerializedFieldDataConverter $fieldDataConverter)
    {
        $this->fieldDataConverter = $fieldDataConverter;
    }

    /**
     * @param \Magento\SalesRule\Setup\UpgradeData $subject
     * @param $result
     * @return mixed
     */
    public function afterConvertSerializedDataToJson(\Magento\SalesRule\Setup\UpgradeData $subject, $result)
    {
        $this->fieldDataConverter->convertSerializedDataToJson(
            'rbofee_extrafee',
            'entity_id',
            ['conditions_serialized']
        );

        return $result;
    }
}
