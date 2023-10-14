<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Api\Data;

interface RoleInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ROLE_ID = "role_id";
    const ORIGINAL_ROLE_ID = "original_role_id";
    const WEBSITE_ID = "website_id";
    const CAN_EDIT_GLOBAL_ATTR = "can_edit_global_attr";
    const CAN_EDIT_OWN_PRODUCTS_ONLY = "can_edit_own_products_only";
    const CAN_CREATE_PRODUCTS = "can_create_products";
    const MANAGE_ORDERS_OWN_PRODUCTS_ONLY = "manage_orders_own_products_only";
    const SCOPE = "scope";
    const VIEW_ALL = "view_all";
    const USE_CONFIG_VIEW_ALL = "use_config_view_all";
    const SHOW_ALL_PRODUCTS = "show_all_products";
    const USE_CONFIG_SHOW_ALL_PRODUCTS = "use_config_show_all_products";
    const SHOW_ALL_CUSTOMERS = "show_all_customers";
    const USE_CONFIG_SHOW_ALL_CUSTOMERS = "use_config_show_all_customers";
    const ALLOW_DELETE = "allow_delete";
    const USE_CONFIG_ALLOW_DELETE = "use_config_allow_delete";
    const ALLOW_NULL_CATEGORY = "allow_null_category";
    const USE_CONFIG_ALLOW_NULL_CATEGORY = "use_config_allow_null_category";
    const SHOW_ADMIN_ON_PRODUCT_GRID = "show_admin_on_product_grid";
    const USE_CONFIG_SHOW_ADMIN_ON_PRODUCT_GRID = "use_config_show_admin_on_product_grid";
    const MANAGE_GLOBAL_ATTRIBUTE = "manage_global_attribute";
    const USE_CONFIG_MANAGE_GLOBAL_ATTRIBUTE = "use_config_manage_global_attribute";

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Original Role Id
     *
     * @return int|null
     */
    public function getOriginalId();

    /**
     * Get Website Id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Get parameter
     *
     * @return int|null
     */
    public function getCanEditGlobalAttr();

    /**
     * Get Paramter
     *
     * @return string|null
     */
    public function getCanEditOwnProductsOnly();

    /**
     * Get Parameter
     *
     * @return string|null
     */
    public function getCanCreateProducts();

    /**
     * Get Parameter
     *
     * @return string|null
     */
    public function getManageOrdersOwnProductsOnly();

    /**
     * Get Scope
     *
     * @return int|null
     */
    public function getScope();

    /**
     * Get value for view All stores
     *
     * @return mixed
     */
    public function getViewAll();

    /**
     * Get value for config view All stores
     *
     * @return mixed
     */
    public function getUseConfigViewAll();

    /**
     * Get value for show all products
     *
     * @return mixed
     */
    public function getShowAllProducts();

    /**
     * Get value for config show all products
     *
     * @return mixed
     */
    public function getUseConfigShowAllProducts();

    /**
     * Get value for show all customers
     *
     * @return mixed
     */
    public function getShowAllCustomers();

    /**
     * Get value for config show all customers
     *
     * @return mixed
     */
    public function getUseConfigShowAllCustomers();

    /**
     * Get value for allow delete
     *
     * @return mixed
     */
    public function getAllowDelete();

    /**
     * Get value for config allow delete
     *
     * @return mixed
     */
    public function getUseConfigAllowDelete();

    /**
     * Get value for allow null category
     *
     * @return mixed
     */
    public function getAllowNullCategory();

    /**
     * Get value for config allow null category
     *
     * @return mixed
     */
    public function getUseConfigAllowNullCategory();

    /**
     * Get value for admin product on grid
     *
     * @return mixed
     */
    public function getShowAdminOnProductGrid();

    /**
     * Get value for config admin product on grid
     *
     * @return mixed
     */
    public function getUseConfigShowAdminOnProductGrid();

    /**
     * Get value for  edit product global attribute
     *
     * @return mixed
     */
    public function getManageGlobalAttribute();

    /**
     * Get value for config  edit product global attribute
     *
     * @return mixed
     */
    public function getUseConfigManageGlobalAttribute();

    /**
     * Set ID
     *
     * @return RoleInterface
     */
    public function setId($id);

    /**
     * Set Original Role Id
     *
     * @return RoleInterface
     */
    public function setOriginalId($originalId);

    /**
     * Set Website Id
     *
     * @return RoleInterfacel
     */
    public function setWebsiteId($websiteId);

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */
    public function setCanEditGlobalAttr($attr);

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */
    public function setCanEditOwnProductsOnly($attr);

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */

    public function setCanCreateProducts($attr);

    /**
     * Set Parameter
     *
     * @return RoleInterface
     */
    public function setManageOrdersOwnProductsOnly($attr);

    /**
     * Set Scope
     *
     * @return RoleInterface
     */
    public function setScope($scope);

    /**
     * Set value for view All stores
     *
     * @return mixed
     */
    public function setViewAll($viewAll);

    /**
     * Set value for config view All stores
     *
     * @return mixed
     */
    public function setUseConfigViewAll($value);

    /**
     * Set value for show all products
     *
     * @return mixed
     */
    public function setShowAllProducts($value);

    /**
     * Set value for config show all products
     *
     * @return mixed
     */
    public function setUseConfigShowAllProducts($value);

    /**
     * Set value for show all customers
     *
     * @return mixed
     */
    public function setShowAllCustomers($value);

    /**
     * Set value for config show all customers
     *
     * @return mixed
     */
    public function setUseConfigShowAllCustomers($value);

    /**
     * Set value for allow delete
     *
     * @return mixed
     */
    public function setAllowDelete($value);

    /**
     * Set value for config allow delete
     *
     * @return mixed
     */
    public function setUseConfigAllowDelete($value);

    /**
     * Set value for allow null category
     *
     * @return mixed
     */
    public function setAllowNullCategory($value);

    /**
     * Set value for config allow null category
     *
     * @return mixed
     */
    public function setUseConfigAllowNullCategory($value);

    /**
     * Set value for admin product on grid
     *
     * @return mixed
     */
    public function setShowAdminOnProductGrid($value);

    /**
     * Set value for config admin product on grid
     *
     * @return mixed
     */
    public function setUseConfigShowAdminOnProductGrid($value);

    /**
     * Set value for edit product global attribute
     *
     * @return mixed
     */
    public function setManageGlobalAttribute($value);

    /**
     * Set value for config  edit product global attribute
     *
     * @return mixed
     */
    public function setUseConfigManageGlobalAttribute($value);
}
