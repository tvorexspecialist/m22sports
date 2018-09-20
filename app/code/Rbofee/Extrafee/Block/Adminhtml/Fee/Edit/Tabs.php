<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Block\Adminhtml\Fee\Edit;

/**
 * * @author Rbo Developer
 */

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('rbofee_extrafee_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Fee Information'));
    }
}