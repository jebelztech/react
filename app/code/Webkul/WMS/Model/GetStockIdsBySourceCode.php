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

namespace Webkul\WMS\Model;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Helper Data Class
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class GetStockIdsBySourceCode extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var GetProductSalableQtyInterface
     */
    private $sourceCollectionF;

    /**
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param StockRepositoryInterface $stockRepository
     * @param GetAssignedStockIdsBySku $getAssignedStockIdsBySku
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     */
    public function __construct(
        \Magento\Inventory\Model\ResourceModel\Source\CollectionFactory $sourceCollectionF
    ) {
        $this->sourceCollectionF = $sourceCollectionF;
    }

    /**
     * @param string $sourceCode
     * @return array
     */
    public function execute(string $sourceCode): array
    {
        $stockIds = [];
        $sourceCollection = $this->sourceCollectionF->create()
            ->addFieldToFilter('main_table.source_code', $sourceCode)
            ->addFieldToFilter('main_table.enabled', 1);
        $sourceStockLinkTable = $sourceCollection->getTable('inventory_source_stock_link');
        $sourceCollection->join(
            ['issl' => $sourceStockLinkTable],
            'issl.source_code=main_table.source_code'
        );
        foreach ($sourceCollection as $source) {
            $stockIds[] = $source->getStockId();
        }
        return $stockIds;
    }
}
