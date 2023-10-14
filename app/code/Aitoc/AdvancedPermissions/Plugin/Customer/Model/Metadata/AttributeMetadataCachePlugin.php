<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Plugin\Customer\Model\Metadata;

use Aitoc\AdvancedPermissions\Helper\Data as AdvancedPermissionHelper;
use Magento\Customer\Api\Data\AttributeMetadataInterface;
use Magento\Customer\Model\Metadata\AttributeMetadataCache;

/**
 * Class AttributeMetadataCache
 */
class AttributeMetadataCachePlugin
{
    const ENTITY_TYPE_CUSTOMER = 'customer';
    const SUFFIX_ALL = 'all';

    /**
     * @var AdvancedPermissionHelper
     */
    private $helper;

    /**
     * AttributeMetadataCachePlugin constructor.
     * @param AdvancedPermissionHelper $helper
     */
    public function __construct(AdvancedPermissionHelper $helper)
    {
        $this->helper = $helper;
    }

    /**
     * Load attributes metadata from cache
     *
     * @param AttributeMetadataCache $subject
     * @param string $entityType
     * @param string $suffix
     * @return AttributeMetadataInterface[]|bool
     */
    public function beforeLoad(AttributeMetadataCache $subject, $entityType, $suffix = '')
    {
        if (!$this->isSuffixShouldBeOverloaded($entityType, $suffix)) {
            return null;
        }

        $suffix = $this->addUserRoleToSuffix($suffix);

        return [$entityType, $suffix];
    }

    /**
     * Save attributes metadata to cache
     *
     * @param AttributeMetadataCache $subject
     * @param string $entityType
     * @param AttributeMetadataInterface[] $attributes
     * @param string $suffix
     * @return void
     */
    public function beforeSave(AttributeMetadataCache $subject, $entityType, $attributes, $suffix = '')
    {
        if (!$this->isSuffixShouldBeOverloaded($entityType, $suffix)) {
            return null;
        }

        $suffix = $this->addUserRoleToSuffix($suffix);

        return [$entityType, $attributes, $suffix];
    }

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
        return ($entityType === self::ENTITY_TYPE_CUSTOMER) || ($suffix == self::SUFFIX_ALL);
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
