<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Inventory\Plugin;

use Magento\InventoryApi\Api\SourceRepositoryInterface;
use Magento\InventoryApi\Api\Data\SourceInterface;
use Magento\Inventory\Model\SourceTypeLinkManagement;

class SourceTypeAttribute
{
    /**
     * @var SourceTypeLinkManagement
     */
    protected $sourceTypeLinkManagement;

    /**
     * SourceTypeAttribute constructor.
     * @param SourceTypeLinkManagement $sourceTypeLinkManagement
     */
    public function __construct(
        SourceTypeLinkManagement $sourceTypeLinkManagement
    ) {
        $this->sourceTypeLinkManagement = $sourceTypeLinkManagement;
    }

    /**
     * @param SourceRepositoryInterface $subject
     * @param SourceInterface $source
     * @return SourceInterface
     */
    public function afterGet(
        SourceRepositoryInterface $subject,
        SourceInterface $source
    ) {
        $this->sourceTypeLinkManagement->loadTypeLinksBySource($source);

        return $source;
    }

    /**
     * @param SourceRepositoryInterface $subject
     * @param callable $proceed
     * @param SourceInterface $source
     */
    public function aroundSave(
        SourceRepositoryInterface $subject,
        callable $proceed,
        SourceInterface $source
    ) {
        $proceed($source);

        $type_code = $source->getExtensionAttributes()->getTypeCode();

        $this->sourceTypeLinkManagement->saveTypeLinksBySource($source, $type_code);
    }
}