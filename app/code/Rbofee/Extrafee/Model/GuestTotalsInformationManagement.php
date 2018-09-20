<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model;

/**
 * Class GuestTotalsInformationManagement
 *
 * @author Rbo Developer
 */

use Rbofee\Extrafee\Api\GuestTotalsInformationManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Rbofee\Extrafee\Api\TotalsInformationManagementInterface;
use Rbofee\Extrafee\Api\Data\TotalsInformationInterface;

class GuestTotalsInformationManagement implements GuestTotalsInformationManagementInterface
{
    /** @var QuoteIdMaskFactory */
    protected $quoteIdMaskFactory;

    /** @var  TotalsInformationManagementInterface */
    protected $totalsInformationManagement;

    /**
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param TotalsInformationManagementInterface $totalsInformationManagement
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        TotalsInformationManagementInterface $totalsInformationManagement
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->totalsInformationManagement = $totalsInformationManagement;
    }

    /**
     * @param string $cartId
     * @param TotalsInformationInterface $information
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function calculate(
        $cartId,
        TotalsInformationInterface $information,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    ) {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this->totalsInformationManagement->calculate(
            $quoteIdMask->getQuoteId(),
            $information,
            $addressInformation
        );
    }
}