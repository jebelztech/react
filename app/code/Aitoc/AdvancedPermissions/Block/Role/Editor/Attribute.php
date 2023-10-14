<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Role\Editor;

use Magento\Framework\App\ResourceConnection;

class Attribute extends \Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
{
    const ADVANCED_ROLES_TAB_NAME = 'aitoc.advancedpermissions.role.tab.advanced';

    protected $_roleId = 0;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\ResourceModel\EditorAttribute\CollectionFactory
     */
    protected $attributeCollectionFactory;

    /**
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resourceConnection;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\EditorAttributeFactory
     */
    protected $editorAttributeFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $dataJson;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\RoleFactory
     */
    protected $roleFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory
     */
    protected $productAttributeCollectionFactory;

    /**
     * @var \Magento\Framework\App\ScopeResolverInterface
     */
    protected $scopeResolver;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Aitoc\AdvancedPermissions\Model\ResourceModel\EditorAttribute\CollectionFactory $collectionFactory,
        ResourceConnection $resourceConnection,
        \Aitoc\AdvancedPermissions\Model\EditorAttributeFactory $editorAttributeFactory,
        \Magento\Framework\Json\Helper\Data $dataJson,
        \Aitoc\AdvancedPermissions\Model\RoleFactory $roleFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\CollectionFactory $productAttributeCollectionFactory,
        \Magento\Framework\App\ScopeResolverInterface $scopeResolver,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $data);
        $this->attributeCollectionFactory = $collectionFactory;
        $this->scopeResolver = $scopeResolver;
        $this->productAttributeCollectionFactory = $productAttributeCollectionFactory;
        $this->resourceConnection = $resourceConnection;
        $this->editorAttributeFactory = $editorAttributeFactory;
        $this->dataJson = $dataJson;
        $this->roleFactory = $roleFactory;
        $this->registry = $registry;
        $this->setId('editorAttributeGrid');
        $this->setUseAjax(true);
    }

    private function getCurrentRole()
    {
        return $this->registry->registry('current_role');
    }

    /**
     * @return string
     * @throws Exception
     */
    public function getGridUrl()
    {
        $roleId = $this->getRequest()->getParam('rid');

        return $this->getUrl('aitocadvancedpermissions/editor/attributegrid', ['rid' => $roleId]);
    }

    /**
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareCollection()
    {
        $collection = $this->productAttributeCollectionFactory->create();

        $subquery = $this->resourceConnection->getConnection(ResourceConnection::DEFAULT_CONNECTION)->select();
        $subquery
            ->from(
                ["attr" => $this->resourceConnection->getTableName("eav_entity_attribute")],
                "attr.attribute_id"
            )
            ->join(
                ["groups" =>  $this->resourceConnection->getTableName("eav_attribute_group")],
                'attr.`attribute_group_id` = `groups`.`attribute_group_id`',
                ['attribute_group_name' => new \Zend_Db_Expr('GROUP_CONCAT( DISTINCT attribute_group_name )')]
            )
            ->group('attr.attribute_id');

        $collection->getSelect()
            ->columns(
                [
                    'is_ait_allow' => new \Zend_Db_Expr(
                        'IF( is_global IN ( ' . implode(',', $this->_getEnableScope()) . ' ) , 1, 0 )'
                    )
                ]
            )
            ->joinLeft(
                ["group_name" => $subquery],
                'group_name.attribute_id = main_table.attribute_id',
                'attribute_group_name'
            );

        $collection->addVisibleFilter();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @param \Magento\Backend\Block\Widget\Grid\Column $column
     * @return $this|\Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addColumnFilterToCollection($column)
    {
        switch ($column->getId()) {
            case 'is_allow':
                $allowIds = $this->_getAllowAttribute();
                $this->_addInFilter($column, $allowIds, 'main_table.attribute_id');
                break;

            case 'is_disable':
                $disableIds = $this->_getDisableAttribute();
                $this->_addInFilter($column, $disableIds, 'main_table.attribute_id');
                break;

            case 'is_hidden':
                $hiddenIds = $this->_getHiddenAttribute();
                $this->_addInFilter($column, $hiddenIds, 'main_table.attribute_id');
                break;

            case 'is_ait_allow':
                $scopeIds = $this->_getEnableScope();
                $this->_addInFilter($column, $scopeIds, 'is_global');
                break;

            default:
                parent::_addColumnFilterToCollection($column);

        }

        return $this;
    }

    /**
     * @param $column
     * @param $arrayIds
     * @param $field
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _addInFilter($column, $arrayIds, $field)
    {
        if (empty($arrayIds)) {
            $arrayIds = 0;
        }
        if ($column->getFilter()->getValue()) {
            $this->getCollection()->addFieldToFilter($field, ['in' => $arrayIds]);
        } else {
            if ($arrayIds) {
                $this->getCollection()->addFieldToFilter($field, ['nin' => $arrayIds]);
            }
        }
    }

    /**
     * @return $this|\Magento\Eav\Block\Adminhtml\Attribute\Grid\AbstractGrid
     * @throws \Exception
     */
    protected function _prepareColumns()
    {
        $disablesInput = false;
        if ($this->getRequest()->getParam('scope') == 'disabled') {
            $disablesInput = true;
        }

        $this->addColumn(
            'is_allow',
            [
                'type' => 'radio',
                'header' => __('Allow'),
                'name' => 'is_allow',
                'values' => $this->_getAllowAttribute(),
                'align' => 'center',
                'index' => 'attribute_id',
                'field_name' => 'is_allow',
                'html_name' => 'attribute_permission',
                'radio_value' => '1',
                'disabled' => $disablesInput,
                'renderer' => \Aitoc\AdvancedPermissions\Block\Role\Editor\Render\Radio::class
            ]
        );

        $this->addColumn(
            'is_disable',
            [
                'type' => 'radio',
                'header' => __('Deny'),
                'name' => 'is_disable',
                'values' => $this->_getDisableAttribute(),
                'align' => 'center',
                'index' => 'attribute_id',
                'field_name' => 'is_disable',
                'html_name' => 'attribute_permission',
                'radio_value' => '0',
                'disabled' => $disablesInput,
                'renderer' => \Aitoc\AdvancedPermissions\Block\Role\Editor\Render\Radio::class
            ]
        );

        $this->addColumn(
            'is_hidden',
            [
                'type' => 'radio',
                'header' => __('Hide'),
                'name' => 'is_hidden',
                'values' => $this->_getHiddenAttribute(),
                'align' => 'center',
                'index' => 'attribute_id',
                'field_name' => 'is_hidden',
                'html_name' => 'attribute_permission',
                'radio_value' => '2',
                'disabled' => $disablesInput,
                'renderer' => \Aitoc\AdvancedPermissions\Block\Role\Editor\Render\Radio::class
            ]
        );

        $this->addColumn(
            'is_ait_allow',
            [
                'header' => __('Inherited from Scope'),
                'sortable' => true,
                'index' => 'is_ait_allow',
                'name' => 'is_ait_allow',
                'field_name' => 'is_ait_allow',
                'type' => 'options',
                'width' => '100px',
                'options' => $this->_getDisableScopeOptions(),
                'align' => 'center',
                'radio_value' => '',
                'disabled' => $disablesInput,
                'html_name' => 'attribute_permission',
                'values' => array_merge(
                    $this->_getAllowAttribute(),
                    $this->_getDisableAttribute(),
                    $this->_getHiddenAttribute()
                ),
                'renderer' => \Aitoc\AdvancedPermissions\Block\Role\Editor\Render\Radio::class
            ]
        );

        $this->addColumn(
            'is_global',
            [
                'header' => __('Scope'),
                'sortable' => true,
                'index' => 'is_global',
                'type' => 'options',
                'options' => $this->getScopeArray(),
                'align' => 'center',
            ]
        );

        parent::_prepareColumns();

        $this->addColumn(
            'attribute_group_name',
            [
                'header' => __('Tabs'),
                'sortable' => true,
                'index' => 'attribute_group_name'
            ]
        );


        return $this;
    }

    /**
     * @return array
     */
    public function getScopeArray()
    {
        return [
            \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_STORE => __('Store View'),
            \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_WEBSITE => __('Website'),
            \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL => __('Global'),
        ];
    }

    /**
     * @param bool $json
     * @return array|mixed|string
     */
    public function _getAllowAttribute($json = false)
    {
        if ($this->_isGetAttributeFromPost()) {
            return $this->_getValueArrayFromPost('is_allow_ids');
        }
        $attrEnable = $this->editorAttributeFactory->create()->getRoleAttributeEnable($this->_getRoleId());

        return $this->_getArrayForOption($attrEnable, $json);
    }

    /**
     * @param bool $json
     * @return array|mixed|string
     */
    public function _getDisableAttribute($json = false)
    {
        if ($this->_isGetAttributeFromPost()) {
            return $this->_getValueArrayFromPost('is_disable_ids');
        }
        $attrDisable = $this->editorAttributeFactory->create()->getRoleAttributeDisable($this->_getRoleId());

        return $this->_getArrayForOption($attrDisable, $json);
    }

    /**
     * @param bool $json
     * @return array|mixed|string
     */
    public function _getHiddenAttribute($json = false)
    {
        if ($this->_isGetAttributeFromPost()) {
            return $this->_getValueArrayFromPost('is_hidden_ids');
        }
        $attrHidden = $this->editorAttributeFactory->create()->getRoleAttributeHidden($this->_getRoleId());

        return $this->_getArrayForOption($attrHidden, $json);
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function _isGetAttributeFromPost()
    {
        if (
            $this->getRequest()->getParam('is_disable_ids') != ""
            || $this->getRequest()->getParam('is_hidden_ids') != ""
            || $this->getRequest()->getParam('is_allow_ids') != ""
            || $this->getRequest()->getParam('default_ids') != ""
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @return array|mixed
     * @throws Exception
     */
    protected function _getValueArrayFromPost($name)
    {
        if ($this->getRequest()->getParam($name) == "") {
            return array();
        }

        return $this->getRequest()->getParam($name);
    }

    /**
     * @param $array
     * @param $json
     * @return array|string
     */
    protected function _getArrayForOption($array, $json)
    {
        if (sizeof($array) > 0) {
            if ($json) {
                $jsonAttr = Array();
                foreach ($array as $attrId) {
                    $jsonAttr[$attrId] = 0;
                }

                return $this->dataJson->jsonEncode((object)$jsonAttr);
            } else {
                return array_values($array);
            }
        } else {
            if ($json) {
                return '{}';
            } else {
                return [];
            }
        }
    }

    /**
     * @return array
     */
    protected function _getDisableScopeOptions()
    {
        return [
            1 => __('Allow'),
            0 => __('Deny'),
        ];
    }

    /**
     * @return bool
     */
    public function getCanManageGlobalAttribute()
    {
        return (int)$this->roleFactory->create()->load($this->_getRoleId())->getManageGlobalAttribute();
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getEnableScope()
    {
        if (!($scope = $this->getRequest()->getParam('scope'))) {
            $scope = $this->scopeResolver->getScope();
            $canEditGlobal =
                $this->roleFactory->create()->load($this->_getRoleId())->getManageGlobalAttribute();
        } else {
            $canEditGlobal = $this->getRequest()->getParam('can_edit_global');
        }

        if (is_numeric($scope)) {
            switch ($scope) {
                case '1':
                    $arrayScope = [0];
                    break;
                case '2':
                    $arrayScope = [0, 2];
                    break;
                default:
                    $arrayScope = [0, 1, 2];
            }
        } else {
            switch ($scope->getCode()) {
                case 'store':
                    $arrayScope = [0];
                    break;
                case 'website':
                    $arrayScope = [0, 2];
                    break;
                default:
                    $arrayScope = [0, 1, 2];
            }
        }

        if (!empty($canEditGlobal)) {
            $arrayScope[] = 1;
        }

        return $arrayScope;
    }

    /**
     * @param $row
     * @return bool
     */
    public function getRowUrl($row)
    {
        return false;
    }

    /**
     * @return int|mixed
     */
    protected function _getRoleId()
    {
        if (empty($this->_roleId)) {
            $role = $this->getCurrentRole();
            $currentRole = $this->roleFactory->create()->loadOriginal($role->getId());
            $this->_roleId = ($currentRole->getId() > 0)
                ? $currentRole->getId()
                : $this->registry->registry(
                    'RID'
                );
        }

        return $this->_roleId;
    }
}

