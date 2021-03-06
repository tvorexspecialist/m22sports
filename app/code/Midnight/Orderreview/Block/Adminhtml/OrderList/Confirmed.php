<?php

namespace Midnight\Orderreview\Block\Adminhtml\OrderList;

use Midnight\Orderreview\Helper\ListHelper;
use \Magento\Backend\Model\UrlInterface;

class Confirmed extends \Magento\Framework\View\Element\Template
{
    protected $registry_key = 'confirmed_orders';
    protected $listHelper;
    protected $urlBuilder;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        ListHelper $listHelper,
        UrlInterface $urlBuilder,
        array $data = [])
    {
        parent::__construct($context, $data);
        $this->listHelper = $listHelper;
        $this->urlBuilder = $urlBuilder;
    }

    public function getOrders()
    {
        $reviewReadyFilter = \Midnight\Orderreview\Model\Review\State::READY_FOR_WINLINE;
        return $this->listHelper->getFilteredOrderCollection($reviewReadyFilter, 25, "DESC");
    }

    public function getPaymentMethod($order)
    {
        return $this->listHelper->getPaymentMethod($order);
    }

    public function getStatus($order)
    {
        return $this->listHelper->getStatus($order);
    }

    public function getOrderClass($order)
    {
        return $this->listHelper->getOrderClass($order);
    }
    public function getOrderUrl($url, array $params = []){
        return $this->urlBuilder->getUrl($url, $params);
    }
}