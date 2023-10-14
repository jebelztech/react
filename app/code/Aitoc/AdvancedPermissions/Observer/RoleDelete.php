<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Observer;

use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\Role;
use Aitoc\AdvancedPermissions\Model\Stores;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class RoleDelete implements ObserverInterface
{
    /**
     * @var Role
     */
    private $role;

    /**
     * @var Stores
     */
    protected $stores;

    /**
     * RoleSave constructor.
     *
     * @param Data $helper
     * @param Role $roleAdv
     */
    public function __construct(
        Role $roleAdv,
        Stores $storesAdv
    ) {
        $this->role   = $roleAdv;
        $this->stores = $storesAdv;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $event        = $observer->getEvent();
        $role         = $event->getDataObject();
        $roleAdvanced = $this->role->loadOriginal($role->getId());

        $this->stores->getCollection()
            ->setRoleFilter($roleAdvanced->getId())
            ->walk('delete');

        $roleAdvanced->delete();

        return;
    }
}
