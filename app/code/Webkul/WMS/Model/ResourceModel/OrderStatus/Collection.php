<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\WMS\Model\ResourceModel\OrderStatus;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class Collection order status
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Collection extends AbstractCollection
{
    protected $_idFieldName = "id";

    /**
     * Costructor
     *
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface    $entityFactory
     * @param \Psr\Log\LoggerInterface                                     $loggerInterface
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyInterface
     * @param \Magento\Framework\Event\ManagerInterface                    $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManagerInterface
     * @param \Magento\Framework\DB\Adapter\AdapterInterface               $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb         $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $loggerInterface,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategyInterface,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManagerInterface,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $loggerInterface,
            $fetchStrategyInterface,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * Contructor of class Collection
     *
     * @return null
     */
    protected function _construct()
    {
        $this->_init(
            \Webkul\WMS\Model\OrderStatus::class,
            \Webkul\WMS\Model\ResourceModel\OrderStatus::class
        );
        $this->_map["fields"]["id"] = "main_table.id";
    }

    /**
     * Filter orderStatus by store
     *
     * @param mixed   $store     store
     * @param boolean $withAdmin withAdmin
     *
     * @return OrderStatus
     */
    public function addStoreFilter($store, $withAdmin = true)
    {
        if (!$this->getFlag("store_filter_added")) {
            $this->performAddStoreFilter($store, $withAdmin);
        }
        return $this;
    }

    /**
     * Set OrderStatus data
     *
     * @param mixed $condition     condition
     * @param mixed $attributeData attributeData
     *
     * @return boolean
     */
    public function setOrderStatusData($condition, $attributeData)
    {
        return $this->getConnection()->update(
            $this->getTable("wms_order_status"),
            $attributeData,
            $where = $condition
        );
    }
}
