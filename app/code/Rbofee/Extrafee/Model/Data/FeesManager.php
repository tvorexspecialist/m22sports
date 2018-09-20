<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model\Data;

/**
 * Class FeesManager
 *
 * @author Rbo Developer
 */

class FeesManager extends \Magento\Framework\Model\AbstractExtensibleModel implements \Rbofee\Extrafee\Api\Data\FeesManagerInterface
{
    /**
     * @param \Rbofee\Extrafee\Api\Data\FeeInterface[] $fees
     * @return \Rbofee\Extrafee\Api\Data\FeesManagerInterface
     */
    public function setFees($fees)
    {
        return $this->setData(self::FEES, $fees);
    }

    /**
     * @param \Magento\Quote\Api\Data\TotalsInterface $totals
     * @return \Rbofee\Extrafee\Api\Data\FeesManagerInterface
     */
    public function setTotals($totals)
    {
        return $this->setData(self::TOTALS, $totals);
    }

    /**
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface[]
     */
    public function getFees()
    {
        return $this->getData(self::FEES);
    }

    /**
     * @return \Magento\Quote\Api\Data\TotalsInterface
     */
    public function getTotals()
    {
        return $this->getData(self::TOTALS);
    }
}