<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Model\Analytics\Collector\Utils;

use Amasty\Mostviewed\Api\Data\GroupInterface;
use Magento\Framework\App\ResourceConnection;

class GetGroupIds
{
    const TABLE_NAME = 'amasty_mostviewed_group';
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    public function __construct(ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Retrieve analytics group ids
     *
     * @return array
     */
    public function execute(): array
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()->from(
            $this->resourceConnection->getTableName(self::TABLE_NAME),
            GroupInterface::GROUP_ID
        );

        return $connection->fetchCol($select);
    }
}
