<?php
namespace Rbo\Puch\Block;

class Homecategories extends \Magento\Framework\View\Element\Template {

    protected $_categoryHelper;
    protected $_categoryFactory;
    protected $_registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Helper\Category $categoryHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Framework\Registry $registry
    ) {
        $this->_registry = $registry;
        $this->_categoryHelper = $categoryHelper;
        $this->_categoryFactory = $categoryFactory;
        parent::__construct($context);
    }


    public function getImageUrl($category){
        $baseUrl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        if($category->getThumbNail()){
            $thumbNailUrl = $baseUrl. DIRECTORY_SEPARATOR . "catalog" . DIRECTORY_SEPARATOR . "category" . DIRECTORY_SEPARATOR .$category->getThumbNail();
            return $thumbNailUrl;
        }
       return $category->getImageUrl();
    }

    public function getRootCategoryId(){
       return $rootId = $this->_storeManager->getStore()->getRootCategoryId();
    }
    /**
     * Return categories helper
     */
    public function getCategoryHelper()
    {
        return $this->_categoryHelper;
    }
    public function getCurrentCategory(){
       return $this->_registry->registry('current_category');
    }
    public function getChildCategories()
    {
        $rootCategoryId = $this->getRootCategoryId();
        $rootCategory = $this->_categoryFactory->create()->load($rootCategoryId);
        if($rootCategory) {
            $subcategories = $rootCategory->getChildren();
            $categoriesData = array();
            if($subcategories){
                $arrayIds = explode(',', $subcategories);
                $categoriesData = array();
                foreach ($arrayIds as $id){
                    $category = $this->_categoryFactory->create()->load($id);
                    if($category->getShowOnMain()){
                        $categoriesData[$id] = $category;
                    }
                }
            }
            return $categoriesData;
        } else {
            return false;
        }
    }
    public function getCategoryChildren($id=null)
    {
        if($id) {
            $parentCategory = $this->_categoryFactory->create()->load($id);
            if($parentCategory) {
                $subcategories = $parentCategory->getChildren();
                $categoriesData = array();
                $categoriesDataPosition = array();
                if($subcategories){
                    $arrayIds = explode(',', $subcategories);
                    $categoriesData = array();
                    foreach ($arrayIds as $childId){
                        $category = $this->_categoryFactory->create()->load($childId);
                        $categoriesData[$childId] = $category;
                        $categoriesDataPosition[$childId] = $category->getPosition();
                    }
                }
                asort($categoriesDataPosition);
                $sortedCategories = array();
                foreach ($categoriesDataPosition as $id => $position){
                    $sortedCategories[$id] = $categoriesData[$id];
                }
                return $sortedCategories;
            } else {
                return false;
            }
        }
    }

    public function getThumbnailUrl(){

    }
}
?>