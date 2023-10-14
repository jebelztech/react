<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model;

use Aitoc\AdvancedPermissions\Api\Data\EditorTabInterface;

class EditorTab extends \Magento\Framework\Model\AbstractModel implements EditorTabInterface
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
        $this->_init(\Aitoc\AdvancedPermissions\Model\ResourceModel\EditorTab::class);
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
    public function getTabCode()
    {
        return $this->getData(self::TAB_CODE);
    }

    /**
     * Set ID
     *
     * @return EditorTab
     */
    public function setId($id)
    {
        $this->setData(self::ID, $id);

        return  $this;
    }

    /**
     * Set Original Role Id
     *
     * @return EditorTab
     */
    public function setAdvancedId($originalId)
    {
        $this->setData(self::ADVANCED_ROLE_ID, $originalId);

        return  $this;
    }

    /**
     * Set Tab Code
     *
     * @return EditorTab
     */
    public function setTabCode($tabCode)
    {
        $this->setData(self::TAB_CODE, $tabCode);

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
    public function getDisabledTabs($roleId)
    {
        $tabs             = [];
        $recordCollection = $this->getCollection()->loadByRoleId($roleId);

        if ($recordCollection->getSize()) {
            foreach ($recordCollection as $record) {
                $tabs[] = $record->getTabCode();
            }
        }

        if (count($tabs) > 0) {
            return $tabs;
        }

        return false;
    }

    /**
     * @param $oldRoleId
     * @param $newRoleId
     * @throws Exception
     */
    public function duplicateProductTabPermissions($oldRoleId, $newRoleId)
    {
        $oldTabs = $this->getDisabledTabs($oldRoleId);
        if ($oldTabs) {
            foreach ($oldTabs as $tab) {
                $this->setAdvancedId($newRoleId);
                $this->setTabCode($tab);
                $this->save();
            }
        }
    }
}
