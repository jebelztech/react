<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Api\Data;

interface EditorAttributeInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = "id";
    const ADVANCED_ROLE_ID = "advanced_role_id";
    const ATTRIBUTE_ID = "attribute_id";
    const IS_ALLOW = "is_allow";

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
     * Get Attribute Id
     *
     * @return int|null
     */
    public function getAttributeId();

    /**
     *
     * @return string|null
     */
    public function getIsAllow();

    /**
     * @param $id
     * @return EditorAttributeInterface
     */
    public function setId($id);

    /**
     * @param $originalId
     * @return EditorAttributeInterface
     */
    public function setAdvancedId($originalId);

    /**
     * @param $attributeId
     * @return EditorAttributeInterface
     */
    public function setAttributeId($attributeId);

    /**
     * @param $isAllow
     * @return EditorAttributeInterface
     */
    public function setIsAllow($isAllow);
}
