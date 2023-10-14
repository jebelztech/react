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

namespace Webkul\WMS\Block\Adminhtml\Edit\Warehouse;

use Webkul\WMS\Block\Adminhtml\Edit\GenericButton;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class DeleteButton staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * Function to getButtonData
     *
     * @return array $data
     */
    public function getButtonData()
    {
        $warehouseId = $this->getWarehouseId();
        $data = [];
        if ($warehouseId) {
            $data = [
                "id" => "warehouse-edit-delete-button",
                "label" => __("Delete Warehouse"),
                "class" => "delete",
                "on_click" => "",
                "sort_order" => 20,
                "data_attribute" => ["url"=>$this->getDeleteUrl()],
            ];
        }
        return $data;
    }

    /**
     * Function to get Delete Url
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl("*/*/delete", ["id" => $this->getWarehouseId()]);
    }
}
