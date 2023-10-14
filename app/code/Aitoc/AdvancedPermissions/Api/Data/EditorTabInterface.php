<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Api\Data;

interface EditorTabInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID = "id";
    const ADVANCED_ROLE_ID = "advanced_role_id";
    const TAB_CODE = "tab_code";

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
    public function getTabCode();

    /**
     * @param $id
     * @return EditorTabInterface
     */
    public function setId($id);

    /**
     * @param $originalId
     * @return EditorTabInterface
     */
    public function setAdvancedId($originalId);

    /**
     * @param $tabCode
     * @return EditorTabInterface
     */
    public function setTabCode($tabCode);
}
