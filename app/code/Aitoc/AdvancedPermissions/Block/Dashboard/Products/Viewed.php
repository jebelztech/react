<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Dashboard\Products;

use Aitoc\AdvancedPermissions\Helper\Data as ApHelper;
use Aitoc\AdvancedPermissions\Model\ResourceModel\Product\CollectionFactory as ApProductCollectionFactory;
use Magento\Backend\Block\Dashboard\Grid;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Reports\Model\ResourceModel\Product\CollectionFactory as CoreProductCollectionFactory;
use Aitoc\AdvancedPermissions\Helper\Dashboard\Customers as DashboardGridHelper;

class Viewed extends \Magento\Backend\Block\Dashboard\Tab\Products\Viewed
{
    protected $_template = "Magento_Backend::dashboard/grid.phtml";

    /**
     * @var ApHelper
     */
    protected $helper;

    /**
     * @var ApProductCollectionFactory
     */
    protected $productsFactoryAdv;

    /**
     * @var DashboardGridHelper
     */
    protected $dashboardGridHelper;

    /**
     * Viewed constructor.
     *
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param CoreProductCollectionFactory $productsFactory
     * @param ApProductCollectionFactory $productsFactoryAdv
     * @param ApHelper $helper
     * @param DashboardGridHelper $dashboardGridHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        CoreProductCollectionFactory $productsFactory,
        ApProductCollectionFactory $productsFactoryAdv,
        ApHelper $helper,
        DashboardGridHelper $dashboardGridHelper,
        array $data = []
    ) {
        parent::__construct($context, $backendHelper, $productsFactory, $data);
        $this->helper = $helper;
        $this->dashboardGridHelper = $dashboardGridHelper;
        $this->productsFactoryAdv = $productsFactoryAdv;
    }

    /**
     * Change Collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $storeIds = $this->getStoreIds();
        $storeId = array_pop($storeIds);

        if (!$this->getParam('website') && !$this->getParam('group')) {
            $collection = $this->productsFactoryAdv->create()
                ->addAttributeToSelect('*')
                ->addViewsCount()
                ->setStoreId($storeId)
                ->addStoresFilter($storeIds);
        } else {
            $collection = $this->_productsFactory->create()
                ->addAttributeToSelect('*')
                ->addViewsCount()
                ->setStoreId($storeId)
                ->addStoreFilter($storeId);
        }

        $this->setCollection($collection);

        return Grid::_prepareCollection();
    }

    /**
     * @return int[]
     */
    private function getStoreIds()
    {
        return $this->dashboardGridHelper->getStoreIds($this);
    }
}
