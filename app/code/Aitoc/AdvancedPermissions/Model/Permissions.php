<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Cms\Model\Block;
use Magento\Cms\Model\Page;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Model\AbstractModel;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Order\Shipment;

class Permissions
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Product
     */
    protected $productModel;

    /**
     * @var Page
     */
    protected $pageModel;

    /**
     * @var Block
     */
    protected $blockModel;

    /**
     * @var Order
     */
    protected $orderModel;

    /**
     * block only for first check. To avoid block menu.
     *
     * @var array
     */
    protected $checkedResource = [];

    /**
     * @var Customer
     */
    protected $customerModel;

    /**
     * @var Invoice
     */
    protected $invoiceModel;

    /**
     * @var Shipment
     */
    protected $shipmentModel;

    /**
     * @var CollectionFactory
     */
    protected $categories;
    
    /**
     * @param Context $context
     * @param Data $helper
     * @param Product $productModel
     * @param Page $pageModel
     * @param Block $blockModel
     * @param Order $orderModel
     * @param Customer $customer
     * @param Invoice $invoiceModel
     * @param Shipment $shipmentModel
     */
    public function __construct(
        Context $context,
        Data $helper,
        Product $productModel,
        Page $pageModel,
        Block $blockModel,
        Order $orderModel,
        Customer $customer,
        Invoice $invoiceModel,
        Shipment $shipmentModel,
        CollectionFactory $categories
    ) {
        $this->request       = $context->getRequest();
        $this->helper        = $helper;
        $this->productModel  = $productModel;
        $this->pageModel     = $pageModel;
        $this->blockModel    = $blockModel;
        $this->orderModel    = $orderModel;
        $this->customerModel = $customer;
        $this->invoiceModel  = $invoiceModel;
        $this->shipmentModel = $shipmentModel;
        $this->categories    = $categories;
    }

    /**
     * Disallow for direct link.
     *
     * @param string $resource
     * @param bool $parentResult
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function isAllowedResource($resource, $parentResult)
    {
        /** InLineEdit Fix */
        if ($this->isCMSAdmin($resource) || $this->isCustomerAdmin($resource)) {
            $parentResult = true;
        }

        /**
         * if it is already has restricted, we don't need to doing additional check.
         * checkedResource avoid to blocking menu item
         */
        if (!$parentResult
            || isset($this->checkedResource[$resource])
            || !$this->helper->isAdvancedPermissionEnabled()
            || !count($allowedStoreIds = $this->helper->getAllowedStoreViewIds())
        ) {
            return $parentResult;
        }
    
        $allowedStoreIds[] = 0;
        $id                = $this->getParamId($resource);
        $action            = $this->request->getModuleName() . $this->request->getActionName();
        $controller        = $this->request->getControllerName();
        
        switch (true) {
            /**
             * Catalog Product Edit
             */
            case ($resource == 'Magento_Catalog::products' && $id):
                if ($action == 'catalogedit' && $controller == 'product') {
                    $product = $this->loadItem($this->productModel, $id, $resource);
                    /** product grouped by website id, for store he have only scope for some attributes */
                    return (bool)array_intersect($this->helper->getAllowedWebsiteIds(), $product->getWebsiteIds());
                }
                break;
            /**
             * CMS Page Edit
             */
            case ($resource == 'Magento_Cms::save' && $id):
                $page       = $this->loadItem($this->pageModel, $id, $resource);
                $pageStores = is_array($page->getStores()) ? $page->getStores() : [$page->getStores()];

                return (bool)array_intersect($allowedStoreIds, $pageStores);
            /**
             * CMS Block Edit
             */
            case ($resource == 'Magento_Cms::block' && $id):
                $block       = $this->loadItem($this->blockModel, $id, $resource);
                $blockStores = is_array($block->getStores()) ? $block->getStores() : [$block->getStores()];

                return (bool)array_intersect($allowedStoreIds, $blockStores);
            /**
             * Sales Order View
             */
            case ($resource == 'Magento_Sales::actions_view' && $id):
                $order = $this->loadItem($this->orderModel, $id, $resource);

                return (bool)in_array($order->getStoreId(), $allowedStoreIds);
            /**
             * Sales Shipment View
             */
            case ($resource == 'Magento_Sales::shipment' && $id):
                $shipment = $this->loadItem($this->shipmentModel, $id, $resource);

                // $hasOnlyZeroStoreId For super-admin ( Super admin has disabled AdvancedPermissions )
                return in_array($shipment->getStoreId(), $allowedStoreIds);
            /**
             * Sales Invoice View
             */
            case ($resource == 'Magento_Sales::sales_invoice' && $id):
                $invoice = $this->loadItem($this->invoiceModel, $id, $resource);

                return in_array($invoice->getStoreId(), $allowedStoreIds);
            /**
             * Customer View
             */
            case ($resource == 'Magento_Customer::manage' && $id):
                if ($action == 'customeredit') {
                    $customer = $this->loadItem($this->customerModel, $id, $resource);
                    return (bool)in_array($customer->getWebsiteId(), $this->helper->getAllowedWebsiteIds());
                }
                break;
            /**
             * CMS Page and CMS Block Inline Edit
             */
            case ($resource == \Magento\Backend\App\AbstractAction::ADMIN_RESOURCE
                && $items = $this->request->getParam('items', false)
            ):
                foreach ($items as $item) {
                    if (isset($item['block_id'])) {
                        $blockId     = $item['block_id'];
                        $block       = $this->loadItem($this->blockModel, $blockId, 'Magento_Cms::block');
                        $blockStores = is_array($block->getStores()) ? $block->getStores() : [$block->getStores()];

                        return (bool)array_intersect($allowedStoreIds, $blockStores);
                    }
                    if (isset($item['page_id'])) {
                        $pageId     = $item['page_id'];
                        $page       = $this->loadItem($this->pageModel, $pageId, 'Magento_Cms::save');
                        $pageStores = is_array($page->getStores()) ? $page->getStores() : [$page->getStores()];

                        return (bool)array_intersect($allowedStoreIds, $pageStores);
                    }
                }
        }

        return $parentResult;
    }

    /**
     * Making load by $id from $model;
     *
     * @param AbstractModel $model
     * @param int $id
     * @param string $resource
     *
     * @return AbstractModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function loadItem(AbstractModel $model, $id, $resource)
    {
        if (is_object($model) && $model->getId() != $id) {
            $model->load($id);
        }
        if (!is_object($model) || !$model->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                new \Magento\Framework\Phrase('Wrong ID')
            );
        }
        /** for avoid to blocking menu item */
        $this->checkedResource[$resource] = true;

        return $model;
    }

    /**
     * Is current Action is In Line Edit for CMS
     *
     * @param string $resource
     *
     * @return bool
     */
    protected function isCMSAdmin($resource)
    {
        return ($this->request->getModuleName() == 'cms'
            && $resource == \Magento\Backend\App\AbstractAction::ADMIN_RESOURCE
            && $this->request->getActionName() == 'inlineEdit');
    }

    /**
     * Is current Action is In Line Edit for Customer
     *
     * @param string $resource
     *
     * @return bool
     */
    protected function isCustomerAdmin($resource)
    {
        return ($this->request->getModuleName() == 'customer'
            && $resource == \Magento\Backend\App\AbstractAction::ADMIN_RESOURCE
            && $this->request->getActionName() == 'inlineEdit');
    }

    /**
     * Get Id Parameter from request
     *
     * @param string $resource
     *
     * @return int | false
     */
    protected function getParamId($resource)
    {
        switch ($resource) {
            case 'Magento_Sales::sales_invoice':
                $idKey = 'invoice_id';
                break;
            case 'Magento_Sales::shipment':
                $idKey = 'shipment_id';
                break;
            case 'Magento_Cms::save':
                $idKey = 'page_id';
                break;
            case 'Magento_Cms::block':
                $idKey = 'block_id';
                break;
            case 'Magento_Sales::actions_view':
                $idKey = 'order_id';
                break;
            default:
                $idKey = 'id';
                break;
        }

        return $this->request->getParam($idKey, false);
    }
    
    /**
     * Retrieve categories tree
     *
     * @param array $tree
     * @param mixed $forStore
     * @return array
     */
    public function getAllowedCategoriesTree($tree, $forStore = null)
    {
        if (!$this->helper->getScope()) {
            return $tree;
        }

        $allowedRootIds    = $this->helper->getAllowedRootCategories($forStore);
        $advancedCategories = Data::ADVANCED_CATEGORIES;
        $categoriesByStore = $this->helper->getTree($advancedCategories, true);

        $result = [];
        foreach ($tree as $treeRoot) {
            $showFull = false;
            
            $rootId = isset($treeRoot['id']) ? $treeRoot['id'] : (
                isset($treeRoot['value']) ? $treeRoot['value'] : null
            );

            // is it really root category?
            if ($rootId != null && in_array($rootId, $this->_getRootCategories())) {
                // hide root if assigned store is not allowed
                if (!isset($allowedRootIds[$rootId])) {
                    continue;
                }
                
                // case: multiple stores with same root category
                foreach ($allowedRootIds[$rootId] as $storeId) {
                    if (empty($categoriesByStore[$storeId])) {
                        $showFull = true;
                    }
                }
            
            // not root but store is definied (and not DEFAULT_STORE_ID)
            } elseif ($forStore && empty($categoriesByStore[$forStore])) {
                $showFull = true;
            }
            
            $newNode = $showFull ? [$treeRoot] : $this->_siftNode($treeRoot);
            $result  = array_merge($result, $newNode);
        }

        return $result;
    }
    
    /**
     * Retrieve category branch
     *
     * @param array $node
     * @param int $level
     * @return array
     */
    protected function _siftNode($node, $level = 0)
    {
        $cats = $this->helper->getCategoryIds();
        
        if (empty($cats)) {
            return [$node];
        }
        $keyChildren = isset($node['id']) ? 'children' : 'optgroup';
        $keyId       = isset($node['id']) ? 'id' : 'value';
        
        $visible     = in_array($node[$keyId], $cats);
        
        $children = empty($node[$keyChildren]) ? [] : $node[$keyChildren];
        $node[$keyChildren] = [];
        
        foreach ($children as $element) {
            $itemsNew = $this->_siftNode($element, $level+(int)$visible);
            $node[$keyChildren] = array_merge($node[$keyChildren], $itemsNew);
        }
        
        return $visible ? [$node] : $node[$keyChildren];
    }
    
    /**
     * Retrieve root category ids
     *
     * @return array
     */
    protected function _getRootCategories()
    {
        if (!isset($this->_all_root_category_ids)) {
            $rootCategories = $this->categories->create();
            $rootCategories->addRootLevelFilter()->load();
            $this->_all_root_category_ids = $rootCategories->getAllIds();
        }
        return $this->_all_root_category_ids;
    }
}
