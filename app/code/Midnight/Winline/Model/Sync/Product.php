<?php

namespace Midnight\Winline\Model\Sync;

use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Midnight\Winline\Model\ResourceModel\Product as ResourceProduct;
use Midnight\Winline\Logger\Logger;
use \Magento\Framework\App\Filesystem\DirectoryList;
use \Magento\Framework\Phrase;
use \Magento\Store\Model\StoreManagerInterface;
use \Magento\Eav\Model\Entity\Type;
use Magento\Framework\Stdlib\DateTime;
use Magento\Catalog\Api\CategoryLinkManagementInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use \Magento\Catalog\Model\ProductRepository;
use \Magento\Framework\Api\SearchCriteriaInterface;
use \Magento\Framework\Api\SearchCriteriaBuilder;
use \Magento\Catalog\Model\Product as MagentoProduct;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\FilterBuilder;
use \Magento\Catalog\Model\Product\Attribute\Source\Status;
use \Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\CategoryFactory;
use \Magento\Framework\App\State;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Image;
use Magento\Catalog\Model\Product\Gallery\ReadHandler;

class Product
{
    /**
     * @var string
     */
    private $checksumFields = [
        'Artikelnummer',
        'Bestellbar',
        'Bezeichnung',
        'Artikelgruppe',
        'Artikeluntergruppe',
        'Langtext1',
        'Langtext2',
        'Notiz1',
        'Notiz2',
        'Notiz3',
        'Notiz4',
        'Notiz5',
        'Notiz6',
        'Notiz7',
        'Notiz8',
        'Notiz9',
        'Notiz10',
        'Grafikfile',
        'Gewicht',
        'Raumgewicht',
        'Erloeskonto',
        'Steuersatzzeile',
        'Webartikel',
        'DatumdErstanlage',
        'DatumdletztenAenderung',
        'Inaktiv',
        'Reparatur'
    ];
    /**
     * @var array
     */
    protected $taxClassMap = [
        '1' => '5',
        '2' => '6',
    ];
    /**
     * @var int
     */
    private $attributeSetId;
    /**
     * @var array
     */
    private $websiteIDs;
    /**
     * @var ProductFactory
     */
    private $magentoProducts;
    /**
     * @var ResourceProduct
     */
    private $winlineProduct;
    /**
     * @var Logger
     */
    private $logger;
    /**
     * @var DirectoryList
     */
    private $directoryList;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Type
     */
    private $eavType;
    /**
     * @var CategoryLinkManagementInterface
     */
    private $categoryApiInterface;
    /**
     * @var StockRegistryInterface
     */
    private $stockItem;
    /**
     * @var MagentoProduct
     */
    private $magentoProductObj;
    /**
     * @var FilterGroup
     */
    private $filterGroup;
    /**
     * @var FilterBuilder
     */
    private $filterBuilder;
    /**
     * @var Status
     */
    private $productStatus;
    /**
     * @var Visibility
     */
    private $productVilibility;
    /**
     * @var ProductRepository
     */
    private $productRepository;
    /**
     * @var SearchCriteriaInterface
     */
    private $searchCriteria;
    /**
     * @var CategoryFactory
     */
    private $categoryFactory;
    /**
     * @var State
     */
    private $state;
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;
    /**
     * @var \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory
     */
    private $attributeSetCollection;
    /**
     * @var Image
     */
    private $image;
    /**
     * @var ReadHandler
     */
    private $readHandler;

