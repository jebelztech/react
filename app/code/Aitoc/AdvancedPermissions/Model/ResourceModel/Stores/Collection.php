<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Stores;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'advanced_permissions_stores_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'permissions_stores_collection';

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
        $this->_init('Aitoc\AdvancedPermissions\Model\Stores', 'Aitoc\AdvancedPermissions\Model\ResourceModel\Stores');
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
}
