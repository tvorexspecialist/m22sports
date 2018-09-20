<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Plugin\Reflection;

class TypeCaster
{

    /**
     * @var \Rbofee\Base\Model\Serializer
     */
    private $serializer;

    /**
     * @var \Magento\Framework\App\ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * TypeCaster constructor.
     * @param \Rbofee\Base\Model\Serializer $serializer
     * @param \Magento\Framework\App\ProductMetadataInterface $productMetadata
     */
    public function __construct(
        \Rbofee\Base\Model\Serializer $serializer,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata
    ) {
        $this->serializer = $serializer;
        $this->productMetadata = $productMetadata;
    }

    /**
     * @param \Magento\Framework\Reflection\TypeCaster $subject
     * @param $value
     * @param $type
     * @return array|string
     */
    public function beforeCastValueToType(\Magento\Framework\Reflection\TypeCaster $subject, $value, $type)
    {
        if ($this->productMetadata->getVersion() <= "2.2.0") {
            if ($value && is_array($value) && !interface_exists($type) && !class_exists($type)) {
                $value = $this->serializer->serialize($value);
            }
        }

        return [$value, $type];
    }
}
