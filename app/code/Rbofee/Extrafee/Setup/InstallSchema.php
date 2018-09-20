<?php
/**
 * @author Rbofee Team
 * @copyright Copyright (c) 2018 Rbofee 
 * @package Rbofee_Extrafee
 */

namespace Rbofee\Extrafee\Setup;

/**
 * Class InstallSchema
 *
 * @author Rbo Developer
 */

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()->newTable(
            $installer->getTable('rbofee_extrafee')
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'enabled',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => false],
            'Enabled'
        )->addColumn(
            'name',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Name'
        )->addColumn(
            'sort_order',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => true, 'default' => '0'],
            'Sort Order ID'
        )->addColumn(
            'frontend_type',
            Table::TYPE_TEXT,
            255,
            ['nullable' => false],
            'Frontend Type'
        )->addColumn(
            'description',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Description'
        )->addColumn(
            'options_serialized',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Options Serialized'
        )->addColumn(
            'conditions_serialized',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Conditions Serialized'
        )->setComment(
            'Rbofee Extrafee'
        );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('rbofee_extrafee_option')
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'fee_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Fee Id'
        )->addColumn(
            'price',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Price'
        )->addColumn(
            'order',
            Table::TYPE_SMALLINT,
            null,
            ['nullable' => false, 'default' => '0'],
            'Order'
        )->addColumn(
            'price_type',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Price Type'
        )->addColumn(
            'default',
            Table::TYPE_BOOLEAN,
            null,
            ['nullable' => false, 'default' => false],
            'Default'
        )->addColumn(
            'admin',
            Table::TYPE_TEXT,
            null,
            ['nullable' => false, 'default' => ''],
            'Admin Label'
        )->addColumn(
            'options_serialized',
            Table::TYPE_TEXT,
            '64k',
            ['nullable' => true],
            'Options Serialized'
        )->addForeignKey(
            $installer->getFkName('rbofee_extrafee_option', 'fee_id', 'rbofee_extrafee', 'entity_id'),
            'fee_id',
            $installer->getTable('rbofee_extrafee'),
            'entity_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Rbofee Extrafee Option'
        );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('rbofee_extrafee_store')
        )->addColumn(
            'fee_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Fee ID'
        )->addColumn(
            'store_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Store ID'
        )->addIndex(
            $installer->getIdxName('rbofee_extrafee_store', ['store_id']),
            ['store_id']
        )->addForeignKey(
            $installer->getFkName('rbofee_extrafee_store', 'fee_id', 'rbofee_extrafee', 'entity_id'),
            'fee_id',
            $installer->getTable('rbofee_extrafee'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName('rbofee_extrafee_store', 'store_id', 'store', 'store_id'),
            'store_id',
            $installer->getTable('store'),
            'store_id',
            Table::ACTION_CASCADE
        )->setComment(
            'Rbofee Extrafee To Store Linkage Table'
        );
        $installer->getConnection()->createTable($table);

        $installer->endSetup();

        $describe = $installer->getConnection()->describeTable($installer->getTable('customer_group'));
        $table = $installer->getConnection()->newTable(
            $installer->getTable('rbofee_extrafee_customer_group')
        )->addColumn(
            'fee_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Fee Id'
        )->addColumn(
            'customer_group_id',
            $describe['customer_group_id']['DATA_TYPE'] == 'int' ? Table::TYPE_INTEGER : Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Customer Group Id'
        )->addIndex(
            $installer->getIdxName('rbofee_extrafee_customer_group', ['customer_group_id']),
            ['customer_group_id']
        )->addForeignKey(
            $installer->getFkName('rbofee_extrafee_customer_group', 'fee_id', 'rbofee_extrafee', 'entity_id'),
            'fee_id',
            $installer->getTable('rbofee_extrafee'),
            'entity_id',
            Table::ACTION_CASCADE
        )->addForeignKey(
            $installer->getFkName(
                'rbofee_extrafee_customer_group',
                'customer_group_id',
                $installer->getTable('customer_group'),
                'customer_group_id'
            ),
            'customer_group_id',
            $installer->getTable('customer_group'),
            'customer_group_id',
            Table::ACTION_CASCADE
        )->setComment('Rbofee Extrafee To Customer Groups Relations');

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable(
            $installer->getTable('rbofee_extrafee_quote')
        )->addColumn(
            'entity_id',
            Table::TYPE_SMALLINT,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity ID'
        )->addColumn(
            'quote_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Quote ID'
        )->addColumn(
            'fee_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Fee Id'
        )->addColumn(
            'option_id',
            Table::TYPE_SMALLINT,
            null,
            ['unsigned' => true, 'nullable' => false],
            'Option Id'
        )->addColumn(
            'fee_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Fee Amount'
        )->addColumn(
            'base_fee_amount',
            Table::TYPE_DECIMAL,
            '12,4',
            ['nullable' => false, 'default' => '0.0000'],
            'Base Fee Amount'
        )->addColumn(
            'label',
            Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => ''],
            'Label'
        )->addIndex(
            $installer->getIdxName(
                'rbofee_extrafee_quote',
                ['quote_id', 'fee_id', 'option_id'],
                AdapterInterface::INDEX_TYPE_UNIQUE
            ),
            ['quote_id', 'fee_id', 'option_id'],
            ['type' => AdapterInterface::INDEX_TYPE_UNIQUE]
        )->setComment(
            'Rbofee Extrafee Quote'
        );

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
