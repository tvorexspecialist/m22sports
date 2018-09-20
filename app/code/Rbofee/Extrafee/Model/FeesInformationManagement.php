<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Model;

use Rbofee\Base\Model\Serializer;
use Rbofee\Extrafee\Api\FeesInformationManagementInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Rbofee\Extrafee\Model\Data\FeesManagerFactory;
use Magento\Checkout\Model\TotalsInformationManagement as CheckoutTotalsInformationManagement;


class FeesInformationManagement implements FeesInformationManagementInterface
{
    /** @var CartRepositoryInterface  */
    protected $cartRepository;

    /** @var FeeRepository  */
    protected $feeRepository;

    /** @var SearchCriteriaBuilder */
    protected $searchCriteriaBuilder;

    /** @var FilterBuilder  */
    protected $filterBuilder;

    /** @var FilterGroupBuilder  */
    protected $filterGroupBuilder;

    /** @var SortOrderBuilder  */
    protected $sortOrderBuilder;

    /** @var CheckoutTotalsInformationManagement  */
    protected $checkoutTotalsInformationManagement;

    /** @var FeesManagerFactory  */
    protected $feesManagerFactory;
    /**
     * @var Serializer
     */
    private $serializerBase;

    /**
     * @param CartRepositoryInterface $cartRepository
     * @param FeeRepository $feeRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param FilterBuilder $filterBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param CheckoutTotalsInformationManagement $checkoutTotalsInformationManagement
     * @param FeesManagerFactory $feesManagerFactory
     * @param Serializer $serializerBase
     */
    public function __construct(
        CartRepositoryInterface $cartRepository,
        FeeRepository $feeRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        FilterBuilder $filterBuilder,
        SortOrderBuilder $sortOrderBuilder,
        CheckoutTotalsInformationManagement $checkoutTotalsInformationManagement,
        FeesManagerFactory $feesManagerFactory,
        Serializer $serializerBase
    ) {
        $this->cartRepository = $cartRepository;
        $this->feeRepository = $feeRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->checkoutTotalsInformationManagement = $checkoutTotalsInformationManagement;
        $this->feesManagerFactory = $feesManagerFactory;
        $this->serializerBase = $serializerBase;
    }

    /**
     * @param int $cartId
     * @param string $paymentMethod
     * @param string $billingCountry
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @param bool $sameAsBilling
     * @return \Rbofee\Extrafee\Api\Data\FeesManagerInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function collect(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        $this->checkoutTotalsInformationManagement->calculate($cartId, $addressInformation);
        $quote = $this->cartRepository->get($cartId);

        //getting and validating fees according to current quote
        $fees = $this->collectQuote($quote);

        //recalculate quote totals according to just loaded extra fees
        $quote->setTotalsCollectedFlag(false);
        $totals = $this->checkoutTotalsInformationManagement->calculate($cartId, $addressInformation);
        if (!count($fees)) {
            $segments = $totals->getTotalSegments();
            unset($segments['rbofee_extrafee']);
            $totals->setTotalSegments($segments);
        }

        $feesManager = $this->feesManagerFactory->create()
            ->setFees($fees)
            ->setTotals($totals);

        return $feesManager;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface[]
     */
    public function collectQuote(
        \Magento\Quote\Model\Quote $quote
    ) {
        $filterEnabled = $this->filterBuilder->setField('enabled')
            ->setValue('1')
            ->setConditionType('eq')
            ->create();

        $filterStore = $this->filterBuilder->setField('store_id')
            ->setValue(['0', $quote->getStoreId()])
            ->setConditionType('in')
            ->create();

        $filterCustomerGroup = $this->filterBuilder->setField('customer_group_id')
            ->setValue($quote->getCustomerGroupId())
            ->setConditionType('eq')
            ->create();

        $filterGroup = $this->filterGroupBuilder
            ->addFilter($filterEnabled)
            ->addFilter($filterStore)
            ->addFilter($filterCustomerGroup)
            ->create();

        $criteria = $this->searchCriteriaBuilder->create()
            ->setFilterGroups([$filterGroup])
            ->setSortOrders(
                $this->sortOrderBuilder->create()
                    ->setField('sort_order')
                    ->getDirection('ASC')
            );

        $searchResults = $this->feeRepository->getList(
            $criteria,
            $quote
        );

        $resultItems = [];
        foreach ($searchResults->getItems() as $item) {
            if (is_array($item['base_options']) && !empty($item['base_options'])) {
                $unserializeOptions = [];
                foreach ($item['base_options'] as $baseOption) {
                    try {
                        $unserializeOptions[] = $this->serializerBase->unserialize($baseOption);
                    } catch (\Exception $exception) {
                        $unserializeOptions[] = $baseOption;
                    }
                }
                $item['base_options'] = $unserializeOptions;
                $resultItems[] = $item;
            }
        }

        if (!empty($resultItems)) {
            $searchResults->setItems($resultItems);
        }

        return $searchResults->getItems();
    }
}
