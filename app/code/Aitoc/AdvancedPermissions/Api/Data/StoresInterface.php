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

interface StoresInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ENTITY_ID = "entity_id";
    const ADVANCED_ROLE_ID = "advanced_role_id";
    const STORE_ID = "store_id";
    const STORE_VIEW_IDS = "store_view_ids";
    const CATEGORY_IDS = "category_ids";

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId();

    /**
     * Get Advanced Role Id
     *
     * @return int|null
     */
    public function getAdvancedId();

    /**
     * Get Website Id
     *
     * @return int|null
     */
    public function getWebsiteId();

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId();

    /**
     * Get Store View Ids
     *
     * @return string|null
     */
    public function getStoreViewIds();

    /**
     * Get Category Ids
     *
     * @return string|null
     */
    public function getCategoryIds();

    /**
     * Set ID
     *
     * @return RoleInterface
     */
    public function setId($id);

    /**
     * Set Advanced Role Id
     *
     * @return RoleInterface
     */
    public function setAdvancedId($originalId);

    /**
     * Set Website Id
     *
     * @return RoleInterfacel
     */
    public function setWebsiteId($websiteId);

    /**
     * Set Store Id
     *
     * @return RoleInterface
     */
    public function setStoreId($storeId);

    /**
     * Set Store View Ids
     *
     * @return RoleInterface
     */
    public function setStoreViewIds($storeViewsId);

    /**
     * Set Store View Ids
     *
     * @return RoleInterface
     */
    public function setCategoryIds($categoryIds);
}
