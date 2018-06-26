<?php

namespace Rbo\Puch\Block;

use Magento\Cms\Api\BlockRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Catalog\Helper\Category;
use Magento\Store\Model\StoreManagerInterface;

class CmsBlocks extends Template
{
    private $blockRepository;

    private $categoryHelper;

    private $storeManager;

    public function __construct(
        BlockRepositoryInterface $blockRepository,
        Context $context,
        Category $categoryHelper,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->blockRepository = $blockRepository;
        $this->categoryHelper = $categoryHelper;
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    public function getContent($identifier)
    {
        try {
            $block = $this->blockRepository->getById($identifier);
            $content = $block->getContent();
        } catch (LocalizedException $e) {
            $content = false;
        }

        return $content;
    }

    public function getCategories(){
        return $this->categoryHelper->getStoreCategories();
    }
    public function getCategoryUrl($categoryId){
        return $this->categoryHelper->getCategoryUrl($categoryId);
    }

    public function getLogoUrl($filename){
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        $logoUrl = $mediaUrl . DIRECTORY_SEPARATOR . 'logo' . DIRECTORY_SEPARATOR . $filename;
        return $logoUrl;
    }
}