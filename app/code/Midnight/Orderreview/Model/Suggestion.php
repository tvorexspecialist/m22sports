<?php

namespace Midnight\Orderreview\Model;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Midnight\Winline\Model\CustomerFactory;

class Suggestion
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;
    /**
     * @var CustomerFactory
     */
    private $winlineCustomer;

    /**
     * Suggestion constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param CustomerFactory $customerFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        CustomerFactory $customerFactory)
    {
        $this->customerRepository = $customerRepository;
        $this->winlineCustomer = $customerFactory;
    }


    /**
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    public function getSuggestions($order)
    {
        try {
            $pools = array();
            $return = array();
            if(!empty($order->getCustomerId())) {
                $customer = $this->customerRepository->getById($order->getCustomerId());
                // Match account number
                if(!empty($customer->getCustomAttribute('account_number'))){
                    $pools[] = $this->getCustomerCollection()
                        ->addFieldToFilter(\Midnight\Winline\Model\Customer::FIELD_ACCOUNT_NUMBER, $customer->getCustomAttribute('account_number')->getValue());
                }
            }
            // Match email
            $pools[] = $this->getCustomerCollection()
                ->addFieldToFilter(\Midnight\Winline\Model\Customer::FIELD_EMAIL, $order->getCustomerEmail());

            // Combine pools
            foreach ($pools as $pool) {
                /** @var $pool \Midnight\Winline\Model\Customer[] */
                foreach ($pool as $winlineCustomer) {
                    $return[$winlineCustomer->getId()] = $winlineCustomer;
                }
            }
        } catch (LocalizedException $exception) {
            $return = null;
        }
        return $return;
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    public function getCustomerCollection()
    {
        return  $this->winlineCustomer->create()->getCollection();
    }
}
