<?php

namespace Midnight\Winline\Model\ResourceModel;

use \Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use \Magento\Framework\Model\ResourceModel\Db\Context;

/**
 * Class Customer
 * @package Midnight\Winline\Model\ResourceModel
 */
class Customer extends AbstractDb
{
    const FIELD_ID = 'Kontonummer';

    protected $connectionName = 'midnight_winline_database';

    public function __construct(Context $context)
    {
        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('ExportWebKunden', self::FIELD_ID);
    }
}