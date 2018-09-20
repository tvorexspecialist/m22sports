<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

interface FeeSearchResultsInterface extends SearchResultsInterface
{
    /**
     * @return \Rbofee\Extrafee\Api\Data\FeeInterface[]
     */
    public function getItems();

    /**
     * @param \Rbofee\Extrafee\Api\Data\FeeInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
