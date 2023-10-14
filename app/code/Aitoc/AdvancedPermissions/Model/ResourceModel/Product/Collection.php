<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Product;

class Collection extends \Magento\Reports\Model\ResourceModel\Product\Collection
{
    /**
     * Add store availability filter. Include availability product
     * for stores website
     *
     * @param null $storeIds
     *
     * @return $this
     */

    public function addStoresFilter($storeIds = null)
    {
        if ($storeIds === null) {
            $storeIds = $this->getStoreId();
        }
        if ($storeIds) {
            $this->setStoreId(array_pop($storeIds));
            $this->_productLimitationFilters['store_id'] = $storeIds;
            $this->_prepareProductLimitationFilters();
            $this->_productLimitationJoinWebsiteWithStores();
            $this->_productLimitationJoinPrice();
            $limitFilter = $this->_productLimitationFilters;
            $connection  = $this->getConnection();

            if (!isset($limitFilter['category_id']) && !isset($limitFilter['visibility'])) {
                return $this;
            }

            $limitConditions = [
                'cat_index.product_id=e.entity_id',
                $connection->quoteInto('cat_index.store_id IN(?)', $limitFilter['store_id']),
            ];
            if (isset($limitFilter['visibility']) && !isset($limitFilter['store_table'])) {
                $limitConditions[] = $connection->quoteInto('cat_index.visibility IN(?)', $limitFilter['visibility']);
            }
            $limitConditions[] = $connection->quoteInto('cat_index.category_id=?', $limitFilter['category_id']);
            if (isset($limitFilter['category_is_anchor'])) {
                $limitConditions[] =
                    $connection->quoteInto('cat_index.is_parent=?', $limitFilter['category_is_anchor']);
            }

            $joinCond = join(' AND ', $limitConditions);
            $select   = $this->getSelect();
            $fromPart = $select->getPart(\Magento\Framework\DB\Select::FROM);
            if (isset($fromPart['cat_index'])) {
                $fromPart['cat_index']['joinCondition'] = $joinCond;
                $select->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
            } else {
                $select->join(
                    ['cat_index' => $this->getTable('catalog_category_product_index')],
                    $joinCond,
                    ['cat_index_position' => 'position']
                );
            }

            $this->_productLimitationJoinStore();
        }

        return $this;
    }

    /**
     * Join website product limitation
     *
     * @return $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _productLimitationJoinWebsiteWithStores()
    {
        $joinWebsite = false;
        $limitFilter = $this->_productLimitationFilters;
        $conditions  = ['product_website.product_id = e.entity_id'];
        $connection  = $this->getConnection();
        $select      = $this->getSelect();
        if (isset($limitFilter['website_ids'])) {
            $joinWebsite = true;
            if (count($limitFilter['website_ids']) > 1) {
                $select->distinct(true);
            }
            $conditions[] =
                $connection->quoteInto('product_website.website_id IN(?)', $limitFilter['website_ids']);
        } elseif (isset($limitFilter['store_id'])
            && (!isset($limitFilter['visibility'])
                && !isset($limitFilter['category_id']))
            && !$this->isEnabledFlat()
        ) {
            $joinWebsite = true;
            $websiteIds  = [];
            foreach ($limitFilter['store_id'] as $store) {
                $websiteIds[] = $this->_storeManager->getStore($store)->getWebsiteId();
            }
            $conditions[] = $connection->quoteInto('product_website.website_id IN(?)', $websiteIds);
        }

        $fromPart = $select->getPart(\Magento\Framework\DB\Select::FROM);
        if (isset($fromPart['product_website'])) {
            if (!$joinWebsite) {
                unset($fromPart['product_website']);
            } else {
                $fromPart['product_website']['joinCondition'] = join(' AND ', $conditions);
            }
            $select->setPart(\Magento\Framework\DB\Select::FROM, $fromPart);
        } elseif ($joinWebsite) {
            $select->join(
                ['product_website' => $this->getTable('catalog_product_website')],
                join(' AND ', $conditions),
                []
            );
        }

        return $this;
    }
}
