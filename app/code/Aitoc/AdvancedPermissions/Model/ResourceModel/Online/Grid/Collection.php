<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Model\ResourceModel\Online\Grid;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Customer\Model\Visitor;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Psr\Log\LoggerInterface;

class Collection extends \Magento\Customer\Model\ResourceModel\Online\Grid\Collection
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Collection constructor.
     *
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @param Visitor $visitorModel
     * @param DateTime $date
     * @param Data $helper
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        $mainTable,
        $resourceModel,
        Visitor $visitorModel,
        DateTime $date,
        Data $helper
    ) {
        $this->helper = $helper;
        
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel,
            $visitorModel,
            $date
        );
    }

    /**
     * Init collection select
     *
     * @return $this
     */
    protected function _initSelect()
    {
        \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult::_initSelect();
        $connection = $this->getConnection();
        $lastDate   = $this->date->gmtTimestamp() - $this->visitorModel->getOnlineInterval() * self::SECONDS_IN_MINUTE;
        $this->getSelect()->joinLeft(
            ['customer' => $this->getTable('customer_entity')],
            'customer.entity_id = main_table.customer_id',
            ['email', 'firstname', 'lastname']
        )->where(
            'main_table.last_visit_at >= ?',
            $connection->formatDate($lastDate)
        );
        
        if ($this->helper->isAdvancedPermissionEnabled()) {
            if (!$this->helper->getRole()->getShowAllCustomers()) {
                $this->getSelect()->where('customer.website_id IN (?)', $this->helper->getAllowedWebsiteIds());
            }
        }

        $expression = $connection->getCheckSql(
            'main_table.customer_id IS NOT NULL AND main_table.customer_id != 0',
            $connection->quote(Visitor::VISITOR_TYPE_CUSTOMER),
            $connection->quote(Visitor::VISITOR_TYPE_VISITOR)
        );
        $this->getSelect()->columns(['visitor_type' => $expression]);

        return $this;
    }
}
