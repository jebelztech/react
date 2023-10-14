<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;

class ProductEditObserver implements ObserverInterface
{
    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * @var ResponseFactory
     */
    protected $responseFactory;
    
    /**
     * @var UrlInterface
     */
    protected $url;
    
    /**
     * InventoryObserver constructor.
     *
     * @param Data $helper
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     */
    public function __construct(
        Data $helper,
        ResponseFactory $responseFactory,
        UrlInterface $url
    ) {
        $this->helper = $helper;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return;
        }
        
        $product            = $observer->getProduct();
        $productCategoryIds = $product->getCategoryIds();
        $roleCategoryIds    = $this->helper->getCategoryIds();
        $allowedStoreIds    = $this->helper->getAllowedStoreIds();
        
        if (array_intersect($allowedStoreIds, $product->getStoreIds())) {
            if (!count($roleCategoryIds) ||
                array_intersect($roleCategoryIds, $productCategoryIds) ||
                $this->helper->getRole()->getAllowNullCategory()
            ) {
                return;
            }
        }
        
        $redirectUrl = $this->url->getUrl('admin/dashboard/index');
        $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
    }
}
