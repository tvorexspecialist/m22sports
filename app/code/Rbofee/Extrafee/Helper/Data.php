<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Helper;

/**
 * Class Data
 *
 * @author Rbo Developer
 */

use Magento\Framework\App\Helper\AbstractHelper;
use Rbofee\Extrafee\Model\FeesInformationManagement;

class Data extends AbstractHelper
{
    /** @var FeesInformationManagement  */
    protected $_feesInformationManagement;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        FeesInformationManagement $feesInformationManagement,
        \Magento\Framework\App\Helper\Context $context
    ){
        parent::__construct($context);
        $this->_feesInformationManagement = $feesInformationManagement;
        $this->scopeConfig = $context->getScopeConfig();
    }

    /**
     * @param $path
     * @return mixed
     */
    public function getScopeValue($path)
    {
        return $this->scopeConfig->getValue('rbofee_extrafee/' . $path, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return array
     */
    public function getFeesOptions(\Magento\Quote\Model\Quote $quote)
    {
        $items = $this->_feesInformationManagement->collectQuote($quote);

        foreach ($items as &$feeData) {
            $feeData += [
                'id' => null,
                'name' => null,
                'description' => null,
                'base_options' => [],
                'frontend_type' => null,
                'current_value' => null
            ];
        }

        return $items;
    }
}
