<?php
/**
 * Webkul Software.
 *
 *
 * @category  Webkul
 * @package   Webkul_DeliveryBoy
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\DeliveryBoy\Model\ResourceModel\Order\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Webkul\DeliveryBoy\Model\ResourceModel\Order\Collection as OrderCollection;

class Collection extends OrderCollection implements SearchResultInterface
{
    /**
     * @var \Magento\Framework\Api\Search\AggregationInterface
     */
    protected $_aggregations;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string $mainTable
     * @param string $eventPrefix
     * @param string $eventObject
     * @param string $resourceModel
     * @param string $model
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        string $mainTable,
        string $eventPrefix,
        string $eventObject,
        string $resourceModel,
        string $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storeManager,
            $connection,
            $resource
        );
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
        $select = $this->getSelect();
        $this->applyCustomFilters($select);
    }

    /**
     * @return \Magento\Framework\Api\Search\AggregationInterface
     */
    public function getAggregations()
    {
        return $this->_aggregations;
    }

    /**
     * @param \Magento\Framework\Api\Search\AggregationInterface $aggregations
     * @return self
     */
    public function setAggregations($aggregations)
    {
        $this->_aggregations = $aggregations;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return self
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @param int $totalCount
     * @return self
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @param array $items
     * @return self
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    public function applyCustomFilters($select)
    {
        $this->joinOrderTable($select);
        $this->leftJoinQuoteTable($select);
        $this->leftJoinDeliveryboyTable($select);
        $this->leftJoinOrderTransactionTable($select);
        $sql = $select->assemble();
        $select->reset();
        $select->from(
            ["main_table" => new \Zend_Db_Expr("($sql)")],
            new \Zend_Db_Expr("*")
        );
    }

    public function leftJoinQuoteTable($select)
    {
        $quoteTable = $this->getTable('quote');
        $select->joinLeft(
            $quoteTable . ' as quoteTable',
            'orderTable.quote_id = quoteTable.entity_id',
            [
                'firstname' => 'IFNULL(quoteTable.customer_firstname, "GUEST")',
                'lastname' => 'quoteTable.customer_lastname'
            ]
        );
        return $select;
    }

    public function leftJoinDeliveryboyTable($select)
    {
        $select->joinLeft(
            $this->getTable('deliveryboy_deliveryboy') . ' as deliveryboyTable',
            'main_table.deliveryboy_id = deliveryboyTable.id',
            ['deliveryboy_name' => 'deliveryboyTable.name']
        );
        $this->_eventManager->dispatch(
            'wk_deliveryboy_assigned_order_collection_apply_filter_event',
            [
                'deliveryboy_order_collection' => $this,
                'collection_table_name' => 'main_table',
                'owner_id' => 0,
            ]
        );
        return $select;
    }
}
