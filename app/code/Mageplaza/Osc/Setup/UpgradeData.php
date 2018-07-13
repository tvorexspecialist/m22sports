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
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\Osc\Setup;

use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Cms\Model\BlockFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var QuoteSetupFactory
     */
    protected $quoteSetupFactory;

    /**
     * @var SalesSetupFactory
     */
    protected $salesSetupFactory;

    /**
     * @var BlockFactory
     */
    protected $blockFactory;

    /**
     * @param QuoteSetupFactory $quoteSetupFactory
     * @param SalesSetupFactory $salesSetupFactory
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
        BlockFactory $blockFactory
    )
    {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
        $this->blockFactory      = $blockFactory;

    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        /** @var \Magento\Quote\Setup\QuoteSetup $quoteInstaller */
        $quoteInstaller = $this->quoteSetupFactory->create(['resourceName' => 'quote_setup', 'setup' => $setup]);

        /** @var \Magento\Sales\Setup\SalesSetup $salesInstaller */
        $salesInstaller = $this->salesSetupFactory->create(['resourceName' => 'sales_setup', 'setup' => $setup]);

        $setup->startSetup();
        if (version_compare($context->getVersion(), '2.1.0') < 0) {
            $entityAttributesCodes = [
                'osc_gift_wrap_amount' => Table::TYPE_DECIMAL,
                'base_osc_gift_wrap_amount' => Table::TYPE_DECIMAL
            ];
            foreach ($entityAttributesCodes as $code => $type) {
                $quoteInstaller->addAttribute('quote_address', $code, ['type' => $type, 'visible' => false]);
                $quoteInstaller->addAttribute('quote_item', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('order', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('order_item', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('invoice', $code, ['type' => $type, 'visible' => false]);
                $salesInstaller->addAttribute('creditmemo', $code, ['type' => $type, 'visible' => false]);
            }

            $quoteInstaller->addAttribute('quote_address', 'used_gift_wrap', ['type' => Table::TYPE_BOOLEAN, 'visible' => false]);
            $quoteInstaller->addAttribute('quote_address', 'gift_wrap_type', ['type' => Table::TYPE_SMALLINT, 'visible' => false]);
            $salesInstaller->addAttribute('order', 'gift_wrap_type', ['type' => Table::TYPE_SMALLINT, 'visible' => false]);

        }

        if (version_compare($context->getVersion(), '2.1.1') < 0) {
            $salesInstaller->addAttribute('order', 'osc_delivery_time', ['type' => Table::TYPE_TEXT, 'visible' => false]);
        }
        if (version_compare($context->getVersion(), '2.1.2') < 0) {
            $salesInstaller->addAttribute('order', 'osc_survey_question', ['type' => Table::TYPE_TEXT, 'visible' => false]);
            $salesInstaller->addAttribute('order', 'osc_survey_answers', ['type' => Table::TYPE_TEXT, 'visible' => false]);
        }
        if (version_compare($context->getVersion(), '2.1.3') < 0) {
            $salesInstaller->addAttribute('order', 'osc_order_house_security_code', ['type' => Table::TYPE_TEXT, 'visible' => false]);
        }
        if (version_compare($context->getVersion(), '2.1.4') < 0) {
            $this->insertBlock($setup);
        }

        $setup->endSetup();
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @return $this
     */
    public function insertBlock($setup)
    {
        $blocks = $this->getDataBlock();
        $blockFactory = $this->blockFactory->create();
        foreach ($blocks as $block) {
            $setup->getConnection()->delete($setup->getTable('cms_block'), ['identifier = ?' => $block['identifier']]);
            $blockFactory->load($block['identifier'], 'identifier')->setData($block)->save();
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getDataBlock()
    {
        $sealContent  = '
            <div class="osc-trust-seals" style="text-align: center;">
                <div class="trust-seals-badges">
                    <a href="https://en.wikipedia.org/wiki/Trust_seal" target="_blank">
                        <img src="{{view url=Mageplaza_Osc/css/images/seal.png}}">
                    </a>
                </div>
                <div class="trust-seals-text">
                    <p>This is a demonstration of trust badge. Please contact your SSL or Security provider to have trust badges embed properly</p>
                </div>
            </div>';

        return [
            [
                'title'      => __('One Step Checkout Seal Content'),
                'identifier' => 'osc-seal-content',
                'content'    => $sealContent,
                'stores'     => [0],
                'is_active'  => 1
            ]
        ];
    }
}
