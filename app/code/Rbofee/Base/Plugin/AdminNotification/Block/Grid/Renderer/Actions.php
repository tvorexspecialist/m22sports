<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Plugin\AdminNotification\Block\Grid\Renderer;

use Magento\AdminNotification\Block\Grid\Renderer\Actions as NativeActions;

class Actions
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    public function aroundRender(
        NativeActions $subject,
        \Closure $proceed,
        \Magento\Framework\DataObject $row
    ) {
        $result = $proceed($row);
        if ($row->getData('is_rbofee')) {
            $result .= sprintf(
                '<a class="action" href="%s" title="%s">%s</a>',
                $this->urlBuilder->getUrl('rbobase/notification/frequency/'). 'action/less',
                __('Show less of these messages'),
                __('Show less of these messages')
            );
            $result .= sprintf(
                '<a class="action" href="%s" title="%s">%s</a>',
                $this->urlBuilder->getUrl('rbobase/notification/frequency/'). 'action/more',
                __('Show more of these messages'),
                __('Show more of these messages')
            );
            $result .= sprintf(
                '<a class="action" href="%s" title="%s">%s</a>',
                $this->urlBuilder->getUrl('adminhtml/system_config/edit/'). 'section/rbofee_base',
                __('Unsubscribe'),
                __('Unsubscribe')
            );
        }

        return  $result;
    }
}
