<?php

namespace Midnight\Orderreview\Controller\Adminhtml\ListController;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Midnight\Orderreview\Model\Suggestion;
use Midnight\Orderreview\Helper\ListHelper;

class Dismiss extends \Magento\Backend\App\Action
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

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws LocalizedException
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            throw new LocalizedException(new \Magento\Framework\Phrase('Got no id.'));
        }
        $order = $this->listHelper->getOrderById($id);
        if (!$order) {
            throw new LocalizedException(new \Magento\Framework\Phrase('Could not find order ' . $id));
        }
        try {
            $order->setWinlineReviewState(\Midnight\Orderreview\Model\Review\State::DISMISSED);
            $order->save();
        }catch (LocalizedException $e){
            $this->messageManager->addErrorMessage(__('Can not change order winline state!'));
        }
        $this->messageManager->addSuccessMessage(__('Die Bestellung wurde entfernt.'));
        $this->_redirect('orderreview/listcontroller/index');
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