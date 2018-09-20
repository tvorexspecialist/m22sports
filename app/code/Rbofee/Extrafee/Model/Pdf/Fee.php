<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Model\Pdf;

use Magento\Sales\Model\Order\Pdf\Total\DefaultTotal;
use Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory as TaxCollectionFactory;
use Rbofee\Extrafee\Model\ResourceModel\Quote\CollectionFactory as FeeQuoteCollectionFactory;

class Fee extends DefaultTotal
{
    /** @var FeeQuoteCollectionFactory  */
    protected $feeQuoteCollectionFactory;

    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        TaxCollectionFactory $ordersFactory,
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        array $data = []
    ) {
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    public function getAmount()
    {
        $quoteId = $this->getOrder()->getQuoteId();
        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('option_id', ['neq' => '0'])
            ->addFieldToFilter('quote_id', $quoteId);

        $feeAmount = 0;

        foreach ($feesQuoteCollection as $feeOption) {
            $feeAmount += $feeOption->getFeeAmount();
        }

        return $feeAmount;
    }
}
