<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Order;

class Collection extends \Magento\Reports\Model\ResourceModel\Order\Collection
{
    /**
     * Add period filter by created_at attribute
     *
     * @param string $period
     * @return $this
     */
    public function addCreateAtPeriodFilter($period)
    {
        list($from, $to) = $this->getDateRange($period, 0, 0, true);

        $this->checkIsLive($period);

        if ($this->isLive()) {
            $fieldToFilter = 'main_table.created_at';
        } else {
            $fieldToFilter = 'period';
        }

        $this->addFieldToFilter(
            $fieldToFilter,
            [
                'from' => $from->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
                'to' => $to->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT)
            ]
        );

        return $this;
    }

    protected function _getTZRangeOffsetExpression($range, $attribute, $from = null, $to = null)
    {
        if ($attribute == 'created_at') {
            $attribute = 'main_table.created_at';
        }

        return parent::_getTZRangeOffsetExpression($range, $attribute, $from, $to);
    }
}