    /**
     * Product constructor.
     * @param ProductFactory $magentoProductFactory
     * @param ResourceProduct $winlineProduct
     * @param Logger $logger
     * @param DirectoryList $directoryList
     * @param StoreManagerInterface $storeManager
     * @param Type $eavType
     * @param CategoryLinkManagementInterface $categoryLinkManagement
     * @param StockRegistryInterface $stockItem
     * @param MagentoProduct $product
     * @param FilterGroup $filterGroup
     * @param FilterBuilder $filterBuilder
     * @param Status $productStatus
     * @param Visibility $visibility
     * @param ProductRepository $productRepository
     * @param SearchCriteriaInterface $criteria
     * @param CategoryFactory $categoryFactory
     * @param State $state
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param Action
     */
    public function __construct(ProductFactory $magentoProductFactory,
                                ResourceProduct $winlineProduct,
                                Logger $logger,
                                DirectoryList $directoryList,
                                StoreManagerInterface $storeManager,
                                Type $eavType,
                                CategoryLinkManagementInterface $categoryLinkManagement,
                                StockRegistryInterface $stockItem,
                                MagentoProduct $product,
                                FilterGroup $filterGroup,
                                FilterBuilder $filterBuilder,
                                Status $productStatus,
                                Visibility $visibility,
                                ProductRepository $productRepository,
                                SearchCriteriaInterface $criteria,
                                CategoryFactory $categoryFactory,
                                State $state,
                                SearchCriteriaBuilder $searchCriteriaBuilder,
                                \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $attributeSetCollection,
                                Image $image,
                                ReadHandler $readHandler)
    {
       $this->magentoProducts = $magentoProductFactory;
       $this->winlineProduct = $winlineProduct;
       $this->logger = $logger;
       $this->directoryList = $directoryList;
       $this->storeManager = $storeManager;
       $this->eavType = $eavType;
       $this->categoryApiInterface = $categoryLinkManagement;
       $this->stockItem = $stockItem;
       $this->magentoProductObj = $product;
       $this->filterGroup = $filterGroup;
       $this->filterBuilder = $filterBuilder;
       $this->productStatus = $productStatus;
       $this->productVilibility = $visibility;
       $this->productRepository = $productRepository;
       $this->searchCriteria = $criteria;
       $this->categoryFactory = $categoryFactory;
       $this->state = $state;
       $this->searchCriteriaBuilder = $searchCriteriaBuilder;
       $this->attributeSetCollection = $attributeSetCollection;
       $this->image = $image;
       $this->readHandler = $readHandler;
    }

