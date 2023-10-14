<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model;

use Aitoc\AdvancedPermissions\Api\Data\EditorTypeInterface;

class EditorType extends \Magento\Framework\Model\AbstractModel implements EditorTypeInterface
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_editor_tab';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Aitoc\AdvancedPermissions\Model\ResourceModel\EditorType::class);
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
     * @return mixed|string|null
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * Set ID
     *
     * @return EditorTypeInterface
     */
    public function setId($id)
    {
        $this->setData(self::ID, $id);

        return  $this;
    }

    /**
     * Set Original Role Id
     *
     * @return EditorTypeInterface
     */
    public function setAdvancedId($originalId)
    {
        $this->setData(self::ADVANCED_ROLE_ID, $originalId);

        return  $this;
    }

    /**
     * Set Tab Code
     *
     * @return EditorTypeInterface
     */
    public function setType($type)
    {
        $this->setData(self::TYPE, $type);

        return $this;
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
     * @param $roleId
     * @return array|bool
     */
    public function getRestrictedTypes($roleId)
    {
        $types            = [];
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize()) {
            foreach ($recordCollection as $record) {
                $types[] = $record->getType();
            }
        }

        if (count($types) > 0) {
            return array_unique($types);
        }

        return false;
    }
}
