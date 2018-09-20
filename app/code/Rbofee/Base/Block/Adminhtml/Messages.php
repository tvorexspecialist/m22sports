<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Block\Adminhtml;

class Messages extends \Magento\Backend\Block\Template
{
    const RBOFEE_BASE_SECTION_NAME = 'rbofee_base';
    /**
     * @var \Rbofee\Base\Model\AdminNotification\Messages
     */
    private $messageManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    private $request;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Rbofee\Base\Model\AdminNotification\Messages $messageManager,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->messageManager = $messageManager;
        $this->request = $request;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messageManager->getMessages();
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        $html  = '';
        if ($this->request->getParam('section') == self::RBOFEE_BASE_SECTION_NAME) {
            $html = parent::_toHtml();
        }

        return $html;
    }
}
