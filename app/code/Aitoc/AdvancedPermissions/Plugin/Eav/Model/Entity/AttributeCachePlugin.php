<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Plugin\Eav\Model\Entity;

use Aitoc\AdvancedPermissions\Helper\Data as AdvancedPermissionHelper;
use Magento\Eav\Model\Entity\AttributeCache;

/**
 * Class AttributeCache
 */
class AttributeCachePlugin
{
    const ENTITY_TYPE_CUSTOMER = 'customer';
    const SUFFIX_ALL = 'all';

    /**
     * @var AdvancedPermissionHelper
     */
    private $helper;

    /**
     * AttributeCachePlugin constructor.
     * @param AdvancedPermissionHelper $helper
     */
    public function __construct(AdvancedPermissionHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * @param AttributeCache $attributeCache
     * @param string $entityType
     * @param string $suffix
     * @return array|null
     */
    public function beforeGetAttributes(AttributeCache $attributeCache, $entityType, $suffix = '')
    {
        if (!$this->isSuffixShouldBeOverloaded($entityType, $suffix)) {
            return null;
        }

        $suffix = $this->addUserRoleToSuffix($suffix);

        return [$entityType, $suffix];
    }

    /**
     * @param AttributeCache $attributeCache
     * @param string $entityType
     * @param object[] $attributes
     * @param string $suffix
     * @return array|null
     */
    public function beforeSaveAttributes(AttributeCache $attributeCache, $entityType, $attributes, $suffix = '')
    {
        if (!$this->isSuffixShouldBeOverloaded($entityType, $suffix)) {
            return null;
        }

        $suffix = $this->addUserRoleToSuffix($suffix);

        return [$entityType, $attributes, $suffix];
    }

    /**
     * @param $entityType
     * @param $suffix
     * @return bool
     */
    private function isSuffixShouldBeOverloaded($entityType, $suffix)
    {
        return $this->isAdvancedPermissionEnabled() && $this->isAllCustomersRequested($entityType, $suffix);
    }

    /**
     * @return bool
     */
    private function isAdvancedPermissionEnabled()
    {
        return $this->helper->isAdvancedPermissionEnabled();
    }

    /**
     * @param string $entityType
     * @param string $suffix
     * @return bool
     */
    private function isAllCustomersRequested($entityType, $suffix)
    {
        return ($entityType === self::ENTITY_TYPE_CUSTOMER) && ($suffix == self::SUFFIX_ALL);
    }

    /**
     * @param $suffix
     * @return string
     */
    private function addUserRoleToSuffix($suffix)
    {
        $userRoleName = $this->getUserRoleName();

        return $suffix . '_' . $userRoleName;
    }

    /**
     * @return string
     */
    private function getUserRoleName()
    {
        return $this->helper->getUser()->getRole()->getRoleName();
    }
}
