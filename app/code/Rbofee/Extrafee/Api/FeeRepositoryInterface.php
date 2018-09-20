<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Api;

interface FeeRepositoryInterface
{
    /**
     * Save
     *
     * @param \Rbofee\Extrafee\Api\Data\FeeInterface $fee
     * @param string[] $options
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface
     */
    public function save(\Rbofee\Extrafee\Api\Data\FeeInterface $fee, $options);

    /**
     * Get by id
     *
     * @param int $feeId
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface
     */
    public function getById($feeId);

    /**
     * Delete
     *
     * @param \Rbofee\Extrafee\Api\Data\FeeInterface $fee
     * @return bool true on success
     */
    public function delete(\Rbofee\Extrafee\Api\Data\FeeInterface $fee);

    /**
     * Delete by id
     *
     * @param int $feeId
     * @return bool true on success
     */
    public function deleteById($feeId);

    /**
     * Lists by quote
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface[]
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        \Magento\Quote\Api\Data\CartInterface $quote
    );

    /**
     * Lists
     *
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface[] Array of items.
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified cart does not exist.
     */
    public function getListItems();

    /**
     * @param $optionId
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface
     */
    public function getByOptionId($optionId);
}
