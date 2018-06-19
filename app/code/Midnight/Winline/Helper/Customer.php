<?php

namespace Midnight\Winline\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use \Midnight\Winline\Model\CustomerFactory;

class Customer extends AbstractHelper
{

    protected $customerFactory;

    protected $customerObj = null;

    public function __construct(Context $context,
                                CustomerFactory $customerFactory)
    {
        parent::__construct($context);
        $this->customerFactory = $customerFactory;
    }
}