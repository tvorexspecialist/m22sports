<?php

namespace Magecomp\Paymentfee\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteFactory;
use Magento\Sales\Model\Order;
use Magento\Framework\Event\Observer;

class Paymentfeequotesubmitbefore implements ObserverInterface
{
	protected $quoteFactory;
	protected $orderFacory;
	
	public function __construct(QuoteFactory $quoteFactory,
	Order $orderFacory)
    {
        $this->quoteFactory = $quoteFactory;
		$this->orderFacory = $orderFacory;
    }
	
    /**
     * Set paymentfee to order from quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
		
		$order = $observer->getOrder();
		$quoteId=$order->getQuoteId();
		
		$quote = $this->quoteFactory->create()->load($quoteId);	
		$address = $quote->getShippingAddress();
		
		$order->setMcPaymentfeeAmount($address->getMcPaymentfeeAmount());
		$order->setBaseMcPaymentfeeAmount($address->getBaseMcPaymentfeeAmount());
		$order->setMcPaymentfeeDescription($address->getMcPaymentfeeDescription());
		$order->setMcPaymentfeeTaxAmount($address->getMcPaymentfeeTaxAmount());
		$order->setBaseMcPaymentfeeTaxAmount($address->getBaseMcPaymentfeeTaxAmount());
		$order->save();
		
        return $this;
    }
}
