<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Setup;

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;

/**
 * @codeCoverageIgnore
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    const AITOC_ADVANCED_PERMISSIONS_EDITOR_ATTRIBUTE = 'aitoc_aitpermissions_editor_attribute';
    const AITOC_ADVANCED_PERMISSIONS_EDITOR_TAB = 'aitoc_aitpermissions_editor_tab';
    const AITOC_ADVANCED_PERMISSIONS_EDITOR_TYPE = 'aitoc_aitpermissions_editor_type';

    /**
     * {@inheritdoc}
     */
    public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '1.3.1', '<')) {
            $this->addEditorTables($setup);
        }
        $setup->endSetup();
    }

    /**
     * @param SchemaSetupInterface $setup
     * @throws \Zend_Db_Exception
     */
    private function addEditorTables(SchemaSetupInterface $setup)
    {
        $table = $setup->getConnection()->newTable(
            $setup->getTable(self::AITOC_ADVANCED_PERMISSIONS_EDITOR_ATTRIBUTE)
        )->addColumn(
                'id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'ID'
            )->addColumn(
                'advanced_role_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned' => true,
                    'nullable' => false
                ],
                'Advanced Role Id'
            )->addColumn(
                'attribute_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'nullable' => false
                ],
                'Attribute ID'
            )->addColumn(
                'is_allow',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Is Allow'
            )->addIndex(
                $setup->getIdxName(
                    'aitoc_advanced_permissions_editor_attribute',
                    ['advanced_role_id']
                ),
                ['advanced_role_id']
            )
            ->setComment('Advanced Editor Attributes');

        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable(self::AITOC_ADVANCED_PERMISSIONS_EDITOR_TAB)
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'advanced_role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false
            ],
            'Advanced Role Id'
        )->addColumn(
            'tab_code',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Tab Code'
        )->addIndex(
            $setup->getIdxName(
                'aitoc_advanced_permissions_editor_tab',
                ['advanced_role_id']
            ),
            ['advanced_role_id']
        )
            ->setComment('Advanced Editor Tab');

        $setup->getConnection()->createTable($table);

        $table = $setup->getConnection()->newTable(
            $setup->getTable(self::AITOC_ADVANCED_PERMISSIONS_EDITOR_TYPE)
        )->addColumn(
            'id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
            'ID'
        )->addColumn(
            'advanced_role_id',
            \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            null,
            [
                'unsigned' => true,
                'nullable' => false
            ],
            'Advanced Role Id'
        )->addColumn(
            'type',
            \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            255,
            ['nullable' => true, 'default' => null],
            'Type'
        )->addIndex(
            $setup->getIdxName(
                'aitoc_advanced_permissions_editor_type',
                ['advanced_role_id']
            ),
            ['advanced_role_id']
        )
            ->setComment('Advanced Editor Type');

        $setup->getConnection()->createTable($table);
    }
}
