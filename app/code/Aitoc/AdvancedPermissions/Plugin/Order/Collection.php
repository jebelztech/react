<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Order;

use Aitoc\AdvancedPermissions\Helper\Sales;
use Closure;
use Magento\Eav\Model\Entity\Attribute;
use Magento\Sales\Model\ResourceModel\Order\Collection as OrderCollection;

/**
 * Class Collection
 */
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
     * @param OrderCollection $subject
     * @param Closure $work
     * @param string|array $field
     * @param null|string|array $condition
     * @return array
     */
    public function beforeAddFieldToFilter(
        OrderCollection $subject,
        $field,
        $condition
    ) {
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return null;
        }

        if ($this->fieldContainsTable($field)) {
            return null;
        }

        $field = $this->addMainTablePrefix($field);

        return [$field, $condition];
    }

    /**
     * @param string $field
     * @return bool
     */
    private function fieldContainsTable($field)
    {
        return strpos($field, '.') !== false;
    }

    /**
     * @param string $field
     * @return string
     */
    private function addMainTablePrefix($field)
    {
        return 'main_table.' . $field;
    }

    /**
     * @param OrderCollection $subject
     * @param string|Attribute $attribute
     * @param array|int|string|null $condition
     * @return array
     */
    public function beforeAddAttributeToFilter(OrderCollection $subject, $attribute, $condition = null)
    {
        if ($attribute == 'store_id') {
            $attribute = 'main_table.store_id';
        }

        return [$attribute, $condition];
    }
}
