<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\WMS\Setup;

/**
 * InstallSchema Data Class
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class InstallSchema implements \Magento\Framework\Setup\InstallSchemaInterface
{
    /**
     * WMS table installer
     *
     * @param Magento\Framework\Setup\SchemaSetupInterface   $setup   setupinterface
     * @param Magento\Framework\Setup\ModuleContextInterface $context modulecontext
     *
     * @return NULL
     */
    public function install(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        $textType = \Magento\Framework\DB\Ddl\Table::TYPE_TEXT;
        $dateType = \Magento\Framework\DB\Ddl\Table::TYPE_DATE;
        $timeType = \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP;
        $timeInit = \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT;
        $integerType = \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER;
        $timeInitUpdate = \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT_UPDATE;

        // WMS Warehouse Table //////////////////////////////////////////////////////
        $table = $installer->getConnection()
            ->newTable($installer->getTable("wms_warehouse"))
            ->addColumn(
                "id",
                $integerType,
                null,
                [
                    "identity"=>true,
                    "unsigned"=>true,
                    "nullable"=>false,
                    "primary"=>true
                ],
                "Id"
            )
            ->addColumn(
                "title",
                $textType,
                null,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Warehouse Title"
            )
            ->addColumn(
                "status",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Status"
            )
            ->addColumn(
                "row_count",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "No of Rows in warehouse"
            )
            ->addColumn(
                "column_count",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "No of Columns in warehouse"
            )
            ->addColumn(
                "shelves_per_cluster",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "No of Shelf per cluster in warehouse"
            )
            ->addColumn(
                "racks_per_shelf",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "No of Racks per shelf in warehouse"
            )
            ->addColumn(
                "source",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Source"
            )
            ->addColumn(
                "tote_count",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Tote count for this warehouse"
            )
            ->addColumn(
                "created_at",
                $timeType,
                null,
                [
                    "nullable"=>false,
                    "default"=>$timeInit
                ],
                "Creation Time"
            )
            ->addColumn(
                "updated_at",
                $timeType,
                null,
                [
                    "nullable"=>false,
                    "default"=>$timeInitUpdate
                ],
                "Updation Time"
            )
            ->setComment("WMS Warehouse Table");
        $installer->getConnection()->createTable($table);

        // WMS Staff Table //////////////////////////////////////////////////////////
        $table = $installer->getConnection()
            ->newTable($installer->getTable("wms_warehouse_staff"))
            ->addColumn(
                "id",
                $integerType,
                null,
                [
                    "identity"=>true,
                    "unsigned"=>true,
                    "nullable"=>false,
                    "primary"=>true
                ],
                "Id"
            )
            ->addColumn(
                "filename",
                $textType,
                null,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "File Name"
            )
            ->addColumn(
                "name",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Name"
            )
            ->addColumn(
                "password",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Password"
            )
            ->addColumn(
                "age",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Staff Age"
            )
            ->addColumn(
                "gender",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Gender"
            )
            ->addColumn(
                "os",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Device OS"
            )
            ->addColumn(
                "email",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Email"
            )
            ->addColumn(
                "contact_no",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Contact Number"
            )
            ->addColumn(
                "device_token",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Device Token"
            )
            ->addColumn(
                "staff_token",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Token"
            )
            ->addColumn(
                "dob",
                $dateType,
                null,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Date of Birth"
            )
            ->addColumn(
                "address",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Staff Address"
            )
            ->addColumn(
                "warehouse_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Warehouse Id"
            )
            ->addColumn(
                "status",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Status"
            )
            ->addColumn(
                "created_at",
                $timeType,
                null,
                [
                    "nullable"=>false,
                    "default"=>$timeInit
                ],
                "Creation Time"
            )
            ->addColumn(
                "updated_at",
                $timeType,
                null,
                [
                    "nullable"=>false,
                    "default"=>$timeInitUpdate
                ],
                "Updation Time"
            )
            ->setComment("WMS Warehouse Staff Table");
        $installer->getConnection()->createTable($table);

        // WMS Order Assigned to Staff Table ////////////////////////////////////////
        $table = $installer->getConnection()
            ->newTable($installer->getTable("wms_order"))
            ->addColumn(
                "id",
                $integerType,
                null,
                [
                    "identity"=>true,
                    "unsigned"=>true,
                    "nullable"=>false,
                    "primary"=>true
                ],
                "Id"
            )
            ->addColumn(
                "increment_id",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Order Incremnet Id"
            )
            ->addColumn(
                "order_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Order Id"
            )
            ->addColumn(
                "warehouse_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Warehouse Id"
            )
            ->addColumn(
                "product_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Order Product Id"
            )
            ->addColumn(
                "staff_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Staff Id"
            )
            ->setComment("WMS Order Assigned to Staff Table");
        $installer->getConnection()->createTable($table);

        // WMS Totes Assigned to Order Table ////////////////////////////////////////
        $table = $installer->getConnection()
            ->newTable($installer->getTable("wms_order_totes"))
            ->addColumn(
                "id",
                $integerType,
                null,
                [
                    "identity"=>true,
                    "unsigned"=>true,
                    "nullable"=>false,
                    "primary"=>true
                ],
                "Id"
            )
            ->addColumn(
                "order_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Order Id"
            )
            ->addColumn(
                "assigned_tote_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Assigned Tote Id"
            )
            ->addColumn(
                "assigned_tote_title",
                $textType,
                null,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Assigned Tote Title"
            )
            ->setComment("WMS Totes Assigned to Order Table");
        $installer->getConnection()->createTable($table);

        // WMS Order Status Table ///////////////////////////////////////////////////
        $table = $installer->getConnection()
            ->newTable($installer->getTable("wms_order_status"))
            ->addColumn(
                "id",
                $integerType,
                null,
                [
                    "identity"=>true,
                    "unsigned"=>true,
                    "nullable"=>false,
                    "primary"=>true
                ],
                "Id"
            )
            ->addColumn(
                "order_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Order Id"
            )
            ->addColumn(
                "status",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>""
                ],
                "Status"
            )
            ->setComment("WMS Order Status Table");
        $installer->getConnection()->createTable($table);

        // WMS Tote Table ///////////////////////////////////////////////////////////
        $table = $installer->getConnection()
            ->newTable($installer->getTable("wms_tote"))
            ->addColumn(
                "id",
                $integerType,
                null,
                [
                    "identity"=>true,
                    "unsigned"=>true,
                    "nullable"=>false,
                    "primary"=>true
                ],
                "Id"
            )
            ->addColumn(
                "title",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Tote Title"
            )
            ->addColumn(
                "warehouse_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Warehouse Id"
            )
            ->addColumn(
                "hash",
                $textType,
                255,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Tote Hash"
            )
            ->setComment("WMS Tote Table");
        $installer->getConnection()->createTable($table);

        // WMS Warehouse Product Location Table /////////////////////////////////////
        $table = $installer->getConnection()
            ->newTable($installer->getTable("wms_product_location"))
            ->addColumn(
                "id",
                $integerType,
                null,
                [
                    "identity"=>true,
                    "unsigned"=>true,
                    "nullable"=>false,
                    "primary"=>true
                ],
                "Id"
            )
            ->addColumn(
                "row",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Warehouse Row"
            )
            ->addColumn(
                "column",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Warehouse Column"
            )
            ->addColumn(
                "shelf",
                $textType,
                50,
                [
                    "nullable"=>true,
                    "default"=>null
                ],
                "Warehouse Shelf"
            )
            ->addColumn(
                "rack",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Warehouse Shelf Rack"
            )
            ->addColumn(
                "warehouse_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Warehouse Id"
            )
            ->addColumn(
                "product_id",
                $integerType,
                null,
                [
                    "unsigned"=>true,
                    "nullable"=>false,
                    "default"=>"0"
                ],
                "Product Id"
            )
            ->setComment("WMS Warehouse Product Location Table");
        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
