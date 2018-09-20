<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */


namespace Rbofee\Extrafee\Block\Adminhtml\Order\Create;

class Fee extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_rbofee_extrafee');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Additional Fees');
    }

    /**
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-rbofee-extrafee';
    }
}
