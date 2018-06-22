<?php

namespace Rbo\Puch\Block;

use \Magento\Framework\View\Element\Html\Link;
use \Magento\Framework\View\Element\Template\Context;
use \Rbo\Puch\Model\UrlStore;

class HomepageUrl extends Link
{
  public $urlStore;
  
  public function __construct(Context $context, UrlStore $urlStore, array $data = [])
  {
    $this->urlStore = $urlStore;
    parent::__construct($context, $data);
  }

  public function getHomepageUrl()
  {
    return $this->urlStore->getHomepageUrl();
  }
}