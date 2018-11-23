<?php

namespace Rbo\Puch\Model;

use Magento\Framework\Model\AbstractModel;

class UrlStore extends AbstractModel
{
  public function getHomePageUrl() 
  {
    return "http://www.rbo.at/";
  }
}