<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Api\Data;

interface FeesManagerInterface
{
    const TOTALS = 'totals';
    const FEES = 'fee';

    /**
     * @param \Rbofee\Extrafee\Api\Data\FeeInterface[] $fees
     * @return \Rbofee\Extrafee\Api\Data\FeesManagerInterface
     */
    public function setFees($fees);

    /**
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return \Rbofee\Extrafee\Api\Data\FeesManagerInterface
     */
    public function setTotals($totals);

    /**
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface[]
     */
    public function getFees();

    /**
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getTotals();
}