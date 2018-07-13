<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_AbandonedCart
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\AbandonedCart\Controller\Adminhtml\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Quote\Model\ResourceModel\Quote\CollectionFactory;
use Magento\Store\Model\Website;
use Mageplaza\AbandonedCart\Helper\Data;
use Mageplaza\AbandonedCart\Model\AbandonedCart;
use Psr\Log\LoggerInterface;

/**
 * Class Test
 * @package Mageplaza\AbandonedCart\Controller\Adminhtml\Index
 */
class Test extends Action
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var AbandonedCart
     */
    protected $abandonedCart;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var CollectionFactory
     */
    protected $quoteCollection;

    /**
     * Test constructor.
     * @param Context $context
     * @param Data $helper
     * @param LoggerInterface $logger
     * @param AbandonedCart $abandonedCart
     * @param JsonHelper $jsonHelper
     * @param CollectionFactory $quoteCollection
     */
    public function __construct(
        Context $context,
        Data $helper,
        LoggerInterface $logger,
        AbandonedCart $abandonedCart,
        JsonHelper $jsonHelper,
        CollectionFactory $quoteCollection
    )
    {
        parent::__construct($context);

        $this->helper          = $helper;
        $this->logger          = $logger;
        $this->abandonedCart   = $abandonedCart;
        $this->jsonHelper      = $jsonHelper;
        $this->quoteCollection = $quoteCollection;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $result['status'] = false;

        try {
            /** @var \Magento\Quote\Model\ResourceModel\Quote\Collection $quoteCollection */
            $quoteCollection = $this->quoteCollection->create()
                ->addFieldToFilter('is_active', 1)
                ->addFieldToFilter('customer_id', ['null' => true])
                ->addFieldToFilter('items_count', ['gt' => 0]);

            /** Check store id & website id */
            if ($storeId = $this->getRequest()->getParam('store')) {
                $storeName = $this->helper->getStoreManager()->getStore($storeId)->getName();
                $quoteCollection->addFieldToFilter('store_id', $storeId);
            } else if ($websiteId = $this->getRequest()->getParam('website')) {
                /** @var Website $website */
                $website   = $this->helper->getStoreManager()->getWebsite($websiteId);
                $storeName = $website->getName();
                $quoteCollection->addFieldToFilter('store_id', ['in' => $website->getStoreIds()]);
            }

            if (!$quoteCollection->getSize()) {
                throw new LocalizedException(isset($storeName)
                    ? __('There is no abandoned cart available for %1.', '<strong>' . $storeName . '</strong>')
                    : __('There is no abandoned cart available.')
                );
            }

            /** @var \Magento\Quote\Model\Quote $quote */
            $quote = $quoteCollection->getFirstItem();
            $quote->setCustomerEmail($this->getRequest()->getParam('test_email'));

            $config = [
                'sender'     => $this->getRequest()->getParam('sender'),
                'template'   => $this->getRequest()->getParam('template'),
                'ignore_log' => true
            ];

            $coupon = [];
            if ((bool)$this->getRequest()->getParam('coupon')) {
                $coupon = $this->abandonedCart->createCoupon($quote->getStoreId());
            }

            $this->abandonedCart->sendMail($quote, $config, 'test_email', $coupon);

            $result['status']  = true;
            $result['content'] = __('Sent successfully! Please check your email box.');
        } catch (LocalizedException $e) {
            $result['content'] = $e->getMessage();
        } catch (\Exception $e) {
            $result['content'] = __('There is an error occurred while sending email. Please try again later.');
            $this->logger->critical($e);
        }

        return $this->getResponse()->representJson($this->jsonHelper->jsonEncode($result));
    }
}
