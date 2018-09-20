<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Api;

interface GuestTotalsInformationManagementInterface
{
    /**
     * Calculate quote totals based on quote and fee
     *
     * @param string $cartId
     * @param \Rbofee\Extrafee\Api\Data\TotalsInformationInterface $information
     * @param \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function calculate(
        $cartId,
        \Rbofee\Extrafee\Api\Data\TotalsInformationInterface $information,
        \Magento\Checkout\Api\Data\TotalsInformationInterface $addressInformation
    );
}
