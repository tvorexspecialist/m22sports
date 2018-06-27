<?php
namespace Rbo\CustomScripts\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Catalog\Model\CategoryFactory;

class CategoryImageImport extends \Symfony\Component\Console\Command\Command
{
    protected function configure()
    {
        $this->setName('rbo:category_image_import')->setDescription('Prints hello world.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->getDatabaseConnection());
    }

    protected function getDatabaseConnection(){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $connection = $objectManager->create('Magento\Framework\App\ResourceConnection');
        $connectionConfigs = $connection->getConnection('default');//->getConfig();
        $categoryFactory = $objectManager->create('Magento\Catalog\Model\CategoryFactory');
        $categoryCollection = $categoryFactory->create()->getCollection()->addAttributeToSelect('entity_id');

        foreach ($categoryCollection as $category){
            $categoryId = $category->getEntityId();
            if($categoryId != '1' && $categoryId != '2') {
                $query = $connectionConfigs->prepare("select * from catalog_category_entity_varchar WHERE entity_id = $categoryId AND attribute_id = 124");
                $query->execute();
                foreach ($query->fetchALL() as $item){
                    try{
                       $currentCategory = $categoryFactory->create()->setStoreId($item['store_id'])->load($categoryId);
                       $currentCategory->setThumbNail($item['value']);
                       $currentCategory->save();
                    } catch(\PDOException $e){
                        echo "Error: " . $e->getMessage();
                    }
                }
            }
        }
    }

}