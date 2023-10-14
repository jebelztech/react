<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model\ResourceModel\EditorAttribute;

use Aitoc\AdvancedPermissions\Api\Data\EditorAttributeInterface;
use Aitoc\AdvancedPermissions\Model\EditorAttribute;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection
{
    /**
     * @var int
     */
    protected $_postRoleId = 0;

    /**
     * @var int
     */
    protected $_postAllow = 0;

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_editor_attribute_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'permissions_editor_attribute_collection';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField = 'advanced_role_id';

    /**
     * @var \Aitoc\AdvancedPermissions\Model\EditorAttributeFactory
     */
    protected $editorAttributeFactory;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Model\ResourceModel\Db\VersionControl\Snapshot $entitySnapshot,
        \Aitoc\AdvancedPermissions\Model\EditorAttributeFactory $editorAttributeFactory,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->editorAttributeFactory = $editorAttributeFactory;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $entitySnapshot,
            $connection,
            $resource
        );
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Aitoc\AdvancedPermissions\Model\EditorAttribute::class,
            \Aitoc\AdvancedPermissions\Model\ResourceModel\EditorAttribute::class
        );
    }

    /**
     * @param $roleId
     * @return $this
     */
    public function loadByRoleId($roleId)
    {
        $this->addFieldToFilter(EditorAttributeInterface::ADVANCED_ROLE_ID, $roleId);
        $this->load();

        return $this;
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
     * @param null $roleId
     * @param null $if
     * @return Varien_Data_Collection_Db
     */
    public function getAttributeByRole($roleId = null, $if = null)
    {
        $this->getSelect()->reset(\Zend_Db_Select::WHERE);
        $this->addFieldToFilter(EditorAttributeInterface::ADVANCED_ROLE_ID, $roleId);

        if ($if !== null) {
            $this->addFieldToFilter(EditorAttributeInterface::IS_ALLOW, $if);
        }

        return $this->load();
    }

    /**
     * @param $array
     */
    public function deleteAttributeByRole($array)
    {
        $this->_resetArrtibuteEditorFilter();
        $this->addFieldToFilter(EditorAttributeInterface::ATTRIBUTE_ID, ['in' => $array]);

        foreach ($this->load() as $attr) {
            $attr->delete();
        }
    }

    /**
     * @param $array
     * @throws Exception
     */
    public function addAttributeByRole($array)
    {
        $this->_resetArrtibuteEditorFilter();
        $data = [
            EditorAttributeInterface::ADVANCED_ROLE_ID => $this->_postRoleId,
            EditorAttributeInterface::IS_ALLOW => $this->_postAllow,
        ];

        foreach ($array as $attr) {
            $data['attribute_id'] = $attr;
            $item = $this->editorAttributeFactory->create()->load(null);
            $item->setData($data);
            $item->save();
        }
    }

    /**
     * Reset Attribute
     */
    protected function _resetArrtibuteEditorFilter()
    {
        $this->getSelect()->reset(\Zend_Db_Select::WHERE);
        $this->addFieldToFilter(EditorAttributeInterface::ADVANCED_ROLE_ID, $this->_postRoleId);
        $this->addFieldToFilter(EditorAttributeInterface::IS_ALLOW, $this->_postAllow);
    }

    /**
     * @param $id
     */
    public function setPostRoleId($id)
    {
        $this->_postRoleId = $id;
    }

    /**
     * @param $allow
     */
    public function setPostAllow($allow)
    {
        $this->_postAllow = $allow;
    }

    /**
     * @param $oldRole
     * @param $newRole
     */
    public function duplicateAttributePermissions($oldRole, $newRole)
    {
        $attributes = $this->getAttributeByRole($oldRole);

        foreach ($attributes as $attr) {
            $attr->setId(null);
            $attr->setAdvancedId($newRole);
            $attr->save();
        }
    }
}
