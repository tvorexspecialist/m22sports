<?php

namespace Midnight\Orderreview\Controller\Adminhtml\ListController;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Midnight\Orderreview\Model\Suggestion;
use Midnight\Orderreview\Helper\ListHelper;

class View extends \Magento\Backend\App\Action
{
    private $resultPageFactory;
    private $orderCollectionFactory;
    private $suggestion;
    private $listHelper;

    public function __construct(Action\Context $context,
                                PageFactory $pageFactory,
                                CollectionFactory $collectionFactory,
                                Suggestion $suggestion,
                                ListHelper $listHelper)
    {
        parent::__construct($context);
        $this->resultPageFactory = $pageFactory;
        $this->orderCollectionFactory = $collectionFactory;
        $this->suggestion = $suggestion;
        $this->listHelper = $listHelper;
    }

    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Midnight_Orderreview::execute');
        $resultPage->getConfig()->getTitle()->prepend(__('Winline Order View'));

        return $resultPage;
    }

    /**
     * Check Permission.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Midnight_Orderreview::listcontroller');
    }
}