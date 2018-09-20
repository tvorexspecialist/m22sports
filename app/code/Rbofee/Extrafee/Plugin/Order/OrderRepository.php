<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Plugin\Order;

use Rbofee\Extrafee\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Model\OrderRepository as MagentoOrderRepository;

class OrderRepository
{
    /**
     * @var OrderExtensionFactory
     */
    private $orderExtensionFactory;

    /**
     * @var CollectionFactory
     */
    private $feeCollectionFactory;

    public function __construct(
        OrderExtensionFactory $orderExtensionFactory,
        CollectionFactory $feeCollectionFactory
    ) {
        $this->orderExtensionFactory = $orderExtensionFactory;
        $this->feeCollectionFactory = $feeCollectionFactory;
    }

    /**
     * @param MagentoOrderRepository   $subject
     * @param OrderInterface    $order
     *
     * @return OrderInterface
     */
    public function afterGet(MagentoOrderRepository $subject, OrderInterface $order)
    {
        $this->loadExtraFeeExtensionAttributes($order);

        return $order;
    }

    /**
     * @param MagentoOrderRepository               $subject
     * @param OrderSearchResultInterface    $orderCollection
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(MagentoOrderRepository $subject, OrderSearchResultInterface $orderCollection)
    {
        foreach ($orderCollection->getItems() as $order) {
            $this->loadExtraFeeExtensionAttributes($order);
        }

        return $orderCollection;
    }

    /**
     * @param OrderInterface $order
     * @return OrderRepository
     */
    private function loadExtraFeeExtensionAttributes(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();

        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderExtensionFactory->create();
        }

        if ($extensionAttributes->getRboextrafeeFeeId() !== null) {
            // Extra Fee entity is already loaded; no actions required
            return $this;
        }

        try {
            $feeQuote = $this->feeCollectionFactory->create()->getFeeByQuoteId($order->getQuoteId());
            $extensionAttributes->setRboextrafeeFeeId((string)$feeQuote['fee_id']);
            $extensionAttributes->setRboextrafeeFeeAmount((float)$feeQuote['fee_amount']);
            $extensionAttributes->setRboextrafeeBaseFeeAmount((float)$feeQuote['base_fee_amount']);
            $extensionAttributes->setRboextrafeeTaxAmount((float)$feeQuote['tax_amount']);
            $extensionAttributes->setRboextrafeeBaseTaxAmount((float)$feeQuote['tax_amount']);

            $order->setExtensionAttributes($extensionAttributes);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // Extra Fee entity cannot be loaded for current order; no actions required
            return $this;
        }
    }
}
