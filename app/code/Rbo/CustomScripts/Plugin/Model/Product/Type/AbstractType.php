<?php

namespace Rbo\CustomScripts\Plugin\Model\Product\Type;

class AbstractType
{

    public function aroundIsSalable(\Magento\Catalog\Model\Product\Type\AbstractType $subject, $proceed, $product){
        $result = $proceed($product);
        return !$product->getOrderable() ? false : $result;
    }

}