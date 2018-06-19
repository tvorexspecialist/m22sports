<?php

namespace Midnight\Winline\Cron;

use Midnight\Winline\Model\Sync\Product;

class ProductSync
{

    protected $syncModel;

    public function __construct(Product $product)
    {
        $this->syncModel = $product;
    }
    public function sync(){
        $this->syncModel->sync();
    }

    public function syncAll(){
        $this->syncModel->syncAll();
    }

}