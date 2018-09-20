<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Model;

/**
 * Class FeeConfigProvider
 *
 * @author Rbo Developer
 */
use Rbofee\Extrafee\Helper\Data as ExtrafeeHelper;
use Magento\Checkout\Model\ConfigProviderInterface;

class FeeConfigProvider implements ConfigProviderInterface
{
    /** @var ExtrafeeHelper  */
    protected $extrafeeHelper;

    /**
     * @param ExtrafeeHelper $extrafeeHelper
     */
    public function __construct(
        ExtrafeeHelper $extrafeeHelper
    ){
        $this->extrafeeHelper = $extrafeeHelper;
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        $config = [];
        $config['rbofee'] = [
            'extrafee' => [
                'enabledOnCheckout' => $this->extrafeeHelper->getScopeValue('frontend/checkout'),
                'enabledOnCart' => $this->extrafeeHelper->getScopeValue('frontend/cart')
            ]
        ];
        return $config;
    }
}