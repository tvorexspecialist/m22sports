<?php

namespace Rbo\CustomScripts\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\View\Page\Config;
use Magento\Framework\Locale\Resolver;


class AddBodyClassPlugin implements ObserverInterface
{
    protected $config;

    protected $localeResolver;

    public function __construct(
        Config $config,
        Resolver $localeResolver
    ){
        $this->config = $config;
        $this->localeResolver = $localeResolver;
    }

    public function execute(Observer $observer){

        $locale = $this->localeResolver->getLocale();
        $this->config->addBodyClass($locale);
    }
}