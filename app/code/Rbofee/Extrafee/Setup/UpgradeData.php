<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Setup;

use Magento\Catalog\Model\Product\Attribute\Repository;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Repository
     */
    private $attributeRepository;

    /**
     * @var \Rbofee\Base\Setup\SerializedFieldDataConverter
     */
    private $fieldDataConverter;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetaData;

    public function __construct(
        Repository $attributeRepository,
        \Magento\Framework\App\ProductMetadataInterface $productMetaData,
        \Rbofee\Base\Setup\SerializedFieldDataConverter $fieldDataConverter
    ) {
        $this->attributeRepository = $attributeRepository;
        $this->fieldDataConverter = $fieldDataConverter;
        $this->productMetaData = $productMetaData;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        if (!$context->getVersion()) {
            return;
        }

        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.2.2', '<')
            && $this->productMetaData->getVersion() >= "2.2.0"
        ) {
            $this->fieldDataConverter->convertSerializedDataToJson(
                'rbofee_extrafee',
                'entity_id',
                ['conditions_serialized']
            );
        }

        $setup->endSetup();
    }
}
