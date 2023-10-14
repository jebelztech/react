<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model;

use Aitoc\AdvancedPermissions\Api\Data\EditorAttributeInterface;

class EditorAttribute extends \Magento\Framework\Model\AbstractModel implements EditorAttributeInterface
{
    /**
     * @var null
     */
    protected $_arrayPermissions = null;

    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_editor_attribute';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Aitoc\AdvancedPermissions\Model\ResourceModel\EditorAttribute::class);
    }

    /**
     * Get ID
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
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
     * Get Attribute Id
     *
     * @return int|null
     */
    public function getAttributeId()
    {
        return $this->getData(self::ATTRIBUTE_ID);
    }

    /**
     * Get Is Allow
     *
     * @return int|null
     */
    public function getIsAllow()
    {
        return $this->getData(self::IS_ALLOW);
    }

    /**
     * Set ID
     *
     * @return EditorAttributeInterface
     */
    public function setId($id)
    {
        $this->setData(self::ID, $id);

        return  $this;
    }

    /**
     * Set Original Role Id
     *
     * @return EditorAttributeInterface
     */
    public function setAdvancedId($originalId)
    {
        $this->setData(self::ADVANCED_ROLE_ID, $originalId);

        return  $this;
    }

    /**
     * Set Attribute Id
     *
     * @return EditorAttributeInterface
     */
    public function setAttributeId($attributeId)
    {
        $this->setData(self::ATTRIBUTE_ID, $attributeId);

        return $this;
    }

    /**
     * Set Is Allow
     *
     * @return EditorAttributeInterface
     */
    public function setIsAllow($isAllow)
    {
        $this->setData(self::IS_ALLOW, $isAllow);

        return $this;
    }

    /**
     * @param null $role
     * @return array
     */
    public function getRoleAttributeEnable($role = null)
    {
        $collection = $this->getCollection();

        return $this->_getAttributeIds($collection->getAttributeByRole($role, 1));
    }

    /**
     * @param null $role
     * @return array
     */
    public function getRoleAttributeDisable($role = null)
    {
        $collection = $this->getCollection();

        return $this->_getAttributeIds($collection->getAttributeByRole($role, 0));
    }

    /**
     * @param null $role
     * @return array
     */
    public function getRoleAttributeHidden($role = null)
    {
        $collection = $this->getCollection();

        return $this->_getAttributeIds($collection->getAttributeByRole($role, 2));
    }

    /**
     * @param $roleId
     */
    public function deleteRole($roleId)
    {
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize()) {

            foreach ($recordCollection as $record) {
                $record->delete();
            }
        }
    }

    /**
     * @param $collection
     * @return array
     */
    protected function _getAttributeIds($collection)
    {
        $array = array();
        foreach ($collection as $editorArrt) {
            $array[] = $editorArrt->getAttributeId();
        }

        return $array;
    }

    /**
     * @param $role
     * @return array|null
     */
    public function getAttributePermissionByRole($role)
    {
        if ($this->_arrayPermissions === null) {
            $collection = $this->getCollection();
            $this->_arrayPermissions = [];

            foreach ($collection->getAttributeByRole($role) as $attr) {
                $this->_arrayPermissions[$attr->getAttributeId()] = $attr->getIsAllow();
            }
        }

        return $this->_arrayPermissions;
    }
}
