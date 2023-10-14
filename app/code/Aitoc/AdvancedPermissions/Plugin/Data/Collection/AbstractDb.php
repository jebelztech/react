<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Data\Collection;

use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Helper\Sales;
use Magento\Framework\DB\Select;
use Magento\Sales\Model\ResourceModel\Order\Creditmemo;

class AbstractDb
{
    /**
     * @var Sales
     */
    protected $salesHelper;

    /**
     * AbstractDb constructor.
     *
     * @param Data $helper
     */
    public function __construct(
        Sales $helper
    ) {
        $this->salesHelper = $helper;
    }

    /**
     * Add filters for sub-Admin
     * Only for sales collections
     *
     * @param $subject
     */
    public function beforeGetSize($subject)
    {
        if ($this->salesHelper->isSalesResource($subject->getResource())
            && $this->salesHelper->isAdvancedPermissionEnabled()
        ) {
            $this->salesHelper->addAllowedProductFilterIfRequired($subject);
        }
    }

    /**
     * Make DISTINCT for Count.
     * Only for sales collections
     *
     * @param $subject
     * @param Select $countSelect
     *
     * @return Select
     */
    public function afterGetSelectCountSql($subject, Select $countSelect)
    {
        if ($this->salesHelper->isSalesResource($subject->getResource())
            && $this->salesHelper->isAdvancedPermissionEnabled()
            && $countSelect->getPart('distinct')
        ) {
            $countSelect->reset(Select::COLUMNS);
            $countSelect->columns('COUNT(DISTINCT main_table.entity_id)');
        }

        return $countSelect;
    }
}
