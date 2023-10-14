<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Analytics\Collector;

use Amasty\Mostviewed\Api\AnalyticRepositoryInterface;
use Amasty\Mostviewed\Model\Analytics\Analytic;
use Amasty\Mostviewed\Model\Analytics\Collector\Utils\GetActionSelect;
use Amasty\Mostviewed\Model\Analytics\Collector\Utils\GetAnalyticsItems;
use Amasty\Mostviewed\Model\Analytics\Collector\Utils\GetGroupIds;
use Magento\Framework\App\ResourceConnection;

class ViewCollector implements CollectorInterface
{
    const ACTION_TYPE = 'view';

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var AnalyticRepositoryInterface
     */
    private $analyticRepository;

    /**
     * @var GetActionSelect
     */
    private $getActionSelect;

    /**
     * @var GetGroupIds
     */
    private $getGroupIds;

    /**
     * @var GetAnalyticsItems
     */
    private $getAnalyticsItems;

    public function __construct(
        AnalyticRepositoryInterface $analyticRepository,
        ResourceConnection $resourceConnection,
        GetActionSelect $getActionSelect,
        GetGroupIds $getGroupIds,
        GetAnalyticsItems $getAnalyticsItems
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->analyticRepository = $analyticRepository;
        $this->getActionSelect = $getActionSelect;
        $this->getGroupIds = $getGroupIds;
        $this->getAnalyticsItems = $getAnalyticsItems;
    }

    /**
     * Collect view analytics actions
     */
    public function execute(): void
    {
        $connection = $this->resourceConnection->getConnection();
        $actionSelect = $this->getActionSelect->execute(self::ACTION_TYPE);

        $analyticsItems = $this->getAnalyticsItems->execute(self::ACTION_TYPE);
        foreach ($this->getGroupIds->execute() as $groupId) {
            /** @var Analytic $item */
            $item = $analyticsItems[$groupId] ?? $this->analyticRepository->getNew();

            $actionSelect
                ->where('id > ?', $item->getVersionId())
                ->having('block_id = ?', $groupId);
            if ($statistics = $connection->fetchRow($actionSelect)) {
                $item
                    ->setBlockId($groupId)
                    ->setCounter($item->getCounter() + $statistics['counter'])
                    ->setType(self::ACTION_TYPE)
                    ->setVersionId($statistics['version_id']);
                $this->analyticRepository->save($item);
            }
            $actionSelect->reset(\Zend_Db_Select::WHERE);
            $actionSelect->reset(\Zend_Db_Select::HAVING);
        }
    }
}
