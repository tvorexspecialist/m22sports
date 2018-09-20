<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Api\Data;

interface TotalsInformationInterface
{
    const OPTIONS_IDS = 'options_ids';
    const FEE_ID = 'fee_id';

    /**
     * @return mixed
     */
    public function getOptionsIds();

    /**
     * @param mixed $feeOptionId
     * @return mixed
     */
    public function setOptionsIds($optionIds);

    /** @return int */
    public function getFeeId();

    /**
     * @param int $feeId
     * @return mixed
     */
    public function setFeeId($feeId);
}