<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Model\AdminNotification\Model\ResourceModel\Inbox\Collection;

class Expired extends \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection
{
    /**
     * @return \Magento\AdminNotification\Model\ResourceModel\Inbox\Collection\Unread
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addFieldToFilter('is_remove', 0)
             ->addFieldToFilter('is_rbofee', 1)
             ->addFieldToFilter('expiration_date', ['neq' => 'NULL'])
             ->addFieldToFilter('expiration_date', ['lt' => 'NOW()']);

        return $this;
    }
}
