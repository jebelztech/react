<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Plugin\Catalog\Block\Adminhtml\Category;

use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\Permissions;
use Magento\Backend\Model\Auth\Session;
use Magento\Catalog\Block\Adminhtml\Category\Tree as MagentoTree;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Framework\DB\Helper;
use Magento\Framework\Json\EncoderInterface;
use Magento\Store\Model\Store;
use Zend_Json;

class Tree
{
    /**
     * @var Session
     */
    protected $auth;

    /**
     * @var Data
     */
    private $helper;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Helper
     */
    protected $resourceHelper;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var Permissions
     */
    protected $permissions;

    protected $block;

    protected $categoriesDissalow;

    /**
     * @param Data             $helper
     * @param CategoryFactory  $categoryFactory
     * @param Helper           $resourceHelper
     * @param EncoderInterface $jsonEncoder
     * @param Session          $auth
     * @param Permissions      $permissions
     */
    public function __construct(
        Data $helper,
        CategoryFactory $categoryFactory,
        Helper $resourceHelper,
        EncoderInterface $jsonEncoder,
        Session $auth,
        Permissions $permissions
    ) {
        $this->helper = $helper;
        $this->categoryFactory = $categoryFactory;
        $this->resourceHelper = $resourceHelper;
        $this->jsonEncoder = $jsonEncoder;
        $this->auth = $auth;
        $this->permissions = $permissions;
    }

    /**
     * @param MagentoTree $block
     * @param             $json
     *
     * @return string
     * @throws \Zend_Json_Exception
     */
    public function afterGetSuggestedCategoriesJson(MagentoTree $block, $json)
    {
        $categories = $this->getJson($json);
        $categoryIdsDissalow = [];
        $tree = $this->helper->getTree();
        foreach ($tree as $value) {
            if (count($categoryIdsDissalow) == 0) {
                $categoryIdsDissalow = $value;
            } else {
                $categoryIdsDissalow = array_merge($categoryIdsDissalow, array_diff($value, $categoryIdsDissalow));
            }
        }
        if (count($categoryIdsDissalow) > 0) {
            $this->categoriesDissalow = $this->_getFullCategories($categoryIdsDissalow);
            $json = $this->jsonEncoder->encode($this->getRecursive($categories));
        }

        return $json;
    }

    /**
     * @param $categories
     *
     * @return array
     */
    public function getRecursive($categories)
    {
        $newCategories = [];
        $count = 0;
        foreach ($categories as $key => $value) {
            if (isset($value['id']) && in_array($value['id'], $this->categoriesDissalow)) {
                $newCategories[$count]['id'] = $value['id'];
                if (isset($value['is_active'])) {
                    $newCategories[$count]['is_active'] = $value['is_active'];
                }
                if (isset($value['label'])) {
                    $newCategories[$count]['label'] = $value['label'];
                }
                if (isset($value['children'])) {
                    $newCategories[$count]['children'] = $this->getRecursive($value['children']);
                } else {
                    $newCategories[$count]['children'] = [];
                }
                $count++;
            }
        }

        return $newCategories;
    }

    /**
     * @param MagentoTree $block
     * @param             $json
     *
     * @return string
     */
    public function afterGetTreeJson(MagentoTree $block, $json)
    {
        if (!$this->helper->getScope()) {
            return $json;
        }

        $nodes = $this->permissions->getAllowedCategoriesTree(
            $this->getJson($json),
            $block->getStore()->getGroup()->getId()
        );

        return $this->jsonEncoder->encode($nodes);
    }

    /**
     * Get decode json
     *
     * @param $json
     *
     * @return mixed
     * @throws \Zend_Json_Exception
     */
    public function getJson($json)
    {
        return Zend_Json::decode($json);
    }

    /**
     * @param $elements
     *
     * @return array
     */
    protected function _getFullCategories($elements)
    {
        $categories = [];
        foreach ($elements as $element) {
            $category = $this->categoryFactory->create()->load($element);
            $ids = $category->getParentIds();
            if (!count($categories)) {
                $categories = $ids;
            } else {
                $categories = array_merge($categories, array_diff($ids, $categories));
            }
            $children = $category->getChildren();
            $childs = explode(",", $children);
            $categories = array_merge($categories, array_diff($childs, $categories));
        }
        if (count($categories)) {
            $elements = array_merge($elements, array_diff($categories, $elements));
        }
        foreach ($elements as $key => $value) {
            if (!$value) {
                unset($elements[$key]);
            }
        }

        return $elements;
    }

    /**
     * Check availability of adding root category
     *
     * @return boolean
     */
    public function afterCanAddRootCategory(MagentoTree $block, $return)
    {
        if ($block->getStore()->getId() == Store::DEFAULT_STORE_ID && $this->helper->getScope()) {
            return false;
        }

        return $return;
    }
}
