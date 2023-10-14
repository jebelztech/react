<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Helper\Dashboard;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Framework\App\Helper\Context;
use Magento\Reports\Model\ResourceModel\Order\Collection;

class Order extends \Magento\Backend\Helper\Dashboard\Order
{

    /**
     * @var Data
     */
    protected $helper;

    /**
     * Order constructor.
     *
     * @param Context $context
     * @param Collection $orderCollection
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        Collection $orderCollection,
        Data $helper
    ) {
        parent::__construct(
            $context,
            $orderCollection
        );
        $this->helper = $helper;
    }

    /**
     * Change collection
     *
     * @return void
     */
    protected function _initCollection()
    {
        $isFilter          = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');
        $this->_collection = $this->_orderCollection->prepareSummary($this->getParam('period'), 0, 0, $isFilter);
        if (!$isFilter && $this->_collection->isLive() && $storeIds = $this->helper->getAllowedStoreViewIds()) {
            $this->_collection->addFieldToFilter('main_table.store_id', ['in' => $storeIds])->load();
        } else {
            parent::_initCollection();
        }
    }
}
