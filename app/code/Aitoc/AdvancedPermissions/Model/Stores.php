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

use Aitoc\AdvancedPermissions\Api\Data\StoresInterface;

class Stores extends \Magento\Framework\Model\AbstractModel implements StoresInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_stores';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Aitoc\AdvancedPermissions\Model\ResourceModel\Stores');
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * Get ADvanced Role Id
     *
     * @return int|null
     */
    public function getAdvancedId()
    {
        return $this->getData(self::ADVANCED_ROLE_ID);
    }

    /**
     * Get Website Id
     *
     * @return int|null
     */
    public function getWebsiteId()
    {
        return $this->getData(self::ROLE_ID);
    }

    /**
     * Get Store Id
     *
     * @return int|null
     */
    public function getStoreId()
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Get Store View Ids
     *
     * @return string|null
     */
    public function getStoreViewIds()
    {
        return (string)$this->getData(self::STORE_VIEW_IDS);
    }

    /**
     * Get Category Ids
     *
     * @return string|null
     */
    public function getCategoryIds()
    {
        return (string)$this->getData(self::CATEGORY_IDS);
    }

    /**
     * Set ID
     *
     * @return RoleInterface
     */
    public function setId($id)
    {
        $this->setData(self::ENTITY_ID, $id);
    }

    /**
     * Set Original Role Id
     *
     * @return RoleInterface
     */
    public function setAdvancedId($originalId)
    {
        $this->setData(self::ADVANCED_ROLE_ID, $originalId);
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
     * Set Store Id
     *
     * @return RoleInterface
     */
    public function setStoreId($storeId)
    {
        $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Set Store View Ids
     *
     * @return RoleInterface
     */
    public function setStoreViewIds($storeViewsId)
    {
        $this->setData(self::STORE_VIEW_IDS, $storeViewsId);
    }

    /**
     * Set Store View Ids
     *
     * @return RoleInterface
     */
    public function setCategoryIds($categoryIds)
    {
        $this->setData(self::CATEGORY_IDS, $categoryIds);
    }

    /**
     * Get model Autorizhation_role
     *
     * @param $originalId
     *
     * @return $this
     */
    public function loadOriginal($originalId)
    {
        return $this->load($originalId, self::ADVANCED_ROLE_ID);
    }
}
