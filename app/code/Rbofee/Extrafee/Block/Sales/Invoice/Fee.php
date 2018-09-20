<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Block\Sales\Invoice;

/**
 * Class Fee
 *
 * @author Rbo Developer
 */

use Rbofee\Extrafee\Model\ResourceModel\Quote\CollectionFactory as FeeQuoteCollectionFactory;
use Magento\Framework\View\Element\Template\Context;

class Fee extends \Magento\Framework\View\Element\Template
{
    /** @var \Magento\Sales\Model\Order  */
    protected $order;

    protected $source;

    /**
     * @var FeeQuoteCollectionFactory
     */
    protected $feeQuoteCollectionFactory;

    /**
     * @param Context $context
     * @param FeeQuoteCollectionFactory $feeQuoteCollectionFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        FeeQuoteCollectionFactory $feeQuoteCollectionFactory,
        array $data = []

    ){
        $this->feeQuoteCollectionFactory = $feeQuoteCollectionFactory;
        return parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();

        $this->order = $parent->getOrder();
        $this->source = $parent->getSource();

        $feesQuoteCollection = $this->feeQuoteCollectionFactory->create()
            ->addFieldToFilter('option_id', ['neq' => '0'])
            ->addFieldToFilter('quote_id', $this->order->getQuoteId());

        $feeAmount = 0;
        $baseFeeAmount = 0;
        $labels = [];

        foreach($feesQuoteCollection as $feeOption) {
            $feeAmount += $feeOption->getFeeAmount();
            $baseFeeAmount += $feeOption->getBaseFeeAmount();

            $labels[] = $feeOption->getLabel();
        }

        if ($feeAmount > 0) {
            $fee = new \Magento\Framework\DataObject(
                [
                    'code' => 'rbofee_extrafee',
                    'strong' => false,
                    'value' => $feeAmount,
                    'base_value' => $baseFeeAmount,
                    'label' => __('Extra Fee: %1', implode(', ', $labels)),
                ]
            );

            $parent->addTotal($fee, 'rbofee_extrafee');
        }

        return $this;
    }
}