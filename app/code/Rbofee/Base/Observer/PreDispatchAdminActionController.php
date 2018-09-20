<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Base
 */


namespace Rbofee\Base\Observer;

use Magento\Framework\Event\ObserverInterface;

class PreDispatchAdminActionController implements ObserverInterface
{
    /**
     * @var \Rbofee\Base\Model\FeedFactory
     */
    private $feedFactory;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    private $backendSession;

    public function __construct(
        \Rbofee\Base\Model\FeedFactory $feedFactory,
        \Magento\Backend\Model\Auth\Session $backendAuthSession
    ) {
        $this->feedFactory = $feedFactory;
        $this->backendSession = $backendAuthSession;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->backendSession->isLoggedIn()) {
            /** @var \Rbofee\Base\Model\Feed $feedModel */
            $feedModel = $this->feedFactory->create();
            $feedModel->checkUpdate();
            $feedModel->removeExpiredItems();
        }
    }
}
