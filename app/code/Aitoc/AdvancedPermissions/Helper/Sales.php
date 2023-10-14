<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Helper;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResource;
use Magento\Sales\Model\ResourceModel\Order\Invoice\Collection as InvoiceCollection;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;
use Zend_Db_Select_Exception;

/**
 *
 */
class Sales extends Data
{
    /**
     * alias for sales_shipment_item, sales_invoice_item tables
     *
     * @var string
     */
    protected $itemAlias = 'entity_item';

    /**
     * Is current model instace is Order, Invoice or Shipment
     *
     * @param AbstractResource $resourceModel
     *
     * @return bool
     */
    public function isSalesResource($resourceModel)
    {
        return $resourceModel instanceof OrderResource
        || $resourceModel instanceof InvoiceResource
        || $resourceModel instanceof ShipmentResource;
    }


    /**
     * Add filter to Order, Invoice and Shipment collections,
     * sub-admin should see entity with allowed products only
     *
     * @param AbstractCollection $collection
     * @return AbstractCollection
     * @throws Zend_Db_Select_Exception
     */
    public function addAllowedProductFilterIfRequired(AbstractCollection $collection)
    {
        /**
         * With method getResource we can process collection like sales_invoice and sales_invoice_grid,
         * because he have same resource model but different collection model Same with shipment
         */
        if (!$this->isSalesResource($collection->getResource())
            || array_key_exists($this->itemAlias, $collection->getSelect()->getPart(Select::FROM))
        ) {
            return $collection;
        }
        /** @var Select $select */
        $select = $collection->getSelect();

        //used on reorder process to get order addresses
        if ($this->isInvoiceCollectionReceivedByOrderId($collection, $select)) {
            return $collection;
        }

        if (!$this->isAdvancedPermissionEnabled()) {
            return $collection;
        }

        return $this->addAllowedProductFilter($collection, $select);
    }

    /**
     * @param AbstractCollection $collection
     * @param Select $select
     * @return bool
     * @throws Zend_Db_Select_Exception
     */
    private function isInvoiceCollectionReceivedByOrderId(AbstractCollection $collection, Select $select)
    {
        return ($collection instanceof InvoiceCollection) && ($this->selectContainsOrderIdInFirstWherePart($select));
    }

    /**
     * @param Select $select
     * @return bool
     * @throws Zend_Db_Select_Exception
     */
    private function selectContainsOrderIdInFirstWherePart(Select $select)
    {
        $where  = $select->getPart(Select::WHERE);

        if (!$where) {
            return false;
        }

        $firstWhere = current($where);

        return strpos($firstWhere, 'order_id') !== false;
    }

    /**
     * @param AbstractCollection $collection
     * @param Select $select
     * @return AbstractCollection
     * @throws Zend_Db_Select_Exception
     */
    public function addAllowedProductFilter(AbstractCollection $collection, Select $select)
    {
        $allowedStoreIds = $this->getAllowedStoreIds();

        if (count($allowedStoreIds)) {
            $select->where('main_table.store_id IN (?)', $allowedStoreIds);
        }

        $select->distinct(true);

        return $collection;
    }

    /**
     * @param AbstractCollection $collection
     * @param $select
     * @return AbstractCollection
     */
    public function addAllowedProductFilterForOrderArchiveCollection(AbstractCollection $collection, Select $select)
    {
        $allowedStoreIds = $this->getAllowedStoreIds();

        if (count($allowedStoreIds)) {
            $select->where('order_grid_table.store_id IN (?)', $allowedStoreIds);
        }

        $select->distinct(true);

        return $collection;
    }
}
