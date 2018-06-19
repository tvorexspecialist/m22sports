<?php

namespace Midnight\Winline\Model;

use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;
use \Magento\Framework\Model\Context;
use \Magento\Framework\Registry;
use \Magento\Framework\Model\ResourceModel\AbstractResource;
use \Magento\Framework\Data\Collection\AbstractDb;

/**
 * Class Product
 * @package Midnight\Winline\Model
 */
class Product extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'ExportWebArtikel';

    const FIELD_ID = 'Artikelnummer';
    const FIELD_IMAGE = 'Grafikfile';
    const FIELD_SKU = 'Artikelnummer';
    const FIELD_NAME = 'Bezeichnung';
    /**
     * @var string
     */
    protected $_cacheTag = 'ExportWebArtikel';
    /**
     * @var string
     */
    protected $_eventPrefix = 'ExportWebArtikel';


    /**
     * Customer constructor.
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init('Midnight\Winline\Model\ResourceModel\Product');
    }

    /**
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }

    /**
     * @return mixed
     */
    public function getSku()
    {
        return $this->getData(self::FIELD_SKU);
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->getData(self::FIELD_NAME);
    }
}