    /**
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Zend_Db_Statement_Exception
     */
    public function sync()
    {;
        $skus = $this->getAsyncProductSkus();
        $numSkus = count($skus);
        if ($numSkus === 0) {
            $this->log(null, 'Everything is up to date');
            return;
        }
        $this->log(null, sprintf('Syncing %s products', $numSkus));
        foreach ($skus as $sku) {
            $this->syncProduct($sku);
        }

        $this->log(null, 'All done');
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Zend_Db_Statement_Exception
     */
    public function startSyncAll()
    {
        $this->createSyncAllFile();
    }

    /**
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function syncAll()
    {
        if (!$this->syncAllFileExists()) {
            return;
        }
        $this->processSyncAllFile();
    }

    /**
     * @param int $limit
     * @return array
     * @throws \Zend_Db_Statement_Exception
     */
    private function getAsyncProductSkus($limit = 50)
    {
        $skus = [];
        $magentoProducts =  $this->getProductFactory()
            ->getCollection()
            ->addFieldToSelect('winline_checksum');
        $magentoChecksums = [];
        foreach ($magentoProducts as $magentoProduct) {
            $magentoChecksums[$magentoProduct->getSku()] = $magentoProduct->getWinlineChecksum();
        }
        $winlineProducts = $this->getWinlineConnection()
            ->query('SELECT
                          TRIM(Artikelnummer) AS sku,
                          DatumdletztenAenderung AS updated_at,
                          ' . $this->getChecksumSql() . ' AS checksum
                      FROM ExportWebArtikel')
            ->fetchAll();

        foreach ($winlineProducts as $winlineProduct) {
            $sku = $winlineProduct['sku'];
            if(!empty($magentoChecksums[$sku])) {
                if ($magentoChecksums[$sku] != $winlineProduct['checksum']) {
                    $skus[] = $sku;
                };
            }else{
                $skus[] = $sku;
            }
            if (count($skus) >= $limit) {
                break;
            }
        }
        return $skus;
    }

    /**
     * @return MagentoProduct
     */
    public function getProductFactory(){
        return $this->magentoProducts->create();
    }

    /**
     * @param $sku
     * @return bool
     * @throws \Zend_Db_Statement_Exception
     */
    public function syncProduct($sku)
    {
        if (!$sku) {
            $this->log($sku, 'No SKU given', 'ERR');
        }
        $this->log($sku, 'Start');
        //$product = $this->getProductFactory()->setStoreId(0)->loadByAttribute('sku', $sku);


        $data = $this->getWinlineProductData($sku);
        if ($this->getProductBySku($sku)) {
            $product = $this->getProductBySku($sku);
            $product = $this->updateProduct($product, $data);
            $this->validate($product);
            $product = $this->productRepository->save($product);
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
            $this->updateStockItem($product);
            $this->syncCategories($product, $data);
        } else {
            $product = $this->createProduct($data);
            $this->validate($product);
            try {
               $product = $product->save();
                $this->updateStockItem($product);
                $this->syncCategories($product, $data);
            } catch (LocalizedException $e) {
                $this->log($sku, 'Can not update product. Error:' .$e->getMessage());
            }
        }
        $this->log($sku, 'Done');
    }

    private function getProductBySku($sku)
    {
        try {
            return $this->productRepository->get($sku);
        } catch (NoSuchEntityException $e)
        {
            return false;
        }
    }
    /**
     * @return string
     */
    private function getChecksumSql()
    {
        return sprintf('MD5(CONCAT_WS(%s))', join(', ', $this->checksumFields));
    }

    /**
     * @return false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    private function getWinlineConnection()
    {
        return $this->winlineProduct->getConnection();
    }

    /**
     * @param $sku
     * @return array|mixed
     * @throws \Zend_Db_Statement_Exception
     */
    private function getWinlineProductData($sku)
    {
        $connection = $this->getWinlineConnection();
        $sql = 'SELECT
                  product.*,
                  TRIM(product.Artikelnummer) AS Artikelnummer,
                  price.Preis1 AS price,
                  ' . $this->getChecksumSql() . ' AS checksum
                FROM ExportWebArtikel product
                LEFT JOIN ExportWebPreise price
                    ON TRIM(price.ArtikelnummerUntergruppe) = TRIM(product.Artikelnummer)
                        AND price.Preisliste = 2
                        AND price.Preisart = 1
                WHERE TRIM(product.Artikelnummer) = :sku LIMIT 1';
        $data = $connection
            ->query($sql, [':sku' => $sku])
            ->fetch();
        if (false === $data) {
            $this->log($sku, 'Couldn\'t find Winline product "%s".'.$sku, 'ERR');
        }

        $data = $this->sanitizeWinlineData($data);

        return $data;
    }

    /**
     * @param MagentoProduct $product
     * @param array $data
     * @return MagentoProduct
     * @throws \Exception
     */
    private function updateProduct(\Magento\Catalog\Model\Product $product, array $data)
    {
        $before = $product->getData();
        $this->hydrateProduct($product, $data);

        $after = $product->getData();
        // Check types of $data items
        foreach ($before as $key => $val) {
            if (null === $val) {
                continue;
            }
            $typeBefore = gettype($val);
            $typeAfter = gettype($after[$key]);
            if ($typeBefore !== $typeAfter) {
                $message = sprintf('Wrong type: %s (%s/%s)', $key, $typeBefore, $typeAfter);
                $this->log($data['Artikelnummer'], $message, 'warning');
            }
        }
        return $product;
    }

    private function updateImages()
    {

    }

    /**
     * @param MagentoProduct $product
     * @param array $data
     * @throws \Exception
     */
    private function hydrateProduct(\Magento\Catalog\Model\Product $product, array $data)
    {
        $product->setTypeId($this->getTypeId($data));
        $product->setVisibility($this->getVisibility($data));
        $product->setStatus($this->getStatus($data));
        $product->setTaxClassId($this->getTaxClassId($data));
        $product->setStoreId(0);
        $product->setWebsiteIds([1]);
        $product->setAttributeSetId(!empty($product->getAttributeSetId()) ? $product->getAttributeSetId() : '4');
        $product->setName($this->getName($data));
        $product->setDescription($this->getDescription($data));
        $product->setShortDescription($this->getShortDescription($data));
        $product->setPrice($this->getPrice($data));
        $product->setUpdatedAt($this->getUpdatedAt($data));
        $product->setOrderable($this->getOrderable($data));
        $product->setWinlineChecksum($this->getWinlineChecksum($data));
        $product->setNewsFromDate($this->getNewsFromDate($data));
        $product->setNewsToDate($this->getNewsToDate($data));
        $product->setSort($this->getSort($data));
    }

    private function getSort($data)
    {
        $sortOrder = $data['Webartikel'];
        return !empty($sortOrder) ? (string)$sortOrder : 0;
    }

    /**
     * @return int
     */
    private function getDefaultStoreId(){
        return $this->storeManager->getStore()->getId();
    }
    /**
     * @param array $data
     * @return string
     */
    private function getTypeId(array $data)
    {
        return 'simple';
    }

    /**
     * @param array $data
     * @return string
     */
    private function getVisibility(array $data)
    {
        return (string)\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH;
    }

    /**
     * @param $data
     * @return string
     */
    private function getStatus(array $data)
    {
        if (
            $data['Webartikel'] === null ||
            $data['Inaktiv'] !== null ||
            $data['Artikeluntergruppe'] === null ||
            ($data['Steuersatzzeile'] === '1' && $data['Steuersatzzeile'] === '2') ||
            $data['price'] === null
        ) {
            $this->log($data['Artikelnummer'], sprintf(
                'Disable (Webartikel: %s, Inaktiv: %s, Artikeluntergruppe: %s, Steuersatzzeile: %s, price: %s)',
                is_null($data['Webartikel']) ? 'null' : (string)$data['Webartikel'],
                is_null($data['Inaktiv']) ? 'null' : (string)$data['Inaktiv'],
                is_null($data['Artikeluntergruppe']) ? 'null' : (string)$data['Artikeluntergruppe'],
                is_null($data['Steuersatzzeile']) ? 'null' : (string)$data['Steuersatzzeile'],
                is_null($data['price']) ? 'null' : (string)$data['price']
            ), 'notice');
            return (string) \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED;
        }
        return (string)\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
    }

    /**
     * @param array $data
     * @return null|string
     */
    private function getTaxClassId(array $data)
    {
        $winlineTaxId = $data['Steuersatzzeile'];
        if (!isset($this->taxClassMap[$winlineTaxId])) {
            return null;
        }
        return $this->taxClassMap[$winlineTaxId];
    }

    /**
     * @param array $data
     * @return array
     */
    private function getWebsiteIDs(array $data)
    {
        if (!$this->websiteIDs) {
            $this->websiteIDs = [$this->storeManager->getStore(\Magento\Store\Model\Store::DISTRO_STORE_ID)->getWebsiteId()];
        }
        return $this->websiteIDs;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getAttributeSetId(array $data)
    {
        if (!$this->attributeSetId) {
            $this->attributeSetId = $this->magentoProductObj->getDefaultAttributeSetId();
        }
        return $this->attributeSetId;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function getName(array $data)
    {
        return $data['Bezeichnung'];
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function getDescription(array $data)
    {
        $description = '';
        if ($data['Langtext1']) {
            $description .= '<p>' . $data['Langtext1'] . '</p>';
        }
        if ($data['Langtext2']) {
            $description .= '<p>' . $data['Langtext2'] . '</p>';
        }
        if ($data['Notiz1']) {
            $description = '<p>' . $this->rtfToText($data['Notiz1']) . '</p>';
        }
        return $description;
    }

    private function rtfToText($text)
    {
        if (!strlen($text)) {
            return "";
        }

        $document = "";
        $stack = [];
        $j = -1;
        for ($i = 0, $len = strlen($text); $i < $len; $i++) {
            $c = $text[$i];

            switch ($c) {
                case "\\":
                    $nc = $text[$i + 1];

                    if ($nc == '\\' && $this->rtfIsPlainText($stack[$j])) {
                        $document .= '\\';
                    } elseif ($nc == '~' && $this->rtfIsPlainText($stack[$j])) {
                        $document .= ' ';
                    } elseif ($nc == '_' && $this->rtfIsPlainText($stack[$j])) {
                        $document .= '-';
                    } elseif ($nc == '*') {
                        $stack[$j]["*"] = true;
                    } elseif ($nc == "'") {
                        $hex = substr($text, $i + 2, 2);
                        if (!empty($stack[$j]) && $this->rtfIsPlainText($stack[$j])) {
                            $document .= html_entity_decode("&#" . hexdec($hex) . ";");
                        }
                        $i += 2;
                    } elseif ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
                        $word = "";
                        $param = null;

                        for ($k = $i + 1, $m = 0; $k < strlen($text); $k++, $m++) {
                            $nc = $text[$k];
                            if ($nc >= 'a' && $nc <= 'z' || $nc >= 'A' && $nc <= 'Z') {
                                if (empty($param)) {
                                    $word .= $nc;
                                } else {
                                    break;
                                }
                            } elseif ($nc >= '0' && $nc <= '9') {
                                $param .= $nc;
                            } elseif ($nc == '-') {
                                if (empty($param)) {
                                    $param .= $nc;
                                } else {
                                    break;
                                }
                            } else {
                                break;
                            }
                        }
                        $i += $m - 1;

                        $toText = "";
                        switch (strtolower($word)) {
                            case "u":
                                $toText .= html_entity_decode("&#x" . dechex($param) . ";");
                                $ucDelta = @$stack[$j]["uc"];
                                if ($ucDelta > 0) {
                                    $i += $ucDelta;
                                }
                                break;
                            case "par":
                            case "page":
                            case "column":
                            case "line":
                            case "lbr":
                                $toText .= "\n";
                                break;
                            case "emspace":
                            case "enspace":
                            case "qmspace":
                                $toText .= " ";
                                break;
                            case "tab":
                                $toText .= "\t";
                                break;
                            case "chdate":
                                $toText .= date("m.d.Y");
                                break;
                            case "chdpl":
                                $toText .= date("l, j F Y");
                                break;
                            case "chdpa":
                                $toText .= date("D, j M Y");
                                break;
                            case "chtime":
                                $toText .= date("H:i:s");
                                break;
                            case "emdash":
                                $toText .= html_entity_decode("&mdash;");
                                break;
                            case "endash":
                                $toText .= html_entity_decode("&ndash;");
                                break;
                            case "bullet":
                                $toText .= html_entity_decode("&#149;");
                                break;
                            case "lquote":
                                $toText .= html_entity_decode("&lsquo;");
                                break;
                            case "rquote":
                                $toText .= html_entity_decode("&rsquo;");
                                break;
                            case "ldblquote":
                                $toText .= html_entity_decode("&laquo;");
                                break;
                            case "rdblquote":
                                $toText .= html_entity_decode("&raquo;");
                                break;
                            default:
                                $stack[$j][strtolower($word)] = empty($param) ? true : $param;
                                break;
                        }
                        if (!empty($stack[$j]) && $this->rtfIsPlainText($stack[$j])) {
                            $document .= $toText;
                        }
                    }

                    $i++;
                    break;
                case "{":
                    $j++;
                    if (isset($stack[$j])) {
                        array_push($stack, $stack[$j]);
                    } else {
                        $j--;
                    }
                    break;
                case "}":
                    array_pop($stack);
                    $j--;
                    break;
                case '\0':
                case '\r':
                case '\f':
                case '\n':
                    break;
                default:
                    if (isset($stack[$j]) && $this->rtfIsPlainText($stack[$j])) {
                        $document .= $c;
                    }
                    break;
            }
        }
        $document = trim($document);
        $lines = explode(PHP_EOL, $document);
        foreach ($lines as $index => $line) {
            $lines[$index] = trim($line);
        }
        $document = join(PHP_EOL, $lines);
        $document = nl2br($document);

        return $document;
    }

    private function rtfIsPlainText($s)
    {
        $arrfailAt = ["*", "fonttbl", "colortbl", "datastore", "themedata"];
        for ($i = 0; $i < count($arrfailAt); $i++) {
            if (!empty($s[$arrfailAt[$i]])) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param array $data
     *
     * @return string
     */
    private function getShortDescription(array $data)
    {
        if (!empty($data['Langtext2'])) {
            return '<p>' . $data['Langtext2'] . '</p>';
        }
        return '';
    }

    /**
     * @param array $data
     *
     * @return float
     */
    private function getPrice(array $data)
    {
        return $data['price'];
    }

    /**
     * @param array $data
     *
     * @return null|string
     */
    private function getUpdatedAt(array $data)
    {
        if (!empty($data['DatumdletztenAenderung'])) {
            $date = new \DateTime($data['DatumdletztenAenderung'] . '+02:00', new \DateTimeZone('Europe/London'));
            return $date->format(DateTime::DATETIME_PHP_FORMAT);
        }
        $this->log($data['Artikelnummer'], 'No update time found', 'notice');
        return null;
    }

    /**
     * @param array $data
     * @return bool|MagentoProduct
     * @throws \Exception
     */
    private function createProduct(array $data)
    {
        $this->log($data['Artikelnummer'], 'Create','info');

        $product = $this->magentoProducts->create();
        $product->setSku($data['Artikelnummer']);
        $this->hydrateProduct($product, $data);
        return $product;
    }

    /**
     * @param $product
     * @param array $data
     * @throws LocalizedException
     */
    private function syncCategories($product, array $data)
    {
        $categoryFactory = $this->categoryFactory->create();
        $categoryIds = $product->getCategoryIds();
        if ($data['Artikeluntergruppe'] || count($categoryIds) > 1) {
            $categories = $categoryFactory->getCollection()->addAttributeToFilter(
                'winline_id',
                $data['Artikeluntergruppe']
            );
            // Merge category collection of existing categories with ones that were fetched by their winline_id
            if (count($categoryIds)) {
                foreach ($categoryIds as $categoryId) {
                    $category = $categoryFactory->load($categoryId);
                    if (!$categories->getItemById($category->getId())) {
                        $categories->addItem($category);
                    }
                }
            }

            // Assign product to each category with its position
            $categoryIdsArr = array();
            foreach ($categories as $category) {
                $categoryIdsArr[] = $category->getId();
            }
            if (count($categoryIdsArr)) {
                $this->categoryApiInterface->assignProductToCategories(
                    $product->getSku(),
                    $categoryIdsArr
                );
            } else {
                throw new LocalizedException(new \Magento\Framework\Phrase(sprintf(
                    '%s coudln\'t be assigned to %s.',
                    $product->getSku(),
                    $data['Artikeluntergruppe']
                )));
            }
        }
    }


    /**
     * @param MagentoProduct $product
     * @throws LocalizedException
     */
    private function validate(\Magento\Catalog\Model\Product $product)
    {
        $errors = $product->validate();

        if (is_array($errors)) {
            foreach ($errors as $code => $error) {
                throw new LocalizedException(new Phrase(sprintf('Product validation failed. (%s: %s)', $code, $error)));
            }
        }
    }

    /**
     * @param $sku
     * @param $message
     * @param $type
     */
    private function log($sku = '', $message, $type = 'info')
    {
        if($type == 'info') {
            $this->logger->info(str_pad($sku, 20, ' ', STR_PAD_RIGHT) . ' ' . $message);
        }
        if($type == 'warning'){
            $this->logger->warning(str_pad($sku, 20, ' ', STR_PAD_RIGHT) . ' ' . $message);
        }
        if($type == 'ERR'){
            $this->logger->error(str_pad($sku, 20, ' ', STR_PAD_RIGHT) . ' ' . $message);
        }
        if($type == 'notice'){
            $this->logger->notice(str_pad($sku, 20, ' ', STR_PAD_RIGHT) . ' ' . $message);
        }
    }

    /**
     * @param array $data
     * @return array
     */
    private function sanitizeWinlineData(array $data)
    {

        foreach ($data as $key => $val) {
            if (is_string($val)) {
                $data[$key] = trim($val);
            }
        }

        return $data;
    }

    /**
     * @return bool
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function syncAllFileExists()
    {
        return file_exists($this->getSyncAllFilePath());
    }

    /**
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Zend_Db_Statement_Exception
     */
    private function createSyncAllFile()
    {
        $skus = $this->getWinlineConnection()
            ->query('SELECT TRIM(Artikelnummer) AS sku FROM ExportWebArtikel')
            ->fetchAll();
        $file = fopen($this->getSyncAllFilePath(), 'w');
        fwrite($file, join($this->getSyncAllSkuGlue(), $skus));
        fclose($file);
        die;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function getSyncAllFilePath()
    {
        return $this->directoryList->getPath('var') . DIRECTORY_SEPARATOR . 'sync-all';
    }

    /**
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    private function processSyncAllFile()
    {
        $syncAllFilePath = $this->getSyncAllFilePath();
        $skus = explode($this->getSyncAllSkuGlue(), file_get_contents($syncAllFilePath));
        for ($i = 0; $i < 50; $i++) {
            $sku = array_shift($skus);
            if (!$sku) {
                return;
            }
            $this->syncProduct($sku);
            file_put_contents($syncAllFilePath, join($this->getSyncAllSkuGlue(), $skus));
        }
    }

    /**
     * @return string
     */
    private function getSyncAllSkuGlue()
    {
        return PHP_EOL;
    }

    /**
     * @param array $data
     * @return bool
     */
    private function getOrderable(array $data)
    {
        return (string)(integer)$data['Bestellbar'];
    }

    /**
     * @param array $data
     * @return string
     */
    private function getWinlineChecksum(array $data)
    {
        return $data['checksum'];
    }

    /**
     * @param array $data
     * @return string
     */
    private function getNewsFromDate(array $data)
    {
        $date = (new \DateTime($data['DatumdErstanlage']))->setTime(0, 0, 0);
        return $date->format(DateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * @param array $data
     * @return string
     * @throws \Exception
     */
    private function getNewsToDate(array $data)
    {
        $end = new \DateTime($data['DatumdErstanlage']);
        $end->add(new \DateInterval('P2M'));
        $end->setTime(0, 0, 0);
        return $end->format(DateTime::DATETIME_PHP_FORMAT);
    }

    /**
     * @param MagentoProduct $product
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function updateStockItem(\Magento\Catalog\Model\Product $product)
    {
        $stockItem = $this->stockItem->getStockItemBySku($product->getSku());
        $stockItem->setUseConfigManageStock(1);
        $stockItem->setProduct($product);
        $stockItem->setStockId(1);
        $stockItem->setIsInStock(true);
        $stockItem->setQty(100);
        $this->stockItem->updateStockItemBySku($product->getSku(), $stockItem);
    }
}

