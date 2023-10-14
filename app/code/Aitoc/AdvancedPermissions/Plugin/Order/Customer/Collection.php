<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Order\Customer;

use Aitoc\AdvancedPermissions\Helper\Sales;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Order\Customer\Collection as OrderCustomerCollection;

class Collection
{
    /**
     * @var Sales
     */
    protected $helper;

    /**
     * Collection constructor.
     *
     * @param Sales $helper
     */
    public function __construct(
        Sales $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * @param OrderCustomerCollection $subject
     */
    public function beforeLoad(
        OrderCustomerCollection $subject,
        $printQuery = false,
        $logQuery = false
    ) {
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getShowAllCustomers()) {
            $allowedStoreIds = $this->helper->getAllowedStoreIds();
            $subject->addFieldToFilter('store_id', ['in' => $allowedStoreIds]);
        }

        return null;
    }
    
    /**
     * Get SQL for get record count
     *
     * @return Select
     */
    public function afterGetSelectCountSql(OrderCustomerCollection $subject, $result)
    {
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getShowAllCustomers()) {
            $result->where('e.store_id IN (?)', $this->helper->getAllowedStoreIds());
        }
        return $result;
    }
}
