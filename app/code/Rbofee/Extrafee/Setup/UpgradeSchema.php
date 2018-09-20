<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

/**
 * Class UpgradeSchema
 *
 * @author Rbo Developer
 */

namespace Rbofee\Extrafee\Setup;

use Rbofee\Extrafee\Model\Config\Source\Excludeinclude;
use Magento\Framework\DB\Ddl\Table as DdlTable;
use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->addCalculationColumns($setup);
        }

        if (version_compare($context->getVersion(), '1.1.3', '<')) {
            $this->changeIdColumnType($setup);
        }

        if (version_compare($context->getVersion(), '1.1.4', '<')) {
            $this->addTaxColumns($setup);
        }

        $setup->endSetup();
    }

    protected function addCalculationColumns(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('rbofee_extrafee');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'discount_in_subtotal',
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Discount In Subtotal'
            ]
        );

        $connection->addColumn(
            $table,
            'tax_in_subtotal',
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Tax In Subtotal'
            ]
        );

        $connection->addColumn(
            $table,
            'shipping_in_subtotal',
            [
                'type' => DdlTable::TYPE_SMALLINT,
                'nullable' => false,
                'default' => Excludeinclude::VAR_DEFAULT,
                'comment' => 'Shipping In Subtotal'
            ]
        );
    }

    protected function changeIdColumnType(SchemaSetupInterface $setup)
    {
        $setup->getConnection()
            ->changeColumn(
                $setup->getTable('rbofee_extrafee_quote'),
                'entity_id',
                'entity_id',
                [
                    'type' => Table::TYPE_INTEGER,
                    'length' => 11,
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary' => true,
                    'comment' => 'Entity ID'
                ]
            );
    }

    protected function addTaxColumns(SchemaSetupInterface $setup)
    {
        $table = $setup->getTable('rbofee_extrafee_quote');
        $connection = $setup->getConnection();

        $connection->addColumn(
            $table,
            'tax_amount',
            [
                'type' => DdlTable::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Tax'
            ]
        );

        $connection->addColumn(
            $table,
            'base_tax_amount',
            [
                'type' => DdlTable::TYPE_DECIMAL,
                'length' => '12,4',
                'nullable' => false,
                'default' => '0.0000',
                'comment' => 'Tax'
            ]
        );
    }
}
