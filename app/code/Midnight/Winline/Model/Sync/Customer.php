<?php

namespace Midnight\Winline\Model\Sync;

use Braintree\Exception;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Framework\Filesystem\DirectoryList;
use Midnight\Winline\Model\CustomerFactory;

use Magento\Customer\Model\CustomerFactory as MagentoCustomerFactory;
use Magento\Customer\Model\GroupFactory;
use Magento\Framework\Filesystem\Io\File;
use \Magento\Customer\Api\AddressRepositoryInterface;
use \Magento\Customer\Model\AddressFactory;
use \Magento\Store\Model\StoreManagerInterface;
use Midnight\Winline\Logger\Logger;
/**
 * Class Customer
 * @package Midnight\Winline\Model\Sync
 */

class Customer
{
    const LAST_SYNC_FILE = 'customer_last_sync.txt';

    /**
     * @var string
     */
    private static $regularGroupId;
    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @var DateTime
     */
    private $dateTime;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var CustomerFactory
     */
    private $customerFactory;
    /**
     * @var MagentoCustomerFactory
     */
    private $magentoCustomers;
    /**
     * @var GroupFactory
     */
    private $groupFactory;
    /**
     * @var File
     */
    private $file;
    /**
     * @var AddressRepositoryInterface
     */
    private $addressRepository;
    /**
     * @var StoreManagerInterface
     */
    private $storeManagerInterface;
    /**
     * @var AddressFactory
     */
    private $addressFactory;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * Customer constructor.
     * @param Transaction $transaction
     * @param DateTime $dateTime
     * @param DirectoryList $directoryList
     * @param CustomerFactory $customerFactory
     * @param MagentoCustomerFactory $magentoCustomers
     * @param GroupFactory $groupFactory
     * @param File $file
     * @param AddressRepositoryInterface $addressRepository
     * @param StoreManagerInterface $storeManager
     * @param AddressFactory $addressFactory
     * @param Logger $logger
     */
    public function __construct(Transaction $transaction,
                                DateTime $dateTime,
                                DirectoryList $directoryList,
                                CustomerFactory $customerFactory,
                                MagentoCustomerFactory $magentoCustomers,
                                GroupFactory $groupFactory,
                                File $file,
                                AddressRepositoryInterface $addressRepository,
                                StoreManagerInterface $storeManager,
                                AddressFactory $addressFactory,
                                Logger $logger)
    {
        $this->transaction = $transaction;
        $this->dateTime = $dateTime;
        $this->directoryList = $directoryList;
        $this->customerFactory = $customerFactory;
        $this->magentoCustomers = $magentoCustomers;
        $this->groupFactory = $groupFactory;
        $this->file = $file;
        $this->addressRepository = $addressRepository;
        $this->storeManagerInterface = $storeManager;
        $this->addressFactory = $addressFactory;
        $this->logger = $logger;
    }

    /**
     * Sync products data
     * @return void
     */
    public function sync()
    {
        $now = $now = new \DateTimeImmutable();
        $defaultMemoryLimit = ini_get('memory_limit');
        ini_set('memory_limit', '512M');
        $winlineCustomers = $this->getUpdatedCustomers();

        if($winlineCustomers->count() > 0) {
            foreach ($winlineCustomers as $winlineCustomer) {
                try {
                    $this->updateCustomer($winlineCustomer);
                } catch (Exception $exception) {
                    continue;
                }
            }
        }
        ini_set('memory_limit', $defaultMemoryLimit);
        $this->setLastSync($now);
    }

    /**
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     */
    private function getUpdatedCustomers()
    {
        $customersCollection = $this->customerFactory->create()->getCollection();
        $lastSync = $this->getLastSync();

        if ($lastSync) {
            $customersCollection->addFieldToFilter(
                \Midnight\Winline\Model\Customer::FIELD_UPDATED,
                ['gt' => $lastSync->format('Y-m-d H:i:s')]
            );
        }
        return $customersCollection;
    }

    /**
     * @return bool|\DateTimeImmutable
     */
    private function getLastSync()
    {
        $path = $this->directoryList->getPath('var');
        $file = $path . '/' . self::LAST_SYNC_FILE;
        if(file_exists($file)) {
            $lastSync = file_get_contents($file);
            if (false === $lastSync) {
                return false;
            }
            return new \DateTimeImmutable('@' . $lastSync);
        }
        return false;
    }

