<?php
/**
 * Webkul Software.
 *
 *
 * @category  Webkul
 * @package   Webkul_DeliveryBoy
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\DeliveryBoy\Controller\Api\Admin;

use Magento\Framework\Exception\LocalizedException;
use Webkul\DeliveryBoy\Model\Deliveryboy\Source\ApproveStatus;

class GetDisapprovedDeliveryboyList extends \Webkul\DeliveryBoy\Controller\Api\AbstractDeliveryboy
{
    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        try {
            $this->verifyRequest();
            $environment = $this->emulate->startEnvironmentEmulation($this->storeId);
            $deliveryboyCollection = $this->deliveryboyResourceCollection->create();
            $deliveryboyList = [];
            $this->addFilter($deliveryboyCollection);
            foreach ($deliveryboyCollection as $each) {
                $eachDeliveryboy = [];
                $eachDeliveryboy["id"] = $each->getId();
                $eachDeliveryboy["name"] = $each->getName();
                $relativeImagePath = $each->getImage();
                $eachDeliveryboy["avatar"] = $this->getDeliveryBoyImageUrl($relativeImagePath);
                $deliveryboyList[] = $eachDeliveryboy;
            }
            
            $this->returnArray["success"] = true;
            $this->returnArray["totalCount"] = $deliveryboyCollection->getSize();
            $this->returnArray["deliveryBoyList"] = $deliveryboyList;
            $this->emulate->stopEnvironmentEmulation($environment);
        } catch (\Throwable $e) {
            $this->returnArray["message"] = __($e->getMessage());
        }

        return $this->getJsonResponse($this->returnArray);
    }

    /**
     * @return void
     * @throws LocalizedException
     */
    public function verifyRequest(): void
    {
        if ($this->getRequest()->getMethod() == "GET" && $this->wholeData) {
            $this->mFactor = trim($this->wholeData["mFactor"] ?? 1);
            $this->storeId = trim($this->wholeData["storeId"] ?? 1);
            $this->pageNumber = trim((int)($this->wholeData["pageNumber"] ?? 1));
            $this->ownerEmail = trim($this->wholeData["ownerEmail"] ?? "");
        } else {
            throw new LocalizedException(__("Invalid Request"));
        }
    }

    /**
     * @param string $imagePath
     * @return string
     */
    public function getDeliveryBoyImageUrl($imagePath)
    {
        $Iconheight = $IconWidth = 144 * $this->mFactor;
        $newUrl = "";
        $basePath = $this->baseDir . DIRECTORY_SEPARATOR . $imagePath;
        try {
            if ($this->fileDriver->isFile($basePath)) {
                $newPath = $this->baseDir . DIRECTORY_SEPARATOR . "deliveryboyresized" . DIRECTORY_SEPARATOR .
                    $IconWidth . "x" . $Iconheight . DIRECTORY_SEPARATOR . $imagePath;
                $this->helperCatalog->resizeNCache($basePath, $newPath, $IconWidth, $Iconheight);
                $newUrl = $this->deliveryboyHelper->getUrl("media") . "deliveryboyresized"
                    . DIRECTORY_SEPARATOR .
                    $IconWidth . "x" . $Iconheight . DIRECTORY_SEPARATOR . $imagePath;
            }
        } catch (\Throwable $t) {
            $this->logger->debug($t->getMessage());
        }
        return $newUrl;
    }

    public function addFilter($deliveryboyCollection)
    {
        $deliveryboyCollection->addFieldToFilter(
            'approve_status',
            ApproveStatus::STATUS_NOT_APPROVED
        );
        return $deliveryboyCollection;
    }
}
