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

namespace Webkul\WMS\Controller\Adminhtml\Warehouse;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * DownloadSample Class to have sample for assignment
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class DownloadSample extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Warehouse list
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $warehouseId = $this->getRequest()->getParam("id");
        $locationArray = $this->locationCollection->create()
            ->addFieldToFilter("warehouse_id", $warehouseId)
            ->toArray();
        $locationArray = $locationArray["items"];
        $header = [
            "id",
            "row",
            "column",
            "shelf",
            "rack",
            "warehouse_id",
            "product_id"
        ];
        $filepath = "export/custom/warehouse_location_details.csv";
        $this->directory->create("export");
        $stream = $this->directory->openFile($filepath, "w+");
        $stream->lock();
        $stream->writeCsv($header);
        foreach ($locationArray as $each) {
            $stream->writeCsv($each);
        }
        $content = [];
        $content["type"] = "filename";
        $content["value"] = $filepath;
        $content["rm"] = "1";
        $csvfilename = "warehouse_location_details.csv";
        return $this->fileFactory->create($csvfilename, $content, DirectoryList::VAR_DIR);
    }
}
