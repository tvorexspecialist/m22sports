<?php

namespace Midnight\Winline\Cron;

use Midnight\Winline\Model\Sync\Customer;

class CustomerSync
{

    protected $syncModel;

    public function __construct(Customer $product)
    {
        $this->syncModel = $product;
    }
    public function sync(){
        $this->syncModel->sync();
    }

}