<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model\ResourceModel\EditorType;

use Aitoc\AdvancedPermissions\Api\Data\EditorTypeInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_editor_type_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'permissions_editor_type_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField = 'advanced_role_id';

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aitoc\AdvancedPermissions\Model\EditorType::class,
            \Aitoc\AdvancedPermissions\Model\ResourceModel\EditorType::class
        );
    }

    /**
     * Add role filter
     *
     * @param int|\Magento\Sales\Model\Order|array $order
     *
     * @return $this
     */
    public function setRoleFilter($role)
    {
        $this->addFieldToFilter($this->_orderField, $role);

        return $this;
    }

    /**
     * @param $roleId
     * @return $this
     */
    public function loadByRoleId($roleId)
    {
        $this->addFieldToFilter(EditorTypeInterface::ADVANCED_ROLE_ID, $roleId);
        $this->load();

        return $this;
    }

    /**
     * @param $oldRoleId
     * @param $newRoleId
     */
    public function duplicateProductTypePermissions($oldRoleId, $newRoleId)
    {
        $oldTypes = $this->loadByRoleId($oldRoleId);

        foreach ($oldTypes as $type) {
            $type->setData(EditorTypeInterface::ID, null);
            $type->setData(EditorTypeInterface::ADVANCED_ROLE_ID, $newRoleId);
            $type->setData(EditorTypeInterface::TYPE, $type->getType());
            $type->save();
        }
    }
}