    /**
     * @param \Midnight\Winline\Model\Customer $winlineCustomer
     * @return bool|\Magento\Framework\DataObject|null
     */
    private function updateCustomer(\Midnight\Winline\Model\Customer $winlineCustomer)
    {
        if ($winlineCustomer->getPaymentMethod() === \Midnight\Winline\Model\Customer::PAYMENT_METHOD_INVOICE) {

            $magentoCustomer = $this->getMagentoCustomer($winlineCustomer);
            if (null !== $magentoCustomer) {
                try {
                    $magentoCustomer->setGroupId($this->getRegularGroupId());
                    $magentoCustomer->setPrefix($winlineCustomer->getPrefix());
                    $magentoCustomer->setFirstname($winlineCustomer->getFirstname());
                    $magentoCustomer->setLastname($winlineCustomer->getLastname());
                    $customer = $magentoCustomer->save();
                    $customerAddress = $this->getMagentoCustomerAddress($magentoCustomer);
                    if (!empty($customerAddress)) {
                        $this->updateCustomerAddress($winlineCustomer, $customerAddress);
                    } else {
                        $this->createCustomerAddress($customer, $winlineCustomer);
                    }
                    return $magentoCustomer;
                }catch (LocalizedException $e){
                    $this->logger->err('Can not save winline customer with account number '.$magentoCustomer->getAccountNumber().'! Error: '.$e->getMessage());
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * @param $winlineCustomer
     * @return bool|\Magento\Customer\Api\Data\AddressInterface
     */
    private function getMagentoCustomerAddress($winlineCustomer){
        $billingAddressId = $winlineCustomer->getDefaultBilling();
        try{
            $customerAddress = $this->addressRepository->getById($billingAddressId);
            return $customerAddress;
        }catch (LocalizedException $e){
            return false;
        }
    }

    /**
     * @param $magentoCustomer
     * @param $winlineCustomer
     * @return bool
     */
    private function createCustomerAddress($magentoCustomer, $winlineCustomer){
        try {
            $addressFactory = $this->addressFactory->create()
                ->setCustomerId($magentoCustomer->getId())
                ->setIsDefaultBilling('1')
                ->setIsDefaultShipping('1')
                ->setSaveInAddressBook('1');
            $insetData = $this->insertAddressData($winlineCustomer, $addressFactory);
            $insetData->save();
        }catch (Exception $exception){
        }
        return false;
    }

    /**
     * @param $winlineCustomer
     * @param \Magento\Customer\Api\Data\AddressInterface $customerAddress
     * @return bool|\Magento\Customer\Api\Data\AddressInterface
     */
    private function updateCustomerAddress($winlineCustomer, \Magento\Customer\Api\Data\AddressInterface $customerAddress){
        try {
            $insetData = $this->insertAddressData($winlineCustomer, $customerAddress);
            $this->addressRepository->save($insetData);
        }catch (Exception $exception){
        }
        return false;
    }

    /**
     * @param $winlineCustomer
     * @param $customerAddress
     * @return \Magento\Customer\Api\Data\AddressInterface
     */
    public function insertAddressData($winlineCustomer, $customerAddress){
        $customerAddress->setFirstname($winlineCustomer->getFirstname())
            ->setPrefix($winlineCustomer->getPrefix())
            ->setLastname($winlineCustomer->getLastname())
            ->setCountryId($winlineCustomer->getCountry())
            ->setPostcode($winlineCustomer->getPostcode())
            ->setCity($winlineCustomer->getCity())
            ->setTelephone(!empty($winlineCustomer->getPhone()) ? $winlineCustomer->getPhone() : '1111111111')
            ->setStreet([0 => $winlineCustomer->getStreet()]);
        return $customerAddress;
    }
    /**
     * @param \Midnight\Winline\Model\Customer $winlineCustomer
     * @return \Magento\Framework\DataObject|null
     */
    private function getMagentoCustomer(\Midnight\Winline\Model\Customer $winlineCustomer)
    {

        $magentoCustomerCollection = $this->magentoCustomers->create()->getCollection();
        $magentoCustomerCollection
            ->addFieldToSelect('account_number')
            ->addFieldToFilter('account_number', $winlineCustomer->getAccountNumber());
        $magentoCustomer = $magentoCustomerCollection->getFirstItem();
        if ($magentoCustomer->isEmpty()) {
            return null;
        }
        return $magentoCustomer;
    }

    /**
     * @return mixed|string
     */
    private function getRegularGroupId()
    {
        if (!self::$regularGroupId) {
            self::$regularGroupId = $this->getCustomerGroupModel()->load('Regular', 'customer_group_code')->getId();
        }
        return self::$regularGroupId;
    }

    /**
     * @return \Magento\Customer\Model\Group
     */
    private function getCustomerGroupModel()
    {
        return $this->groupFactory->create();
    }

    /**
     * @param \DateTimeInterface $dateTime
     */
    private function setLastSync(\DateTimeInterface $dateTime)
    {
        $path = $this->directoryList->getPath('var');
        file_put_contents($path . '/' . self::LAST_SYNC_FILE, $dateTime->getTimestamp());
    }
}
