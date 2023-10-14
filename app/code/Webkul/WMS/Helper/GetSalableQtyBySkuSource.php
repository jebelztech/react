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

namespace Webkul\WMS\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\InventorySalesAdminUi\Model\ResourceModel\GetAssignedStockIdsBySku;
use Magento\InventoryApi\Api\StockRepositoryInterface;
use Magento\InventorySalesApi\Api\GetProductSalableQtyInterface;
use Magento\InventoryConfigurationApi\Api\GetStockItemConfigurationInterface;

/**
 * Helper Data Class
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class GetSalableQtyBySkuSource extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var GetProductSalableQtyInterface
     */
    private $getProductSalableQty;

    /**
     * @var StockRepositoryInterface
     */
    private $stockRepository;

    /**
     * @var GetAssignedStockIdsBySku
     */
    private $getAssignedStockIdsBySku;

    /**
     * @var GetStockItemConfigurationInterface
     */
    private $getStockItemConfiguration;

    /**
     * @param GetProductSalableQtyInterface $getProductSalableQty
     * @param StockRepositoryInterface $stockRepository
     * @param GetAssignedStockIdsBySku $getAssignedStockIdsBySku
     * @param GetStockItemConfigurationInterface $getStockItemConfiguration
     */
    public function __construct(
        GetProductSalableQtyInterface $getProductSalableQty,
        StockRepositoryInterface $stockRepository,
        GetAssignedStockIdsBySku $getAssignedStockIdsBySku,
        GetStockItemConfigurationInterface $getStockItemConfiguration,
        \Webkul\WMS\Model\GetStockIdsBySourceCode $getStockIdsBySourceCode
    ) {
        $this->getProductSalableQty = $getProductSalableQty;
        $this->stockRepository = $stockRepository;
        $this->getAssignedStockIdsBySku = $getAssignedStockIdsBySku;
        $this->getStockItemConfiguration = $getStockItemConfiguration;
        $this->getStockIdsBySourceCode = $getStockIdsBySourceCode;
    }

    /**
     * @param string $sku
     * @return array
     */
    public function execute(string $sku, string $sourceCode)
    {
        $stockInfo = [];
        $sourceStockIds = $this->getStockIdsBySourceCode->execute($sourceCode);
        $productAssignedStockIds = $this->getAssignedStockIdsBySku->execute($sku);
        $availableStockIds = array_intersect($productAssignedStockIds, $sourceStockIds);
        $totalSalableQty = 0;
        if (count($availableStockIds)) {
            foreach ($availableStockIds as $stockId) {
                $stockId = (int)$stockId;
                $stock = $this->stockRepository->get($stockId);
                $stockItemConfiguration = $this->getStockItemConfiguration->execute($sku, $stockId);
                $isManageStock = $stockItemConfiguration->isManageStock();

                $stockInfoItem = [
                    'stock_name' => $stock->getName(),
                    'qty' => $isManageStock ? $this->getProductSalableQty->execute($sku, $stockId) : null,
                    'manage_stock' => $isManageStock,
                ];
                $totalSalableQty += $stockInfoItem['qty'];
                $stockInfo[] = $stockInfoItem;
            }
        }
        return $totalSalableQty;
    }
}
