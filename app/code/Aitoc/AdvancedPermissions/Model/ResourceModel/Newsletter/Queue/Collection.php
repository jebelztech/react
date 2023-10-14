<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Newsletter\Queue;

use Aitoc\AdvancedPermissions\Helper\Data as ApHelper;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Magento\Newsletter\Model\ResourceModel\Queue\Collection as CoreQueueCollection;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 */
class Collection extends CoreQueueCollection
{
    protected $apHelper;

    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        DateTime $date,
        ApHelper $apHelper,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $date, $connection, $resource);

        $this->apHelper = $apHelper;
    }

    public function getSelectCountSql()
    {
        if (!$this->apHelper->isAdvancedPermissionEnabled()) {
            return parent::getSelectCountSql();
        }

        $allowedStoreIds = $this->apHelper->getAllowedStoreViewIds();
        $this->addStoreFilter($allowedStoreIds);

        return parent::getSelectCountSql();
    }

    /**
     * Adds subscribers info to selelect
     *
     * @return $this
     */
    protected function _addSubscriberInfoToSelect()
    {
        if (!$this->apHelper->isAdvancedPermissionEnabled()) {
            return parent::_addSubscriberInfoToSelect();
        }

        $allowedStoreIds = $this->apHelper->getAllowedStoreViewIds();

        /** @var $select \Magento\Framework\DB\Select */
        $select = $this->getConnection()->select()->from(
            ['qlt' => $this->getTable('newsletter_queue_link')],
            'COUNT(qlt.queue_link_id)'
        )->where(
            'qlt.queue_id = main_table.queue_id'
        );

        $this->joinSubscribers($select, $allowedStoreIds, 'qlt');

        $totalExpr = new \Zend_Db_Expr(sprintf('(%s)', $select->assemble()));

        $select = $this->getConnection()->select()->from(
            ['qls' => $this->getTable('newsletter_queue_link')],
            'COUNT(qls.queue_link_id)'
        )->where(
            'qls.queue_id = main_table.queue_id'
        )->where(
            'qls.letter_sent_at IS NOT NULL'
        );

        $this->joinSubscribers($select, $allowedStoreIds, 'qls');

        $sentExpr = new \Zend_Db_Expr(sprintf('(%s)', $select->assemble()));

        $this->getSelect()->columns(['subscribers_sent' => $sentExpr, 'subscribers_total' => $totalExpr]);

        return $this;
    }

    /**
     * Before load action
     *
     * @return $this
     */
    protected function _beforeLoad()
    {
        if (!$this->apHelper->isAdvancedPermissionEnabled()) {
            return parent::_beforeLoad();
        }

        $allowedStoreIds = $this->apHelper->getAllowedStoreViewIds();
        $this->addStoreFilter($allowedStoreIds);

        return parent::_beforeLoad();
    }

    /**
     * @param Select $select
     * @param int[] $allowedStoreIds
     */
    protected function joinSubscribers(Select $select, $allowedStoreIds, $mainTablename)
    {
        $select->joinInner(
            ['subs' => $this->getTable('newsletter_subscriber')],
            $mainTablename . '.subscriber_id = subs.subscriber_id',
            []
        )->where('subs.store_id in (?)', $allowedStoreIds);
    }
}
