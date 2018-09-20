<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Block\Adminhtml\Order\Create\Fee;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Rbofee\Extrafee\Helper\Data as ExtrafeeHelper;

class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /** @var  array */
    protected $_rates;
    /**
     * @var ExtrafeeHelper
     */
    private $extrafeeHelper;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        ExtrafeeHelper $extrafeeHelper,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
        $this->extrafeeHelper = $extrafeeHelper;
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_rbofee_extrafee_form');
    }

    public function getExtraFees()
    {
        if ($this->_rates === null) {
            $this->_rates = $this->extrafeeHelper->getFeesOptions($this->_orderCreate->getQuote());
        }

        return $this->_rates;
    }

    public function getFormattedPrice($amount)
    {
        $amount = number_format($amount, 2);
        $pattern = $this->_storeManager->getStore($this->_orderCreate->getQuote()->getStoreId())
            ->getCurrentCurrency()->getOutputFormat();

        return str_replace('%s', $amount, $pattern);
    }
}
