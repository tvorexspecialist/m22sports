<?php

namespace Rbo\Puch\Plugin\Model\Category\Attribute\Source;

class Mode
{
    public function afterGetAllOptions(
        \Magento\Catalog\Model\Category\Attribute\Source\Mode $subject,
        $result
    ) {
        $result[] = ['value' => 'SUBCATEGORY_MODE', 'label' => 'Show only subcategories'];
        $result[] = ['value' => 'SUBCATEGORY_AND_PRODUCTS_MODE', 'label' => 'Show subcategories and products'];
        return $result;
    }
}

?>