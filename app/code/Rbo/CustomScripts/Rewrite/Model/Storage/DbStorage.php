<?php
namespace Rbo\CustomScripts\Rewrite\Model\Storage;

use Magento\UrlRewrite\Model\Storage\DbStorage as ParentDbStorage;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite as UrlRewriteData;

class DbStorage extends ParentDbStorage
{
    /**
     * {@inheritdoc}
     */
    protected function doReplace(array $urls)
    {
        foreach ($this->createFilterDataBasedOnUrls($urls) as $type => $urlData) {
            $urlData[UrlRewrite::ENTITY_TYPE] = $type;
            $this->deleteByData($urlData);
        }

        $data = [];
        foreach ($urls as $url) {
            $urlFound = $this->doFindOneByData(
                [
                    UrlRewriteData::REQUEST_PATH => $url->getRequestPath(),
                    UrlRewriteData::STORE_ID => $url->getStoreId()
                ]
            );
            if (isset($urlFound[UrlRewriteData::URL_REWRITE_ID])) {
                $newUrlRewrite = $url->getRequestPath() . '-' . $url->getEntityId();
                $url->setRequestPath($newUrlRewrite);
            }
            $data[] = $url->toArray();
        }
        try {
            $this->insertMultiple($data);
        } catch (\Magento\Framework\Exception\AlreadyExistsException $e) {
            /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[] $urlConflicted */
            $urlConflicted = [];
            foreach ($urls as $url) {
                $urlFound = $this->doFindOneByData(
                    [
                        UrlRewriteData::REQUEST_PATH => $url->getRequestPath(),
                        UrlRewriteData::STORE_ID => $url->getStoreId()
                    ]
                );
                if (isset($urlFound[UrlRewriteData::URL_REWRITE_ID])) {
                    $urlConflicted[$urlFound[UrlRewriteData::URL_REWRITE_ID]] = $url->toArray();
                }
            }
            if ($urlConflicted) {
                throw new \Magento\UrlRewrite\Model\Exception\UrlAlreadyExistsException(
                    __('URL key for specified store already exists.'),
                    $e,
                    $e->getCode(),
                    $urlConflicted
                );
            } else {
                throw $e->getPrevious() ?: $e;
            }
        }

        return $urls;
    }
}
