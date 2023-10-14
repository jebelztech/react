<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Newsletter\Queue\Grid;

use Aitoc\AdvancedPermissions\Model\ResourceModel\Newsletter\Queue\Collection as BaseCollection;

/**
 * Class Collection
 */
class Collection extends BaseCollection
{
    /**
     * @return $this
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->addSubscribersInfo();

        return $this;
    }
}
