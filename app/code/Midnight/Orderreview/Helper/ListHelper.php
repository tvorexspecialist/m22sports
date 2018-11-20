<?php

namespace Midnight\Orderreview\Helper;

use Magento\Framework\App\Helper\Context;
use \Magento\Framework\Phrase;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Registry;
use \Magento\Sales\Model\OrderFactory;
use \Magento\Customer\Model\CustomerFactory;
use Magento\Customer\Api\CustomerRepositoryInterface;

class ListHelper extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Registry
     */
    protected $registry;
    /**
     * @var OrderFactory
     */
    protected $orderCollection;
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * ListHelper constructor.
     * @param Context $context
     * @param Registry $registry
     * @param OrderFactory $orderCollection
     * @param CustomerFactory $customerFactory
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        Registry $registry,
        OrderFactory $orderCollection,
        CustomerFactory $customerFactory,
        CustomerRepositoryInterface $customerRepository)
    {
        parent::__construct($context);
        $this->registry = $registry;
        $this->orderCollection = $orderCollection;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param $order
     * @return string
     */
    public function getPaymentMethod($order)
    {
        $payment = $order->getPayment();
        $paymentMethod = null;
        try {
            $paymentMethod = $payment->getMethodInstance();
        } catch (LocalizedException $e) {

        }
        if ($paymentMethod) {
            return $paymentMethod->getCode();
        }
        return '';
    }

    /**
     * @param $order
     * @return mixed
     */
    public function getStatus($order)
    {
        return $order->getStatus();
    }

    /**
     * @param $order
     * @return string
     */
    public function getOrderClass($order)
    {
        $status = $this->getStatus($order);
        $method = $this->getPaymentMethod($order);

        $combined = join('/', array($method, $status));
        if ($combined === 'mpay24/processing') {
            return 'warning';
        } else if ($combined === 'mpay24/canceled') {
            return 'deny';
        } else if ($combined === 'paypal_standard/pending_payment') {
            return 'deny';
        } else if ($combined === 'paypal_standard/canceled') {
            return 'deny';
        }
        return '';
    }

    public function getFilteredOrderCollection($statusFilter = null, $pageSize = null, $sort = "ASC"){
        $collection = $this->orderCollection->create()->getCollection();
        if(!empty($statusFilter)){
                $collection->addFieldToFilter('winline_review_state', array('in' => array($statusFilter)));
        }
        if(!empty($pageSize)){
            $collection->setPageSize($pageSize)->setCurPage(1);
        }
        $collection->setOrder('created_at', $sort);

        return $collection;
    }

    /**
     * @param $orderId
     * @return \Magento\Sales\Model\Order|bool
     */
    public function getOrderById($orderId){
        $collection = $this->orderCollection->create()->load($orderId);
        if(!empty($collection)){
            return $collection;
        }
        return false;
    }

    /**
     * /**
     * @param $customerId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer($customerId){
        try{
            return $this->customerRepository->getById($customerId);
        }catch (LocalizedException $e){
        }
        return null;
    }

    /**
     * @param $customerId
     * @param $accountNumber
     * @throws LocalizedException
     */
    public function saveCustomer($customerId, $accountNumber){
        $customer = $this->getCustomer($customerId);
        if($customer) {
            $customer->setCustomAttribute('account_number', $accountNumber);
            try{
                $this->customerRepository->save($customer);
            }catch (\Exception $e){
                throw new LocalizedException(new Phrase(__('Can not save customer with id %1.', $customerId)));
            }
        }
    }
}
