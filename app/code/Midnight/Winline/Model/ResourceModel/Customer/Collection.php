<?php

namespace Midnight\Winline\Model\ResourceModel\Customer;

use \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection
 * @package Midnight\Winline\Model\ResourceModel\Customer
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'Kontonummer';
    protected $_eventPrefix = 'ExportWebKunden';
    protected $_eventObject = 'ExportWebKunden';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Midnight\Winline\Model\Customer', 'Midnight\Winline\Model\ResourceModel\Customer');
    }
}
