<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model;

/**
 * Class TotalsInformation
 *
 * @author Rbo Developer
 */

use Magento\Framework\Model\AbstractExtensibleModel;
use Rbofee\Extrafee\Api\Data\TotalsInformationInterface;

class TotalsInformation extends AbstractExtensibleModel implements TotalsInformationInterface
{
    /**
     * @return mixed
     */
    public function getOptionsIds()
    {
        return $this->getData(self::OPTIONS_IDS);
    }

    /**
     * @param $optionsIds
     * @return $this
     */
    public function setOptionsIds($optionsIds)
    {
        return $this->setData(self::OPTIONS_IDS, $optionsIds);
    }

    /** @return int */
    public function getFeeId()
    {
        return $this->getData(self::FEE_ID);
    }

    /**
     * @param int $feeId
     * @return mixed
     */
    public function setFeeId($feeId)
    {
        return $this->setData(self::FEE_ID, $feeId);
    }
}