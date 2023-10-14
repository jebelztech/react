<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Plugin\Sales\Order\Model\ResourceModel\Grid;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OrderGridCollection;

/**
 * Class Collection
 */
class Collection
{
    /**
     * @param OrderGridCollection $orderGridCollection
     * @param string|array $field
     * @param null|string|array $condition
     * @return array
     */
    public function beforeAddFieldToFilter(OrderGridCollection $orderGridCollection, $field, $condition = null)
    {
        if ($field === 'store_id') {
            $field = 'main_table.store_id';
        }

        return [$field, $condition];
    }
}
