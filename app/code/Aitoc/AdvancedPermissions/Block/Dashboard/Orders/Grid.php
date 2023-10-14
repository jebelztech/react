<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Block\Dashboard\Orders;

use Aitoc\AdvancedPermissions\Helper\Data as ApHelper;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Module\Manager;
use Magento\Reports\Model\ResourceModel\Order\CollectionFactory;

class Grid extends \Magento\Backend\Block\Dashboard\Orders\Grid
{
    protected $_template = 'Magento_Backend::dashboard/grid.phtml';

    /**
     * @var ApHelper
     */
    protected $helper;

    /**
     * Grid constructor.
     *
     * @param Context $context
     * @param BackendHelper $backendHelper
     * @param Manager $moduleManager
     * @param CollectionFactory $collectionFactory
     * @param ApHelper $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        BackendHelper $backendHelper,
        Manager $moduleManager,
        CollectionFactory $collectionFactory,
        ApHelper $helper,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $backendHelper,
            $moduleManager,
            $collectionFactory,
            $data
        );
        $this->helper = $helper;
    }

    /**
     * Change condition for collection
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $isFilter = $this->getParam('store') || $this->getParam('website') || $this->getParam('group');
        if (!$isFilter && $storeIds = $this->helper->getAllowedStoreViewIds()) {
                $this->getCollection()->addFieldToFilter('store_id', ['in' => $storeIds]);
        }

        return $this;
    }
}
