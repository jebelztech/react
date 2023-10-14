<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $table = $installer->getConnection()
            ->newTable($installer->getTable('aitoc_advanced_permissions_role'))
            ->addColumn(
                'role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Role id'
            )
            ->addColumn(
                'original_role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                ['unsigned' => true, 'nullable' => false, 'primary' => true],
                'Original Role Id'
            )
            ->addColumn(
                'website_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Website Id'
            )
            ->addColumn(
                'scope',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                2,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Scope'
            )
            ->addColumn(
                'can_edit_global_attr',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Can edit Global Attribute'
            )
            ->addColumn(
                'can_edit_own_products_only',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Can edit own product only'
            )
            ->addColumn(
                'can_create_products',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Can create products'
            )
            ->addColumn(
                'manage_orders_own_products_only',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Manage orders own products only'
            )
            ->addIndex(
                $installer->getIdxName('aitoc_advanced_permissions_role', ['original_role_id']),
                ['original_role_id']
            )
            ->addColumn(
                'view_all',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'View All'
            )
            ->addColumn(
                'use_config_view_all',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Use Config View All'
            )
            ->addColumn(
                'show_all_products',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Show All Products'
            )
            ->addColumn(
                'use_config_show_all_products',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Use Config Show All Products'
            )
            ->addColumn(
                'show_all_customers',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Show All Customers'
            )
            ->addColumn(
                'use_config_show_all_customers',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Use Config Show All Customers'
            )
            ->addColumn(
                'allow_delete',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Allow Delete'
            )
            ->addColumn(
                'use_config_allow_delete',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Use Config Allow Delete'
            )
            ->addColumn(
                'allow_null_category',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Allow Null Category'
            )
            ->addColumn(
                'use_config_allow_null_category',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Use Config Allow Null Category'
            )
            ->addColumn(
                'show_admin_on_product_grid',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Show Admin on product grid'
            )
            ->addColumn(
                'use_config_show_admin_on_product_grid',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Use Config Show Admin on product grid'
            )
            ->addColumn(
                'manage_global_attribute',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 0],
                'Allow to update global attributes'
            )
            ->addColumn(
                'use_config_manage_global_attribute',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                1,
                ['unsigned' => true, 'nullable' => false, 'default' => 1],
                'Use Config Allow to update global attributes'
            )
            ->setComment(
                'Advanced Permissions roles'
            );

        $installer->getConnection()->createTable($table);

        $table = $installer->getConnection()->newTable($installer->getTable('aitoc_advanced_permissions_stores'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'identity' => true,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Entity id'
            )
            ->addColumn(
                'advanced_role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_BIGINT,
                20,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'primary'  => true
                ],
                'Advanced Role Id'
            )
            ->addColumn(
                'store_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                5,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0
                ],
                'Store Id'
            )
            ->addColumn(
                'store_view_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0
                ],
                'Store View Ids'
            )
            ->addColumn(
                'category_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                [
                    'type'     => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                    'unsigned' => true,
                    'nullable' => false,
                    'default'  => 0
                ],
                'Category Ids'
            )
            ->addIndex(
                $installer->getIdxName(
                    'aitoc_advanced_permissions_stores',
                    ['advanced_role_id']
                ),
                ['advanced_role_id']
            )
            ->setComment('Advanced Permissions Stores');

        $installer->getConnection()->createTable($table);

        $installer->endSetup();
    }
}
