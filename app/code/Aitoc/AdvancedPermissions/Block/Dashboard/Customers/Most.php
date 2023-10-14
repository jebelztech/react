<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Block\Dashboard\Customers;

use Aitoc\AdvancedPermissions\Helper\Dashboard\Customers as CustomersHelper;
use Aitoc\AdvancedPermissions\Helper\Data as Helper;
use Magento\Backend\Block\Dashboard\Tab\Customers\Most as BaseMost;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Reports\Model\ResourceModel\Order\Collection;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;

/**
 * Class Most
 */
class Most extends BaseMost
{
    protected $_template = "Magento_Backend::dashboard/grid.phtml";

    /**
     * @var Helper
     */
    private $helper;

    /**
     * @var CustomersHelper
     */
    private $customersHelper;

    /**
     * Most constructor.
     * @param Context $context
     * @param Data $backendHelper
     * @param CollectionFactory $collectionFactory
     * @param Helper $helper
     * @param CustomersHelper $customersHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        CollectionFactory $collectionFactory,
        Helper $helper,
        CustomersHelper $customersHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $collectionFactory, $data);
        $this->helper = $helper;
        $this->customersHelper = $customersHelper;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareCollection()
    {
        if (!$this->isAdvancedPermissionsEnabled()) {
            return parent::_prepareCollection();
        }

        $collection = $this->createCollection();
        $collection->groupByCustomer()->addOrdersCount()->joinCustomerName();

        $this->setStoreIdsFilter($collection);

        $storeFilter = $this->getStoreFilter();
        $collection->addSumAvgTotals($storeFilter)->orderByTotalAmount();

        $this->setCollection($collection);

        return Extended::_prepareCollection();
    }

    /**
     * @return bool
     */
    private function isAdvancedPermissionsEnabled()
    {
        return $this->helper->isAdvancedPermissionEnabled();
    }

    /**
     * @return Collection
     */
    private function createCollection()
    {
        return $this->_collectionFactory->create();
    }

    /**
     * @param Collection $collection
     */
    private function setStoreIdsFilter(Collection $collection)
    {
        $this->customersHelper->setStoreIdsFilter($this, $collection);
    }

    /**
     * @return int
     */
    private function getStoreFilter()
    {
        return $this->customersHelper->getStoreFilter($this);
    }
}
