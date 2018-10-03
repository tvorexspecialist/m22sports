<?php

namespace Midnight\Orderreview\Controller\Adminhtml\ListController;

use Magento\Backend\App\Action;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Midnight\Orderreview\Model\Suggestion;
use Midnight\Orderreview\Helper\ListHelper;

class Index extends \Magento\Backend\App\Action
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
        if(!empty($_GET['opcache_clean'])){
            opcache_reset();
            opcache_reset();
        }
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Midnight_Orderreview::execute');
        $resultPage->getConfig()->getTitle()->prepend(__('Winline Orders'));
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