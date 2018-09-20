<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Plugin\AdminNotification\Block\Grid\Renderer;

use Magento\AdminNotification\Block\Grid\Renderer\Notice as NativeNotice;

class Notice
{
    public function aroundRender(
        NativeNotice $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $row
    ) {
        $result = $proceed($row);

        $rbofeeLogo = $row->getData('is_rbofee') ? ' rbofee-grid-logo' : '';
        $result = '<div class="rbobase-grid-message' . $rbofeeLogo .'">' . $result . '</div>';

        return  $result;
    }
}
