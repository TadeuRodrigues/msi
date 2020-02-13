<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Inventory\Model\ResourceModel\Source;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Exception\LocalizedException;

/**
 * Implementation of basic operations for type entity for specific db layer
 */
class Type extends AbstractDb
{
    /**
     * Define main table name and attributes table
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('inventory_source_type', 'type_code');
    }

    /**
     * Return all types of source to select field
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAllTypes(): array
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable()
        );

        return $connection->fetchAll($select);
    }
}
