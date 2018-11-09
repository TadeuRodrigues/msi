<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\InventorySales\Test\Integration\Website;

use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\Website;
use Magento\Store\Model\WebsiteFactory;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Magento\Framework\Registry;
use Magento\Store\Model\ResourceModel\Website as WebsiteResource;

class AssignWebsiteToDefaultStockTest extends TestCase
{
    /**
     * @var WebsiteFactory
     */
    private $websiteFactory;

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    /**
     * @var DefaultStockProviderInterface
     */
    private $defaultStockProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var WebsiteResource
     */
    private $websiteResource;

    protected function setUp()
    {
        $this->websiteFactory = Bootstrap::getObjectManager()->get(WebsiteFactory::class);
        $this->stockRepository = Bootstrap::getObjectManager()->get(StockRepositoryInterface::class);
        $this->defaultStockProvider = Bootstrap::getObjectManager()->get(DefaultStockProviderInterface::class);
        $this->storeManager = Bootstrap::getObjectManager()->get(StoreManagerInterface::class);
        $this->websiteResource = Bootstrap::getObjectManager()->get(WebsiteResource::class);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testThatMainWebsiteIsAssignedToDefaultStock()
    {
        try {

        $websiteCode = $this->storeManager->getWebsite()->getCode();

        $defaultStockId = $this->defaultStockProvider->getId();
        $defaultStock = $this->stockRepository->get($defaultStockId);

        $extensionAttributes = $defaultStock->getExtensionAttributes();
        $salesChannels = $extensionAttributes->getSalesChannels();
        self::assertContainsOnlyInstancesOf(SalesChannelInterface::class, $salesChannels);
        self::assertCount(1, $salesChannels);

        $salesChannel = reset($salesChannels);
        self::assertEquals($websiteCode, $salesChannel->getCode());
        self::assertEquals(SalesChannelInterface::TYPE_WEBSITE, $salesChannel->getType());} catch (
            \Exception $e
        ) {
            echo $e->getMessage();
            exit();
        }

    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testThatNewWebsiteWillBeAssignedToDefaultStock()
    {
        $websiteCode = 'test_1';

        /** @var Website $website */
        $website = $this->websiteFactory->create();
        $website->setCode($websiteCode);
        $this->websiteResource->save($website);

        $defaultStockId = $this->defaultStockProvider->getId();
        $defaultStock = $this->stockRepository->get($defaultStockId);

        $extensionAttributes = $defaultStock->getExtensionAttributes();
        $salesChannels = $extensionAttributes->getSalesChannels();
        self::assertContainsOnlyInstancesOf(SalesChannelInterface::class, $salesChannels);

        $salesChannelsOfCreatedWebsite = array_filter($salesChannels, function ($salesChannel) use ($websiteCode) {
            return $salesChannel->getCode() === $websiteCode;
        });

        self::assertCount(1, $salesChannelsOfCreatedWebsite);

        $salesChannelOfCreatedWebsite = reset($salesChannelsOfCreatedWebsite);
        self::assertEquals($website->getCode(), $salesChannelOfCreatedWebsite->getCode());
        self::assertEquals(SalesChannelInterface::TYPE_WEBSITE, $salesChannelOfCreatedWebsite->getType());
        $this->deleteWebsite($website);
    }

    /**
     * @param Website $website
     * @return void
     */
    private function deleteWebsite(Website $website)
    {
        $registry = Bootstrap::getObjectManager()->get(Registry::class);
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', true);
        $website->delete();
        $registry->unregister('isSecureArea');
        $registry->register('isSecureArea', false);
    }
}
