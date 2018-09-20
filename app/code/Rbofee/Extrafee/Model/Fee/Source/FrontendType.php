<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model\Fee\Source;

/**
 * Class FrontendType
 *
 * @author Rbo Developer
 */

use Magento\Framework\Data\OptionSourceInterface;
use Rbofee\Extrafee\Model\Fee;

class FrontendType implements OptionSourceInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Checkbox'),
                'value' => Fee::FRONTEND_TYPE_CHECKBOX
            ],
            [
                'label' => __('Dropdown'),
                'value' => Fee::FRONTEND_TYPE_DROPDOWN
            ],
            [
                'label' => __('Radio Button'),
                'value' => Fee::FRONTEND_TYPE_RADIO
            ]
        ];
    }
}