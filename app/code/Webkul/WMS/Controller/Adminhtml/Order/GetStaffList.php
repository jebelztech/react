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

namespace Webkul\WMS\Controller\Adminhtml\Order;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class GetStaffList order
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class GetStaffList extends \Magento\Backend\App\Action
{
    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory
     */
    protected $staffCollection;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * Contructor Method
     *
     * @param \Magento\Backend\App\Action\Context                     $context         context
     * @param \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection orderCollection
     * @param \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection staffCollection
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection
    ) {
        $this->staffCollection = $staffCollection;
        $this->orderCollection = $orderCollection;
        parent::__construct($context);
    }

    /**
     * Generate order assign info for ajax request
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $orderId = $this->getRequest()->getParam("orderId");
        $productId = $this->getRequest()->getParam("productId");
        $warehouseId = $this->getRequest()->getParam("warehouseId");
        $staffCollection = $this->staffCollection->create()
            ->addFieldToFilter("status", 1)
            ->addFieldToFilter("warehouse_id", $warehouseId);
        $returnData = [];
        foreach ($staffCollection as $eachStaff) {
            $eachData = [];
            $eachData["id"] = $eachStaff->getId();
            $eachData["name"] = $eachStaff->getName();
            $activeOrder = $this->orderCollection->create()
                ->addFieldToSelect("staff_id", "order_id")
                ->addFieldToFilter("staff_id", $eachData["id"])
                ->addExpressionFieldToSelect("total_order_active", "COUNT({{order_id}})", "order_id")
                ->getSelect()
                ->group("order_id");
            $conn = $activeOrder->getConnection();
            $data = $conn->fetchAll($activeOrder);
            $eachData["activeOrder"] = $data[0]["total_order_active"] ?? 0;
            $isSelected = $this->orderCollection->create()
                ->addFieldToFilter("staff_id", $eachData["id"])
                ->addFieldToFilter("order_id", $orderId)
                ->addFieldToFilter("product_id", $productId)
                ->getFirstItem();
            if ($isSelected->getId()) {
                $eachData["isSelected"] = 1;
            } else {
                $eachData["isSelected"] = 0;
            }
            $returnData[] = $eachData;
        }
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($returnData);
        return $resultJson;
    }
}
