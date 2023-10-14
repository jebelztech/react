<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Api\Data;

interface EditorTypeInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = "id";
    const ADVANCED_ROLE_ID = "advanced_role_id";
    const TYPE = "type";

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
     *
     * @return string|null
     */
    public function getType();

    /**
     * @param $id
     * @return EditorTypeInterface
     */
    public function setId($id);

    /**
     * @param $originalId
     * @return EditorTypeInterface
     */
    public function setAdvancedId($originalId);

    /**
     * @param $type
     * @return EditorTypeInterface
     */
    public function setType($type);
}
