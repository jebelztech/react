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
 * Importcsv Class to update the locations
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Importcsv extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * Checks csv
     *
     * @return \Magento\Framework\Controller\Result\RedirectFactory
     */
    public function execute()
    {
        $files = $this->getRequest()->getFiles();
        $wholeData = $this->getRequest()->getParams();
        $returnData = [
            "success" => 1,
            "message" => ""
        ];
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $target = $this->directory->getAbsolutePath(
            "warehousecsv/".$wholeData["id"]."/"
        );
        $this->directory->delete($target);
        $this->ioFile->mkdir($target, 0755);
        $newFileName = "File-".time().".csv";
        $uploader = $this->fileUploader->create(
            [
                "fileId" => "file"
            ]
        );
        $uploader->setAllowedExtensions(
            [
                "csv"
            ]
        );
        $uploader->setAllowRenameFiles(true);
        $result = $uploader->save($target, $newFileName);
        if ($result["error"] == 0) {
            $csvData = $this->csv->getData($target.$newFileName);
            foreach ($csvData as $row => $data) {
                if ($row > 0) {
                    $this->productLocationFactory
                        ->create()
                        ->load($data[0])
                        ->setRow($data[1])
                        ->setColumn($data[2])
                        ->setShelf($data[3])
                        ->setRack($data[4])
                        ->setWarehouseId($data[5])
                        ->setProductId($data[6])
                        ->save();
                }
            }
            $returnData["message"] = __("Import successful.");
        } else {
            $returnData["success"] = 0;
            $returnData["message"] = __("Some Error occured while uploading.");
        }
        $resultJson->setData($returnData);
        return $resultJson;
    }
}
