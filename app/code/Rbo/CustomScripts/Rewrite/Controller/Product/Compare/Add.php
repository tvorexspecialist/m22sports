<?php

namespace Rbo\CustomScripts\Rewrite\Controller\Product\Compare;

use Magento\Framework\Exception\NoSuchEntityException;

class Add extends \Magento\Catalog\Controller\Product\Compare\Add
{
    /**
     * Add item to compare list
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $resultRedirect->setRefererUrl();
        }

        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId && ($this->_customerVisitor->getId() || $this->_customerSession->isLoggedIn())) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productId, false, $storeId);
            } catch (NoSuchEntityException $e) {
                $product = null;
            }

            if ($product) {
                $item = $this->_compareItemFactory->create();
                $item->loadByProduct($product);
                $helper = $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class);
                if ($item->getId()) {
                    $item->delete();
                    $productName = $this->_objectManager->get(\Magento\Framework\Escaper::class)
                        ->escapeHtml($product->getName());
                    $this->messageManager->addSuccess(
                        __('You removed product %1 from the comparison list.', $productName)
                    );
                    $this->_eventManager->dispatch(
                        'catalog_product_compare_remove_product',
                        ['product' => $item]
                    );
                    $helper->calculate();
                    $resultRedirect = $this->resultRedirectFactory->create();
                    $resultRedirect->setPath($this->_redirect->getRefererUrl());
                    return $resultRedirect;
                }
                $this->_catalogProductCompareList->addProduct($product);
                $productName = $this->_objectManager->get(
                    \Magento\Framework\Escaper::class
                )->escapeHtml($product->getName());
                $this->messageManager->addSuccess(__('You added product %1 to the comparison list.', $productName));
                $this->_eventManager->dispatch('catalog_product_compare_add_product', ['product' => $product]);
            }

            $this->_objectManager->get(\Magento\Catalog\Helper\Product\Compare::class)->calculate();
        }
        return $resultRedirect->setRefererOrBaseUrl();
    }
}
