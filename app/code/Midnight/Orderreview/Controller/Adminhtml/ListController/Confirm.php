<?php

namespace Midnight\Orderreview\Controller\Adminhtml\ListController;

use Magento\Backend\App\Action;
use Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\View\Result\PageFactory;
use \Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Midnight\Orderreview\Model\Suggestion;
use Midnight\Orderreview\Helper\ListHelper;

class Confirm extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    private $resultPageFactory;
    /**
     * @var CollectionFactory
     */
    private $orderCollectionFactory;
    /**
     * @var Suggestion
     */
    private $suggestion;
    /**
     * @var ListHelper
     */
    private $listHelper;

    /**
     * Confirm constructor.
     * @param Action\Context $context
     * @param PageFactory $pageFactory
     * @param CollectionFactory $collectionFactory
     * @param Suggestion $suggestion
     * @param ListHelper $listHelper
     */
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
        if ($this->getRequest()->getParam('id')) {
            $post = $this->getRequest()->getParams();


            $order = $this->listHelper->getOrderById($post['order_id']);

            $order->setCustomerPrefix($post['billing']['prefix']);
            $order->setCustomerFirstname($post['billing']['firstname']);
            $order->setCustomerLastname($post['billing']['lastname']);
            $order->setWinlineReviewState(\Midnight\Orderreview\Model\Review\State::READY_FOR_WINLINE);

            $billing = $order->getBillingAddress();
            $billing->setPrefix($post['billing']['prefix']);
            $billing->setFirstname($post['billing']['firstname']);
            $billing->setLastname($post['billing']['lastname']);
            $billing->setStreet($post['billing']['street']);
            $billing->setPostcode($post['billing']['postcode']);
            $billing->setCity($post['billing']['city']);
            $billing->setRegion($post['billing']['region']);
            $billing->setCountryId($post['billing']['country']);
            $billing->setTelephone($post['billing']['telephone']);

            $shipping = $order->getShippingAddress();
            $shipping->setPrefix($post['shipping']['prefix']);
            $shipping->setFirstname($post['shipping']['firstname']);
            $shipping->setLastname($post['shipping']['lastname']);
            $shipping->setStreet($post['shipping']['street']);
            $shipping->setPostcode($post['shipping']['postcode']);
            $shipping->setCity($post['shipping']['city']);
            $shipping->setRegion($post['shipping']['region']);
            $shipping->setCountryId($post['shipping']['country']);
            $shipping->setTelephone($post['shipping']['telephone']);
            if ($post['account_number']) {
                $order->setCustomerAccountNumber($post['account_number']);
                $customer_id = $order->getCustomerId();
            }

           try{
                $billing->save();
                $shipping->save();
                if (isset($customer_id)) {
                   $this->listHelper->saveCustomer($customer_id, $post['account_number']);
                }
                $order->save();

               $this->messageManager->addSuccessMessage(__('Order was saved successfully: ').$order->getIncrementId());
            }catch (LocalizedException $e){
                $this->messageManager->addErrorMessage(__('Can not save data. Error: '). $e->getMessage());
           }
        }
        $this->_redirect('orderreview/listcontroller/index/');
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