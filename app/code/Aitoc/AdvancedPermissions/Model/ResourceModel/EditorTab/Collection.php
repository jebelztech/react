<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model\ResourceModel\EditorTab;

use \Aitoc\AdvancedPermissions\Api\Data\EditorTabInterface;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_editor_tab_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'permissions_editor_tab_collection';

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
            \Aitoc\AdvancedPermissions\Model\EditorTab::class,
            \Aitoc\AdvancedPermissions\Model\ResourceModel\EditorTab::class
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
        $this->addFieldToFilter(EditorTabInterface::ADVANCED_ROLE_ID, $roleId);
        $this->load();

        return $this;
    }

    /**
     * @param $oldRoleId
     * @param $newRoleId
     */
    public function duplicateProductTabPermissions($oldRoleId, $newRoleId)
    {
        $oldTabs = $this->loadByRoleId($oldRoleId);

        foreach ($oldTabs as $tab) {
            $tab->setData(EditorTabInterface::ID, null);
            $tab->setData(EditorTabInterface::ADVANCED_ROLE_ID, $newRoleId);
            $tab->setData(EditorTabInterface::TAB_CODE, $tab->getTabCode());

            $tab->save();
        }
    }
}
