<?php

namespace Rbo\Puch\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Cms\Model\Page;
use Magento\Cms\Model\PageFactory;

class UpgradeSchema implements UpgradeSchemaInterface
{
    private $pageFactory;

    private $blockFactory;

    public function __construct(
        PageFactory $pageFactory,
        \Magento\Cms\Model\BlockFactory $blockFactory
    )
    {
        $this->pageFactory = $pageFactory;
        $this->blockFactory = $blockFactory;
    }

    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.1.1') < 0) {
            $testBlock = [
                'title' => 'Footer ssl link',
                'identifier' => 'footer-ssl-link',
                'content' => '<a href="http://www.verisign.de/products-services/security-services/ssl/ssl-information-center/" 
                                title="Zur Bestätigung klicken&nbsp;– Diese Website hat das Symantec SSL-Zertifikat für sicheren E-Commerce und vertrauliche Kommunikation gewählt.">
                                Über SSL-Zertifikate
                              </a>',
                'stores' => [0],
                'is_active' => 1,
            ];
            $this->blockFactory->create()->setData($testBlock)->save();
        }
        if (version_compare($context->getVersion(), '1.1.2') < 0) {
            $testBlock = [
                'title' => 'Footer contacts',
                'identifier' => 'footer-contacts',
                'content' => '<p>
                                <a href=\'http://www.rbo.at/\'>RBO - Hermann Stöckl</a><br/>
                                Gewerbepark II/4<br/>
                                <abbr title=\'Austria\'>A</abbr>-2111 Tresdorf bei Korneuburg
                              </p>
                              <dl>
                                <dt>Phone:</dt>
                                <dd>+43 (0) 2262 / 725 13</dd>
                                <dt>Fax:</dt>
                                <dd>+43 (0) 2262 / 725 13-4</dd>
                                <dt>Email:</dt>
                                <dd><a href=\'mailto:office@rbo.at\'>office@rbo.at</a></dd>
                              </dl>
                              <p>
                                <a href=\'/impressum\'>Impressum</a>
                              </p>',
                'stores' => [0],
                'is_active' => 1,
            ];
            $this->blockFactory->create()->setData($testBlock)->save();
        }
        if (version_compare($context->getVersion(), '1.1.3') < 0) {
            $testBlock = [
                'title' => 'Footer copyright',
                'identifier' => 'footer_copyright',
                'content' => 'Design and programming by <a href=\'http://www.midnight-design.at/\'>Midnight Design</a>',
                'stores' => [0],
                'is_active' => 1,
            ];
            $this->blockFactory->create()->setData($testBlock)->save();
        }
        if (version_compare($context->getVersion(), '1.1.4') < 0) {
            $testBlock = [
                'title' => 'Home page content block',
                'identifier' => 'home-page-content-block',
                'content' => '<div class="block-main-content">
                                <p><img title="image" src="{{media url="midnight_images/2c67c15ccc130421ae1de63f76a05ab448dbecfd.jpg"}}" alt="image" width="930" height="500" /></p>
                                <div class="content-top-fader"><span>Puch-Shop</span></div>
                                <div class="content-bottom-text">
                                <h2>Puch-Shop</h2>
                                <p>Alle Puch-Artikel</p>
                                </div>
                                </div>',
                'stores' => [0],
                'is_active' => 1,
            ];
            $this->blockFactory->create()->setData($testBlock)->save();
        }


        $setup->endSetup();
    }
}
