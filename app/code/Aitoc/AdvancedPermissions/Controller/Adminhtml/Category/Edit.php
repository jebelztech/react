<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Controller\Adminhtml\Category;

class Edit extends \Magento\Catalog\Controller\Adminhtml\Category\Edit
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Aitoc\AdvancedPermissions\Helper\Data $data
    ) {
        $this->helper = $data;
        parent::__construct($context, $resultPageFactory, $resultJsonFactory);
    }

    /**
     * Edit category page
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function execute()
    {
        $storeId = (int)$this->getRequest()->getParam('store');
        $store = $this->getStoreManager()->getStore($storeId);
        $this->getStoreManager()->setCurrentStore($store->getCode());

        $categoryId = (int)$this->getRequest()->getParam('id');

        if (!$categoryId) {
            if ($storeId) {
                $categoryId = (int)$this->getStoreManager()->getStore($storeId)->getRootCategoryId();
            } else {
                $defaultStoreView = $this->getStoreManager()->getDefaultStoreView();

                if ($this->helper->isAdvancedPermissionEnabled()) {
                    $allowedStoreIds = $this->helper->getAllowedStoreIds();

                    if ($allowedStoreIds) {
                        foreach ($allowedStoreIds as $allowedStoreId) {
                            $store = $this->helper->getStore($allowedStoreId);

                            if ($store->getId() && $store->getRootCategoryId()) {
                                $categoryId = $store->getRootCategoryId();

                                break;
                            }
                        }
                    }
                }

                if ($defaultStoreView && !$categoryId) {
                    $categoryId = (int)$defaultStoreView->getRootCategoryId();
                } else {
                    $stores = $this->getStoreManager()->getStores();
                    if (count($stores)) {
                        $store = reset($stores);
                        $categoryId = (int)$store->getRootCategoryId();
                    }
                }
            }
            $this->getRequest()->setParam('id', $categoryId);
        }

        $category = $this->_initCategory(true);
        if (!$category || $categoryId != $category->getId() || !$category->getId()) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        /**
         * Check if there are data in session (if there was an exception on saving category)
         */
        $categoryData = $this->_getSession()->getCategoryData(true);
        if (is_array($categoryData)) {
            if (isset($categoryData['image']['delete'])) {
                $categoryData['image'] = null;
            } else {
                unset($categoryData['image']);
            }
            $category->addData($categoryData);
        }

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        if ($this->getRequest()->getQuery('isAjax')) {
            return $this->ajaxRequestResponse($category, $resultPage);
        }

        $resultPageTitle = $categoryId ? $category->getName() . ' (ID: ' . $categoryId . ')' : __('Categories');
        $resultPage->setActiveMenu('Magento_Catalog::catalog_categories');
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));
        $resultPage->getConfig()->getTitle()->prepend($resultPageTitle);
        $resultPage->addBreadcrumb(__('Manage Catalog Categories'), __('Manage Categories'));

        return $resultPage;
    }

    /**
     * @return \Magento\Store\Model\StoreManagerInterface
     */
    private function getStoreManager()
    {
        if (null === $this->storeManager) {
            $this->storeManager = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Store\Model\StoreManagerInterface::class);
        }
        return $this->storeManager;
    }
}
