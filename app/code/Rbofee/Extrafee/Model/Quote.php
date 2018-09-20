<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model;

/**
 * Class Quote
 *
 * @author Rbo Developer
 */

use Magento\Framework\Model\AbstractModel;

use Magento\Framework\DataObject\IdentityInterface;

class Quote extends AbstractModel implements IdentityInterface
{
    /**
     * Fee cache tag
     */
    const CACHE_TAG = 'rbofee_extrafee_quote';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Rbofee\Extrafee\Model\ResourceModel\Quote');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
}
