<?php

namespace Rbo\CustomScripts\Plugin\Framework\View\Page\Config;

use Magento\Framework\View\Page\Config;
use \Magento\Framework\View\Page\Config\Renderer as ParentRenderer;
use Magento\Framework\View\Asset\GroupedCollection;
use Magento\Framework\View\Page\Config\Generator\Head;


class Renderer extends ParentRenderer
{
    protected $config;

    public function __construct(\Magento\Framework\View\Context $context,
                                Config $pageConfig,
                                \Magento\Framework\View\Asset\MergeService $assetMergeService,
                                \Magento\Framework\UrlInterface $urlBuilder,
                                \Magento\Framework\Escaper $escaper,
                                \Magento\Framework\Stdlib\StringUtils $string,
                                \Psr\Log\LoggerInterface $logger)
    {
        parent::__construct($pageConfig, $assetMergeService, $urlBuilder, $escaper, $string, $logger);
    }

    public function prepareFavicon()
    {
        $this->pageConfig->addPageAsset(
            $this->pageConfig->getDefaultFavicon(),
            ['attributes' => ['rel' => 'icon', 'type' => 'image/x-icon']],
            'icon'
        );
        $this->pageConfig->addPageAsset(
            $this->pageConfig->getDefaultFavicon(),
            ['attributes' => ['rel' => 'shortcut icon', 'type' => 'image/x-icon']],
            'shortcut-icon'
        );
    }
}