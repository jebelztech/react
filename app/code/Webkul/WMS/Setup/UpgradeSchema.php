<?php

namespace Webkul\WMS\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Upgrade schema for qty in location class
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Setup upgrade to add columns
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        $connection = $setup->getConnection();
        $tableName = $setup->getTable("wms_product_location");
        if ($setup->tableExists($tableName) == true) {
            $columns = [
                "location_qty" => [
                    "type" => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    "unsigned" => true,
                    "nullable" => false,
                    "default" => "0",
                    "comment" => "qty of product in Location"
                ]
            ];
            $this->createColumn($connection, $tableName, $columns);
        }
        $tableName = $setup->getTable("wms_order_totes");
        if ($setup->tableExists($tableName) == true) {
            $columns = [
                "location_id" => [
                    "type" => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    "unsigned" => true,
                    "nullable" => false,
                    "default" => "0",
                    "comment" => "Location id"
                ],
                "qty_picked" => [
                    "type" => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    "unsigned" => true,
                    "nullable" => false,
                    "default" => "0",
                    "comment" => "qty picked from Location"
                ]
            ];
            $this->createColumn($connection, $tableName, $columns);
        }
        $tableName = $setup->getTable("wms_warehouse_staff");
        if ($setup->tableExists($tableName)) {
            $columns = [
                "is_logged_in" => [
                    "type" => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
                    "unsigned" => true,
                    "nullable" => false,
                    "default" => false,
                    "comment" => "Is Logged In"
                ]
            ];
            $this->createColumn($connection, $tableName, $columns);
        }
        $setup->endSetup();
    }

    /**
     * Create Column
     *
     * @param string $connection
     * @param string $tableName
     * @param array $columns
     * @return void
     */
    private function createColumn($connection, $tableName, $columns)
    {
        foreach ($columns as $columnName => $columnDefinition) {
            $connection->addColumn($tableName, $columnName, $columnDefinition);

        }
    }
}
