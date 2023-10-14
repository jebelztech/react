<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model;

use Aitoc\AdvancedPermissions\Api\Data\RoleInterface;
use Aitoc\AdvancedPermissions\Model\ResourceModel\Role as RoleResourceModel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

class Role extends \Magento\Framework\Model\AbstractModel implements RoleInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $scopeConfig,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = [])
    {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_role';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(RoleResourceModel::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * Get Original Role Id
     *
     * @return int|null
     */
    public function getOriginalId()
    {
        return $this->getData(self::ORIGINAL_ROLE_ID);
    }

    /**
     * Get Website Id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->getData(self::WEBSITE_ID);
    }

    /**
     * Get Parameter
     *
     * @return int|null
     */
    public function getCanEditGlobalAttr()
    {
        return $this->getData(self::CAN_EDIT_GLOBAL_ATTR);
    }

    /**
     * Get Parameter
     *
     * @return string|null
     */
    public function getCanEditOwnProductsOnly()
    {
        return (string)$this->getData(self::CAN_EDIT_OWN_PRODUCTS_ONLY);
    }

    /**
     * Get Parameter
     *
     * @return string|null
     */
    public function getCanCreateProducts()
    {
        return (string)$this->getData(self::CAN_CREATE_PRODUCTS);
    }

    /**
     * Get Parameter
     *
     * @return string|null
     */
    public function getManageOrdersOwnProductsOnly()
    {
        return (string)$this->getData(self::MANAGE_ORDERS_OWN_PRODUCTS_ONLY);
    }

    /**
     * Get Scope
     *
     * @return int|null
     */
    public function getScope()
    {
        return (string)$this->getData(self::SCOPE);
    }

    /**
     * Get value for view All stores
     *
     * @return mixed
     */
    public function getViewAll()
    {
        return $this->getParam(self::VIEW_ALL);
    }

    /**
     * Get value for config view All stores
     *
     * @return mixed
     */
    public function getUseConfigViewAll()
    {
        return $this->getUseConfig(self::USE_CONFIG_VIEW_ALL);
    }

    /**
     * Get value for show all products
     *
     * @return mixed
     */
    public function getShowAllProducts()
    {
        return $this->getData(self::SHOW_ALL_PRODUCTS);
    }

    /**
     * Get value for config show all products
     *
     * @return mixed
     */
    public function getUseConfigShowAllProducts()
    {
        return $this->getUseConfig(self::USE_CONFIG_SHOW_ALL_PRODUCTS);
    }

    /**
     * Get value for show all customers
     *
     * @return mixed
     */
    public function getShowAllCustomers()
    {
        return $this->getParam(self::SHOW_ALL_CUSTOMERS);
    }

    /**
     * Get value for config show all customers
     *
     * @return mixed
     */
    public function getUseConfigShowAllCustomers()
    {
        return $this->getUseConfig(self::USE_CONFIG_SHOW_ALL_CUSTOMERS);
    }

    /**
     * Get value for allow delete
     *
     * @return mixed
     */
    public function getAllowDelete()
    {
        return $this->getParam(self::ALLOW_DELETE);
    }

    /**
     * Get value for config allow delete
     *
     * @return mixed
     */
    public function getUseConfigAllowDelete()
    {
        return $this->getUseConfig(self::USE_CONFIG_ALLOW_DELETE);
    }

    /**
     * Get value for allow null category
     *
     * @return mixed
     */
    public function getAllowNullCategory()
    {
        return $this->getParam(self::ALLOW_NULL_CATEGORY);
    }

    /**
     * Get value for config allow null category
     *
     * @return mixed
     */
    public function getUseConfigAllowNullCategory()
    {
        return $this->getUseConfig(self::USE_CONFIG_ALLOW_NULL_CATEGORY);
    }

    /**
     * Get value for admin product on grid
     *
     * @return mixed
     */
    public function getShowAdminOnProductGrid()
    {
        return $this->getData(self::SHOW_ADMIN_ON_PRODUCT_GRID);
    }

    /**
     * Get value for config admin product on grid
     *
     * @return mixed
     */
    public function getUseConfigShowAdminOnProductGrid()
    {
        return $this->getUseConfig(self::USE_CONFIG_SHOW_ADMIN_ON_PRODUCT_GRID);
    }

    /**
     * Get value for edit product global attribute
     *
     * @return mixed
     */
    public function getManageGlobalAttribute()
    {
        return $this->getParam(self::MANAGE_GLOBAL_ATTRIBUTE);
    }

    /**
     * Get value for config edit product global attribute
     *
     * @return int
     */
    public function getUseConfigManageGlobalAttribute()
    {
        return $this->getUseConfig(self::USE_CONFIG_MANAGE_GLOBAL_ATTRIBUTE);
    }

    /**
     * Set ID
     *
     * @return RoleInterface
     */
    public function setId($id)
    {
        $this->setData(self::ROLE_ID, $id);
    }

    /**
     * Set Original Role Id
     *
     * @return RoleInterface
     */
    public function setOriginalId($originalId)
    {
        $this->setData(self::ORIGINAL_ROLE_ID, $originalId);
    }

    /**
     * Set Website Id
     *
     * @return RoleInterfacel
     */
    public function setWebsiteId($websiteId)
    {
        $this->setData(self::WEBSITE_ID, $websiteId);
    }

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */
    public function setCanEditGlobalAttr($attr)
    {
        $this->setData(self::CAN_EDIT_GLOBAL_ATTR, $attr);
    }

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */
    public function setCanEditOwnProductsOnly($attr)
    {
        $this->setData(self::CAN_EDIT_OWN_PRODUCTS_ONLY, $attr);
    }

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */
    public function setCanCreateProducts($attr)
    {
        $this->setData(self::CAN_CREATE_PRODUCTS, $attr);
    }

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */
    public function setManageOrdersOwnProductsOnly($attr)
    {
        $this->setData(self::MANAGE_ORDERS_OWN_PRODUCTS_ONLY, $attr);
    }

    /**
     * Set Scope
     *
     * @return int|null
     */
    public function setScope($scope)
    {
        $this->setData(self::SCOPE, $scope);
    }

    /**
     * Set value for view All stores
     *
     * @return mixed
     */
    public function setViewAll($viewAll)
    {
        $this->setData(self::VIEW_ALL, $viewAll);
    }

    public function loadOriginal($originalId)
    {
        return $this->load($originalId, self::ORIGINAL_ROLE_ID);
    }

    /**
     * Set value for config view All stores
     *
     * @return mixed
     */
    public function setUseConfigViewAll($value)
    {
        $this->setData(self::USE_CONFIG_VIEW_ALL, $value);
    }

    /**
     * Set value for show all products
     *
     * @return mixed
     */
    public function setShowAllProducts($value)
    {
        $this->setData(self::SHOW_ALL_PRODUCTS, $value);
    }

    /**
     * Set value for config show all products
     *
     * @return mixed
     */
    public function setUseConfigShowAllProducts($value)
    {
        $this->setData(self::USE_CONFIG_SHOW_ALL_PRODUCTS, $value);
    }

    /**
     * Set value for show all customers
     *
     * @return mixed
     */
    public function setShowAllCustomers($value)
    {
        $this->setData(self::SHOW_ALL_CUSTOMERS, $value);
    }

    /**
     * Set value for config show all customers
     *
     * @return mixed
     */
    public function setUseConfigShowAllCustomers($value)
    {
        $this->setData(self::USE_CONFIG_SHOW_ALL_CUSTOMERS, $value);
    }

    /**
     * Set value for allow delete
     *
     * @return mixed
     */
    public function setAllowDelete($value)
    {
        $this->setData(self::ALLOW_DELETE, $value);
    }

    /**
     * Set value for config allow delete
     *
     * @return mixed
     */
    public function setUseConfigAllowDelete($value)
    {
        $this->setData(self::USE_CONFIG_ALLOW_DELETE, $value);
    }

    /**
     * Set value for allow null category
     *
     * @return mixed
     */
    public function setAllowNullCategory($value)
    {
        $this->setData(self::ALLOW_NULL_CATEGORY, $value);
    }

    /**
     * Set value for config allow null category
     *
     * @return mixed
     */
    public function setUseConfigAllowNullCategory($value)
    {
        $this->setData(self::USE_CONFIG_ALLOW_NULL_CATEGORY, $value);
    }

    /**
     * Set value for admin product on grid
     *
     * @return mixed
     */
    public function setShowAdminOnProductGrid($value)
    {
        $this->setData(self::SHOW_ADMIN_ON_PRODUCT_GRID, $value);
    }

    /**
     * Set value for config admin product on grid
     *
     * @return mixed
     */
    public function setUseConfigShowAdminOnProductGrid($value)
    {
        $this->setData(self::USE_CONFIG_SHOW_ADMIN_ON_PRODUCT_GRID, $value);
    }

    /**
     * Set value for edit product global attribute
     *
     * @return mixed
     */
    public function setManageGlobalAttribute($value)
    {
        $this->setData(self::MANAGE_GLOBAL_ATTRIBUTE, $value);
    }

    /**
     * Set value for config edit product global attribute
     *
     * @return mixed
     */
    public function setUseConfigManageGlobalAttribute($value)
    {
        $this->setData(self::USE_CONFIG_MANAGE_GLOBAL_ATTRIBUTE, $value);
    }

    /**
     * @param string $field
     * @param string $group
     *
     * @return mixed
     */
    public function getConfig($field, $group = 'general')
    {
        return ObjectManager::getInstance()
            ->get(ScopeConfigInterface::class)
            ->getValue('advancedpermissions/' . $group . '/' . $field);
    }

    /**
     * Load object data
     *
     * @param integer $modelId
     * @param null|string $field
     *
     * @return $this
     */
    public function load($key, $field = null)
    {
        if (!is_numeric($key)) {
            $this->_getResource()->load($this, $key, $field);

            return $this;
        }

        return parent::load($key, $field);
    }


    /**
     * @param $roleId
     * @return bool
     */
    public function canCreateProducts($roleId)
    {
        $role = $this->load($roleId);

        if ($role->getId()) {
            return (bool)$role->getCanCreateProducts();
        }

        return true;
    }

    /**
     * Get array options
     *
     * @return array
     */
    public function getOptions()
    {
        return [
            self::SHOW_ALL_PRODUCTS,
            self::SHOW_ALL_CUSTOMERS,
            self::ALLOW_DELETE,
            self::ALLOW_NULL_CATEGORY,
            self::SHOW_ADMIN_ON_PRODUCT_GRID,
            self::VIEW_ALL,
            self::MANAGE_GLOBAL_ATTRIBUTE
        ];
    }

    /**
     * Get param field
     *
     * @param $field
     * @param int $reverse
     *
     * @return int|mixed
     */
    public function getParam($field, $reverse = 0)
    {
        $scope = $this->scopeConfig->getValue('advancedpermissions/general/' . $field);
        $useData = $this->getUseConfig('use_config_' . $field);
        $data = $this->getData($field);
        $value = $useData ? $scope : $data;

        return $value;
    }

    /**
     * Get field for global
     *
     * @param $field
     *
     * @return int|mixed
     */
    public function getUseConfig($field)
    {
        if ($this->hasData($field)) {
            return $this->getData($field);
        }

        return 1;
    }
}
