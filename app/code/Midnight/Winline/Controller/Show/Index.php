<?php

namespace Midnight\Winline\Controller\Show;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{

    private $sync;
    public function __construct(Context $context, \Midnight\Winline\Cron\ProductSync $sync)
    {
        parent::__construct($context);
        $this->sync = $sync;
    }

    public function execute()
    {
        $this->sync->sync();
    }
}