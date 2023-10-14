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

namespace Webkul\WMS\Model\ResourceModel\Tote\Grid;

use Magento\Framework\Api\Search\SearchResultInterface;
use Webkul\WMS\Model\ResourceModel\Tote\Collection as ToteCollection;

/**
 * Class Collection tote
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Collection extends ToteCollection implements SearchResultInterface
{

    protected $aggregations;
    protected $objectManager;

    /**
     * Costructor
     *
     * @param \Magento\Framework\ObjectManagerInterface                         $objectManager objectManager
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface         $entityFactory entityFactory
     * @param \Psr\Log\LoggerInterface                                          $logger        logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface      $fetchStrategy fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface                         $eventManager  eventManager
     * @param \Magento\Store\Model\StoreManagerInterface                        $storeManager  storeManager
     * @param mixed                                                             $mainTable     mainTable
     * @param mixed                                                             $eventPrefix   eventPrefix
     * @param mixed                                                             $eventObject   eventObject
     * @param mixed                                                             $resourceModel resourceModel
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\Document $model         model
     * @param null                                                              $connection    null
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb              $resource      null
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->objectManager = $objectManager;
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
        $this->addAssignedStatusColumn();
    }

    /**
     * Function getAggregations
     *
     * @return mixed
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * Function setAggregations
     *
     * @param mixed $aggregations aggregations
     *
     * @return null
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * Function getAllIds
     *
     * @param integer $limit  limit
     * @param integer $offset offset
     *
     * @return mixed
     */
    public function getAllIds($limit = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
    }

    /**
     * Function getSearchCriteria
     *
     * @return null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Function setSearchCriteria
     *
     * @param SearchCriteriaInterface $searchCriteria searchCriteria
     *
     * @return mixed
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Function getTotalCount
     *
     * @return integer
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Function setTotalCount
     *
     * @param integer $totalCount totalCount
     *
     * @return mixed
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Function setItems
     *
     * @param array $items items
     *
     * @return mixed
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    public function addAssignedStatusColumn()
    {
        $select = $this->getSelect();
        $assigned = __("Assigned");
        $notAssigned = __("Not Assigned");
        $orderTotesTable = $this->getTable('wms_order_totes');
        $select->joinLeft(
            ['ott' => $orderTotesTable],
            "ott.assigned_tote_id = main_table.id",
            [
                'tote_status' => "IF(ott.assigned_tote_id > 0,'$assigned', '$notAssigned')"
            ]
        );
        $sql = $select->__toString();
        $select->reset();
        $select->from(['main_table' => new \Zend_Db_Expr("($sql)")]);
    }
}
