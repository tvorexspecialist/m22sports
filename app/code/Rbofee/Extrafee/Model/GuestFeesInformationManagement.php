<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model;

/**
 * Class GuestFeesInformationManagement
 *
 * @author Rbo Developer
 */

use Rbofee\Extrafee\Api\GuestFeesInformationManagementInterface;
use Rbofee\Extrafee\Api\FeesInformationManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestFeesInformationManagement implements GuestFeesInformationManagementInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;

    /** @var  FeesInformationManagementInterface */
    protected $feesInformationManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param FeesInformationManagementInterface $feesInformationManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        FeesInformationManagementInterface $feesInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->feesInformationManagement = $feesInformationManagement;
    }

    /**
     * @param string $cartId
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     *
     * @return \Rbofee\Extrafee\Api\Data\FeesManagerInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function collect(
        $cartId,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->feesInformationManagement->collect(
            $quoteIdMask->getQuoteId(),
            $addressInformation
        );
    }
}