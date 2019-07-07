<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Inventory\Model\ResourceModel\Source;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Catalog product link resource model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
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
        $this->_init('inventory_source_type', 'type_id');
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getAllTypes()
    {
        $connection = $this->getConnection();
        $select = $connection->select()->from(
            $this->getMainTable());

        return $connection->fetchAll($select);
    }
}