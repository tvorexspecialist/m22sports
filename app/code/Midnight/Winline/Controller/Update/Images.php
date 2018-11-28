<?php

namespace Midnight\Winline\Controller\Update;

use Magento\Framework\App\Action\Context;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;

class Images extends \Magento\Framework\App\Action\Action
{
    private $productCollection;
    private $image;
    private $readHandler;
    public function __construct(
        Context $context,
        Collection $productCollection,
        Image $image,
        ReadHandler $readHandler
    ) {
        parent::__construct($context);
        $this->productCollection = $productCollection;
        $this->image = $image;
        $this->readHandler = $readHandler;
    }

    public function execute()
    {
        $collection = $this->productCollection
            ->addAttributeToSelect('image')
            ->addAttributeToSelect('thumbnail')
            ->addAttributeToSelect('small_image');
        if ($collection->getSize()) {
            foreach ($collection as $product) {
                $this->readHandler->execute($product);
                if (!empty($product->getMediaGalleryImages()->getFirstItem())){
                    $file = $product->getMediaGalleryImages()->getFirstItem()->getFile();

                    if (empty($product->getImage()) || $product->getImage() == 'no_selection') {
                        $product->setImage($file);
                        $product->getResource()->saveAttribute($product, 'image');
                    }
                    if (empty($product->getSmallImage()) || $product->getSmallImage() == 'no_selection') {
                        $product->setSmallImage($file);
                        $product->getResource()->saveAttribute($product, 'small_image');
                    }
                    if (empty($product->getThumbnail()) || $product->getThumbnail() == 'no_selection') {
                        $product->setThumbnail($file);
                        $product->getResource()->saveAttribute($product, 'thumbnail');
                    }
                }
            }
        }
    }
}