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

/**
 * Validate Class for warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Validate extends \Webkul\WMS\Controller\Adminhtml\Warehouse
{
    /**
     * ValidateWarehouse function for class Upload
     *
     * @param \Magento\Framework\DataObject $response response
     *
     * @return warehouse
     */
    protected function validateWarehouse($response)
    {
        $banner = null;
        $errors = [];
        try {
            $banner = $this->warehouseDataFactory->create();
            $data = $this->getRequest()->getParams();
            $dataResult = $data["wms_warehouse"];
            $errors = [];
            if (!isset($dataResult["title"])) {
                $errors[] = __("Warehouse Title is mandatory field.");
            }
            if (!isset($dataResult["source"])) {
                $errors[] = __("Source is mandatory field.");
            }
            if (!isset($dataResult["row_count"])) {
                $errors[] = __("Row count is mandatory field.");
            }
            if (!isset($dataResult["column_count"])) {
                $errors[] = __("Column count is mandatory field.");
            }
            if (!isset($dataResult["shelves_per_cluster"])) {
                $errors[] = __("Shelves per cluster is mandatory field.");
            }
            if (!isset($dataResult["racks_per_shelf"])) {
                $errors[] = __("Racks per shelf is mandatory field.");
            }
        } catch (\Magento\Framework\Validator\Exception $exception) {
            $exceptionMsg = $exception->getMessages(
                \Magento\Framework\Message\MessageInterface::TYPE_ERROR
            );
            foreach ($exceptionMsg as $error) {
                $errors[] = $error->getText();
            }
        }
        if ($errors) {
            $messages = $response->hasMessages() ? $response->getMessages() : [];
            foreach ($errors as $error) {
                $messages[] = $error;
            }
            $response->setMessages($messages);
            $response->setError(1);
        }
        return $banner;
    }

    /**
     * Execute function for class Upload
     *
     * @return json
     */
    public function execute()
    {
        $response = new \Magento\Framework\DataObject();
        $response->setError(0);
        $banner = $this->validateWarehouse($response);
        $resultJson = $this->resultJsonFactory->create();
        if ($response->getError()) {
            $response->setError(true);
            $response->setMessages($response->getMessages());
        }
        $resultJson->setData($response);
        return $resultJson;
    }
}
