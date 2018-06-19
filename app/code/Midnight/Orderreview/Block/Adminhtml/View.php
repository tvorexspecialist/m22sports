<?php

namespace Midnight\Orderreview\Block\Adminhtml;

use Magento\Framework\View\Element\Template;

class View extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    public function __construct(Template\Context $context,
                                array $data = [])
    {
        parent::__construct($context, $data);
    }
}