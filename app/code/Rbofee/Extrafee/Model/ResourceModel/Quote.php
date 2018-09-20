<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Model\ResourceModel;

/**
 * Class Quote
 *
 * @author Rbo Developer
 */

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

class Quote extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('rbofee_extrafee_quote', 'entity_id');
    }
}