<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model\Fee\Source;

/**
 * Class PriceType
 *
 * @author Rbo Developer
 */
use Magento\Framework\Data\OptionSourceInterface;
use Rbofee\Extrafee\Model\Fee;

class PriceType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Fixed'),
                'value' => Fee::PRICE_TYPE_FIXED
            ],
            [
                'label' => __('Percent'),
                'value' => Fee::PRICE_TYPE_PERCENT
            ]
        ];
    }
}