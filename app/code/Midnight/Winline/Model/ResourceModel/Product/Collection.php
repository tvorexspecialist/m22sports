<?php

namespace Midnight\Winline\Model\ResourceModel\Product;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Midnight\Winline\Model\ResourceModel\Product
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'Artikelnummer';

    protected $_eventPrefix = 'ExportWebArtikel';
    protected $_eventObject = 'ExportWebArtikel';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Midnight\Winline\Model\Product', 'Midnight\Winline\Model\ResourceModel\Product');
    }
}
