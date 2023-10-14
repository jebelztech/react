<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Plugin\Newsletter\Model\ResourceModel\Subscriber;

use Aitoc\AdvancedPermissions\Helper\Data as ApHelper;
use Magento\Newsletter\Model\ResourceModel\Subscriber\Collection as SubscriberCollection;

/**
 * Class CollectionPlugin
 */
class CollectionPlugin
{
    /**
     * @var ApHelper
     */
    private $apHelper;

    public function __construct(ApHelper $apHelper)
    {
        $this->apHelper = $apHelper;
    }

    public function afterShowStoreInfo(SubscriberCollection $subject, SubscriberCollection $result)
    {
        if (!$this->apHelper->isAdvancedPermissionEnabled()) {
            return $result;
        }

        $this->addStoreIdFilter($subject);

        return $subject;
    }

    private function addStoreIdFilter(SubscriberCollection $subject)
    {
        $select = $subject->getSelect();
        $allowedStoreIds = $this->apHelper->getAllowedStoreViewIds();

        $select->where('main_table.store_id IN (?)', $allowedStoreIds);
    }
}
