<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Inventory\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\SchemaPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
* Patch is mechanism, that allows to do atomic upgrade data changes
*/
class InstallDefaultSourceTypes implements
    DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface $moduleDataSetup
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(ModuleDataSetupInterface $moduleDataSetup)
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * Do Upgrade
     *
     * @return void
     */
    public function apply()
    {
        /**
         * Install source types
         */
        $data = [
            ['type_id' => \Magento\Inventory\Model\Source::SOURCE_TYPE_REGULAR, 'code' => 'regular', 'name' => 'Regular'],
            ['type_id' => \Magento\Inventory\Model\Source::SOURCE_TYPE_DROP_SHIPPER, 'code' => 'drop_shipper', 'name' => 'Drop-Shipper'],
            ['type_id' => \Magento\Inventory\Model\Source::SOURCE_TYPE_VIRTUAL, 'code' => 'virtual', 'name' => 'Virtual'],
        ];

        foreach ($data as $bind) {
            $this->moduleDataSetup->getConnection()->insertForce(
                $this->moduleDataSetup->getTable(
                    'inventory_source_type'
                ),
                $bind
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAliases()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public static function getDependencies()
    {
        return [

        ];
    }
}
