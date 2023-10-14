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

use Magento\Framework\Controller\ResultFactory;

/**
 * Check csv to upload
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Checkcsv extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Checks csv
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $files = $this->getRequest()->getFiles();
        $returnData = [
            "success" => 1,
            "message" => ""
        ];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $invalidRows = [];
        if (isset($files["file"]["tmp_name"])) {
            $tmpName = $files["file"]['tmp_name'];
            $mimeType = $files["file"]['type'];
            $logger = \Magento\Framework\App\ObjectManager::getInstance()->get(\Psr\Log\LoggerInterface::class);
            $logger->info(json_encode($mimeType, JSON_PRETTY_PRINT));
            if (false === in_array($mimeType, [ 'text/csv', 'application/vnd.ms-excel'])) {
                $returnData["success"] = 0;
                $returnData["message"] = __(
                    "Please unload a csv file."
                );
                $resultJson->setData($returnData);
                return $resultJson;
            }
            $csvData = $this->csv->getData($files["file"]["tmp_name"]);
            foreach ($csvData as $row => $data) {
                if ($row == 0) {
                    if ((!isset($data[0])
                        || $data[0] != "id")
                        || (!isset($data[1])
                        || $data[1] != "row")
                        || (!isset($data[2])
                        || $data[2] != "column")
                        || (!isset($data[3])
                        || $data[3] != "shelf")
                        || (!isset($data[4])
                        || $data[4] != "rack")
                        || (!isset($data[5])
                        || $data[5] != "warehouse_id")
                        || (!isset($data[6])
                        || $data[6] != "product_id")
                    ) {
                        $returnData["data"] = $data;
                        $returnData["success"] = 0;
                        $returnData["message"] = __(
                            "The provided csv is not in correct format."
                        );
                        $resultJson->setData($returnData);
                        return $resultJson;
                    }
                } else {
                    if ((!isset($data[0])
                        || !is_numeric($data[0]))
                        || (!isset($data[1])
                        || !is_numeric($data[1]))
                        || (!isset($data[2])
                        || !is_numeric($data[2]))
                        || (!isset($data[3])
                        || !ctype_alpha($data[3]))
                        || (!isset($data[4])
                        || !is_numeric($data[4]))
                        || (!isset($data[5])
                        || !is_numeric($data[5]))
                        || (!isset($data[6])
                        || !is_numeric($data[6]))
                    ) {
                        $invalidRows[] = $row;
                    }
                }
            }
            if (!empty($invalidRows)) {
                $returnData["success"] = 0;
                $returnData["message"] = __(
                    "Invalid row count: %1, Total rows: %2, Invalid rows are: %3",
                    count($invalidRows),
                    count($csvData),
                    implode(",", $invalidRows)
                );
            }
        } else {
            $returnData["success"] = 0;
            $returnData["message"] = __(
                "File not found"
            );
        }
        $resultJson->setData($returnData);
        return $resultJson;
    }
}
