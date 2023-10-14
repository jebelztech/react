<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Product\Edit\Action\Attribute\Tab;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Backend\Block\Template\Context;
use Magento\CatalogInventory\Api\StockConfigurationInterface;
use Magento\CatalogInventory\Model\Source\Backorders;

class Inventory extends \Magento\Catalog\Block\Adminhtml\Product\Edit\Action\Attribute\Tab\Inventory
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Inventory constructor.
     *
     * @param Context $context
     * @param Backorders $backorders
     * @param StockConfigurationInterface $stockConfiguration
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Backorders $backorders,
        StockConfigurationInterface $stockConfiguration,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($context, $backorders, $stockConfiguration, $data);
        $this->helper = $helper;
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getManageGlobalAttribute()) {
            return false;
        }

        return true;
    }
}
