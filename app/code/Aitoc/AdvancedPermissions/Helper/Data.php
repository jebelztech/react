<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Helper;

use Aitoc\AdvancedPermissions\Model\Role;
use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;

class Data extends \Magento\Framework\App\Helper\AbstractHelper implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    protected $role;

    protected $stores;

    const ADVANCED_CATEGORIES = 'categories';

    const ADVANCED_STOREVIEWS = 'store_views';

    const SCOPE_STORE = 1;

    const SCOPE_WEBSITE = 2;

    protected $auth;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\EditorAttributeFactory
     */
    protected $editorAttributeFactory;

    /**
     * @var \Aitoc\Core\Helper\Extensions
     */
    protected $extensions;

    /**
     * @var \Magento\Catalog\Api\ProductAttributeRepositoryInterface
     */
    protected $repository;

    public function __construct(
        Context $context,
        \Aitoc\Core\Helper\Extensions $extensions,
        \Aitoc\AdvancedPermissions\Model\EditorAttributeFactory $editorAttributeFactory,
        \Magento\Catalog\Api\ProductAttributeRepositoryInterface $repository
    ) {
        parent::__construct($context);
        $this->extensions = $extensions;
        $this->editorAttributeFactory = $editorAttributeFactory;
        $this->repository = $repository;
    }

    /**
     * Return access for manages stores
     *
     * @return bool
     */
    public function isAdvancedPermissionEnabled()
    {
        if (!$role = $this->getRole()) {
            return false;
        }

        return (bool) $role->getScope();
    }

    /**
     * @param AbstractAttribute $attribute
     *
     * @return bool
     */
    public function isAttributeRestricted(AbstractAttribute $attribute)
    {
        if ($attribute->getEntityType()->getEntityTypeCode() != \Magento\Catalog\Model\Product::ENTITY
            || $attribute->getAttributeCode() == 'entity_id'
        ) {
            return false;
        }
        $allowedScopes = [];
        if ($this->getRole()->getManageGlobalAttribute()) {
            $allowedScopes[] = ScopedAttributeInterface::SCOPE_GLOBAL;
        }
        switch ($this->getRole()->getScope()) {
            case self::SCOPE_WEBSITE:
                $allowedScopes[] = ScopedAttributeInterface::SCOPE_WEBSITE;
            // no break
            case self::SCOPE_STORE:
                $allowedScopes[] = ScopedAttributeInterface::SCOPE_STORE;
                break;
        }

        return !in_array($attribute->getIsGlobal(), $allowedScopes);
    }

    /**
     * Check if functionality is allowed
     *
     * @return bool
     */
    public function isAdvancedPermissionAllowed()
    {
        $methods = debug_backtrace();
        foreach ($methods as $call) {
            $call['class'] = isset($call['class']) ? $call['class'] : '';
            $call['function'] = isset($call['function']) ? $call['function'] : '';
            if ($call['class'] == 'Magento\\Eav\\Model\\ResourceModel\\Entity\\Attribute' &&
                $call['function'] == '_updateAttributeOptionValues'
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get allowed store view Ids
     * If scope dissabled, then empty array of values.
     *
     * @return array
     */
    public function getAllowedStoreViewIds($withCodes = false)
    {
        if ($withCodes && !isset($this->allowed_store_view_ids_with_codes) ||
            !$withCodes && !isset($this->allowed_store_view_ids)
        ) {
            $storeIds = [];
            if (!$this->getScope()) {
                $storesArray = \Magento\Framework\App\ObjectManager::getInstance()->get(
                    'Aitoc\AdvancedPermissions\Model\Store\StoreManager'
                )->getStoresAll();
                foreach ($storesArray as $web) {
                    if (!$withCodes) {
                        $storeIds[] = $web->getId();
                    } else {
                        $storeIds[] = $web->getCode();
                    }
                }
            } elseif ($this->getScope() == self::SCOPE_STORE) {
                $storeIds = $this->getTree(self::ADVANCED_STOREVIEWS, false);
                if ($withCodes) {
                    $elements = $storeIds;
                    $storeIds = [];
                    foreach ($elements as $value) {
                        $store= $this->getStore($value);
                        $storeIds[] = $store->getCode();
                    }
                }
            } elseif ($this->getScope() == self::SCOPE_WEBSITE) {
                $websites = explode(",", $this->getRole()->getWebsiteId());
                foreach ($this->getStoresFromWebsite($websites) as $website) {
                    if (!$withCodes) {
                        $storeIds[] = $website->getId();
                    } else {
                        $storeIds[] = $website->getCode();
                    }
                }
            }

            if ($withCodes) {
                $this->allowed_store_view_ids_with_codes = $storeIds;
            } else {
                $this->allowed_store_view_ids = $storeIds;
            }
        }

        if ($withCodes) {
            return $this->allowed_store_view_ids_with_codes;
        } else {
            return $this->allowed_store_view_ids;
        }
    }

    /**
     * Get allowed website Ids
     * If scope dissabled, then empty array of values.
     *
     * @return array
     */
    public function getAllowedWebsiteIds($withCodes = false)
    {
        $websites = [];
        if (!$this->getScope()) {
            $websitesArray = \Magento\Framework\App\ObjectManager::getInstance()->get(
                'Aitoc\AdvancedPermissions\Model\Store\StoreManager'
            )->getWebsitesAll();
            foreach ($websitesArray as $web) {
                if (!$withCodes) {
                    $websites[] = $web->getId();
                } else {
                    $websites[] = $web->getCode();
                }
            }
        } elseif ($this->getScope() == self::SCOPE_STORE) {
            foreach ($this->getTree(self::ADVANCED_STOREVIEWS, false) as $storeId) {
                $store = $this->getStore($storeId);
                if (!$withCodes) {
                    $websites[] = $store->getWebsiteId();
                } else {
                    $websites[] = $this->getWebsite($store->getWebsiteId())->getCode();
                }
            }
        } elseif ($this->getScope() == self::SCOPE_WEBSITE) {
            $websitesArray = explode(",", $this->getRole()->getWebsiteId());
            foreach ($websitesArray as $web) {
                if (!$withCodes) {
                    $websites[] = $this->getWebsite($web)->getId();
                } else {
                    $websites[] = $this->getWebsite($web)->getCode();
                }
            }
        }
        return $websites;
    }

    /**
     * Get allowed group Ids
     * If scope disabled, then empty array of values.
     *
     * @return array
     */
    public function getAllowedStoreIds()
    {
        $elements = [];
        if ($this->getScope() == self::SCOPE_STORE) {
            $elements = $this->getTree(self::ADVANCED_STOREVIEWS, false);
        } elseif ($this->getScope() == self::SCOPE_WEBSITE) {
            $websites = explode(",", $this->getRole()->getWebsiteId());
            foreach ($this->getStoresFromWebsite($websites) as $website) {
                $elements[] = $website->getId();
            }
            if (empty($elements)) {
                $elements[] = 0;
            }
        }
        return $elements;
    }

    /**
     * Get tree values of categories or stores
     *
     * @param string$type
     * @param bool|true $groupByStore
     *
     * @return array
     */
    public function getTree($type = self::ADVANCED_CATEGORIES, $groupByStore = true)
    {
        $elements = [];
        $items= [];
        if ($this->getScope() == self::SCOPE_STORE && $stores = $this->_getStores()) {
            foreach ($stores as $item) {
                if ($type == self::ADVANCED_CATEGORIES) {
                    $items = $item->getCategoryIds();
                } elseif ($type == self::ADVANCED_STOREVIEWS) {
                    $items = $item->getStoreViewIds();
                }

                if ($items) {
                    if ($groupByStore) {
                        $elements[$item->getStoreId()] = explode(",", $items);
                    } else {
                        $elements = array_unique(array_merge($elements, explode(",", $items)));
                    }
                }
            }
            if (!count($elements) && $groupByStore && $type == self::ADVANCED_CATEGORIES) {
                foreach ($stores as $item) {
                    $elements = [];
                }
            }
        }
        return $elements;
    }

    /**
     * @deprecated
     * Get Categories By Store
     *
     * @param $store
     *
     * @return array
     */
    public function getCategories($store)
    {
        return $this->getCategoriesByStore($store);
    }

    /**
     * Get Categories By Store
     *
     * @param $store
     *
     * @return array
     */
    public function getCategoriesByStore($store)
    {
        if ($this->getScope() == self::SCOPE_STORE && $stores = $this->_getStores()) {
            foreach ($stores as $item) {
                if ($item->getStoreId() == $store) {
                    return explode(',', $item->getCategoryIds());
                }
            }
        }

        return [];
    }

    /**
     * Get Role
     *
     * @return Role
     */
    public function getRole()
    {
        if (!$this->role
            && $user = $this->getUser()
        ) {
            $roleId= $user->getRole()->getId();
            /** @var Role $role */
            $this->role = ObjectManager::getInstance()->get(Role::class)->loadOriginal($roleId);
        }

        return $this->role;
    }

    /**
     * Get User from Session
     *
     * @return mixed
     */
    public function getUser()
    {
        return ObjectManager::getInstance()->get('Magento\Backend\Model\Auth\Session')->getUser();
    }
    /**
     * Get group stores of role
     *
     * @return mixed
     */
    protected function _getStores()
    {
        if (!$this->stores) {
            $roleId= $this->getRole();
            $this->stores =
                ObjectManager::getInstance()->get('Aitoc\AdvancedPermissions\Model\Stores')->getCollection()
                    ->setRoleFilter($roleId->getId());
        }

        return $this->stores;
    }

    /**
     * Get current store of role
     *
     * @param $value
     *
     * @return mixed
     */
    public function getStore($value)
    {
        return ObjectManager::getInstance()->get('Magento\Store\Model\Store')->load($value);
    }

    /**
     * Get current website of role
     *
     * @param $value
     *
     * @return mixed
     */
    public function getWebsite($value)
    {
        return ObjectManager::getInstance()->get('Magento\Store\Model\Website')->load($value);
    }

    /**
     * get allowed category ids
     *
     * @return array
     */
    public function getCategoryIds()
    {
        return $this->getTree(self::ADVANCED_CATEGORIES, false);
    }

    /**
     * Get scope from model Role
     *
     * @return int|null
     */
    public function getScope()
    {
        return $this->getRole()->getScope();
    }

    /**
     * Get collection with webistes of role
     *
     * @param $websiteId
     *
     * @return mixed
     */
    public function getStoresFromWebsite($websiteId)
    {
        return ObjectManager::getInstance()->get('Magento\Store\Model\Store')->getCollection()
            ->addWebsiteFilter($websiteId);
    }

    /**
     * @return bool
     */
    public function isHideAllStoreViews()
    {
        if ($this->isAdvancedPermissionEnabled() && $this->isAdvancedPermissionAllowed()) {
            return $this->isViewAll();
        }

        return false;
    }

    /**
     * Get value
     *
     * @return bool
     */
    public function isViewAll()
    {
        return $this->getRole()->getViewAll();
    }

    /**
     * @return array
     */
    public function getProductTabs()
    {
        $options = [
            'content' => __('Content'),
            'gallery' => __('Image and Videos'),
            'search-engine-optimization' => __('Search Engine Optimization'),
            'related' => __('Related Products, Up-Sells, and Cross-Sells'),
            'custom_options' => __('Customizable Options'),
            'websites_restrict' => __('Product in Websites'),
            'design' => __('Design'),
            'schedule-design-update' => __('Schedule Design Update'),
            'gift-options' => __('Gift Options'),
        ];

        if ($this->extensions->isModuleEnabled('Magento_GroupedProduct')) {
            $options['grouped'] = __('Grouped Products');
        }

        if ($this->extensions->isModuleEnabled('Magento_InventorySalesAdminUi')) {
            $options['salable_quantity'] = __('Product Salable Quantity');
        }

        if ($this->extensions->isModuleEnabled('Magento_Downloadable')) {
            $options['downloadable'] = __('Downloadable Information');
        }

        if ($this->extensions->isModuleEnabled('Magento_Bundle')) {
            $options['bundle-items'] = __('Bundle Items');
        }

        if ($this->extensions->isModuleEnabled('Aitoc_ProductUnitsAndQuantities')) {
            $options['product-units-and-quantities'] = __('Product Units And Quantities');
        }

        if ($this->extensions->isModuleEnabled('Aitoc_ConfigurableProducts')) {
            $options['configurable-products-pro'] = __('Configurable Products Pro');
        }

        if ($this->extensions->isModuleEnabled('Aitoc_CustomProductDesigner')) {
            $options['custom-product-designer'] = __('Custom Product Designer');
        }

        if ($this->extensions->isModuleEnabled('Aitoc_DimensionalShipping')) {
            $options['dimensional_shipping_options'] = __('Dimensional Shipping Options');
        }

        return $options;
    }

    /**
     * @return mixed
     */
    public function getAttributePermission()
    {
        $aitAttributeModel = $this->editorAttributeFactory->create();

        return $aitAttributeModel->getAttributePermissionByRole($this->getRole()->getId());
    }

    /**
     * @return mixed
     */
    public function getAttributePermissionWithName()
    {
        $aitAttributeModel = $this->editorAttributeFactory->create();
        $attrArray = $aitAttributeModel->getAttributePermissionByRole($this->getRole()->getId());
        $result = [];

        if ($attrArray) {
            foreach ($attrArray as $key => $value) {
                $attribute = $this->repository->get($key);
                if ($attribute->getId()) {
                    $result[] = [
                        'code' => $attribute->getAttributeCode(),
                        'allow' => $value,
                    ];
                }
            }
        }

        return $result;
    }

    /**
     * @param $element
     */
    public function disableElement($element)
    {
        $element->setDisabled(true);
        $element->setReadonly(true);

        $afterHtml = $element->getAfterElementHtml();

        if (false !== strpos($afterHtml, 'type="checkbox"')) {
            $afterHtml = str_replace('type="checkbox"', 'type="checkbox" disabled="disabled"', $afterHtml);
            $element->setAfterElementHtml($afterHtml);
        }
    }

    /**
     * Get allowed root categories by store
     *
     * @return array
     */
    public function getAllowedRootCategories($store)
    {
        $allowedRootIds = [];
        foreach ($this->getAllowedStoreIds() as $id) {
            // collect all stores OR allowed by $store
            if (!$store || $store == $id) {
                $group_id = $this->getStore($id)->getGroup()->getId();
                $allowedRootIds[$this->getStore($id)->getRootCategoryId()][] = $group_id;
            }
        }
        return $allowedRootIds;
    }
}
