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

namespace Webkul\WMS\Controller\Api;

/**
 * ChangeStatus of Order
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ChangeStatus extends \Webkul\WMS\Controller\ApiController
{
    /**
     * ProductLocation
     *
     * @var \Webkul\WMS\Model\ProductLocationFactory
     */
    protected $productLocationFactory;

    /**
     * OrderStatusFactory
     *
     * @var \Webkul\WMS\Model\OrderStatusFactory
     */
    protected $orderStatus;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory
     */
    protected $staffCollection;

    /**
     * OrderTotesFactory
     *
     * @var \Webkul\WMS\Model\OrderTotesFactory
     */
    protected $orderTotesFactory;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory
     */
    protected $orderStatusFactory;

    /**
     * Data
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Initialized Constructor
     *
     * @param \Webkul\WMS\Helper\Data $helper
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Webkul\WMS\Model\OrderStatusFactory $orderStatus
     * @param \Webkul\WMS\Model\ProductLocationFactory $productLocationFactory
     * @param \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Webkul\WMS\Model\OrderTotesFactory $orderTotesFactory
     * @param \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection
     * @param \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatusFactory
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\WMS\Model\OrderStatusFactory $orderStatus,
        \Webkul\WMS\Model\ProductLocationFactory $productLocationFactory,
        \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Webkul\WMS\Model\OrderTotesFactory $orderTotesFactory,
        \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection,
        \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatusFactory
    ) {
        $this->orderStatus = $orderStatus;
        $this->orderTotesFactory = $orderTotesFactory;
        $this->staffCollection = $staffCollection;
        $this->orderCollection = $orderCollection;
        $this->orderStatusFactory = $orderStatusFactory;
        $this->productLocationFactory = $productLocationFactory;
        $this->jsonHelper = $jsonHelper;
        parent::__construct(
            $helper,
            $context,
            $jsonHelper
        );
    }

    /**
     * Execute Function
     *
     * @return json
     */
    public function execute()
    {
        try {
            $validity = $this->verifyRequest();
            if ($validity) {
                return $validity;
            }
            // getting staff details from token /////////////////////////////////////
            $staff = $this->staffCollection
                ->create()
                ->addFieldToFilter("staff_token", $this->staffToken)
                ->getFirstItem();
            $staffId = $staff->getId();
            if ($staffId) {
                // check order is assigned to any staff or not //////////////////////
                $order = $this->orderCollection
                    ->create()
                    ->addFieldToFilter("increment_id", $this->incrementId)
                    ->getFirstItem();
                $orderId = $order->getOrderId();
                // verifying staff for this order ///////////////////////////////
                if ($order->getStaffId() != $staffId) {
                    $this->returnArray["message"] = __("Unauthorised access.");
                    return $this->getJsonResponse($this->returnArray);
                }
                if ($orderId) {
                    $orderStatus = $this->orderStatusFactory
                        ->create()
                        ->addFieldToFilter("order_id", $orderId)
                        ->getFirstItem();
                    $orderStatusId = $orderStatus->getId();
                    if ($orderStatusId) {
                        $this->orderStatus
                            ->create()
                            ->load($orderStatusId)
                            ->setStatus($this->status)
                            ->save();
                        $this->deductQty($orderId);
                    }
                    $this->returnArray["message"] = __(
                        "Order status changed."
                    );
                } else {
                    $this->returnArray["message"] = __("Invalid Order.");
                    $this->returnArray["sessionLogout"] = true;
                    return $this->getJsonResponse($this->returnArray);
                }
                $this->returnArray["success"] = true;
                return $this->getJsonResponse($this->returnArray);
            } else {
                $this->returnArray["message"] = __("Invalid User.");
                $this->returnArray["sessionLogout"] = true;
                return $this->getJsonResponse($this->returnArray);
            }
        } catch (\Exception $e) {
            $this->returnArray["message"] = __($e->getMessage());
            $this->helper->printLog($this->returnArray);
            return $this->getJsonResponse($this->returnArray);
        }
    }

    /**
     * Deducting picked Qty
     *
     * @return void
     */
    private function deductQty($orderId)
    {
        foreach ($this->locationQtyData as $locationId => $qtyPicked) {
            if ($locationId != "" && $qtyPicked != "") {
                $productLocation =  $this->productLocationFactory->create()->load($locationId);
                $leftQty = $productLocation->getLocationQty() - ((int)$qtyPicked);
                $leftQty = $leftQty < 0 ? 0 : $leftQty;
                $productLocation->setLocationQty($leftQty)->save();
                $order = $this->orderTotesFactory->create()->load($orderId, "order_id");
                $order->setLocationId($locationId)
                    ->setQtyPicked($qtyPicked)
                    ->save();
            }
        }
    }

    /**
     * Function verify Request to authenticate the request
     * Authenticates the request and logs the result for invalid requests
     *
     * @return Json
     */
    protected function verifyRequest()
    {
        if ($this->getRequest()->getMethod() == "POST" && $this->wholeData) {
            $this->status = $this->wholeData["status"] ?? "picked";
            $this->staffToken = $this->wholeData["staffToken"] ?? "";
            $this->incrementId = $this->wholeData["incrementId"] ?? "";
            $this->locationQtyData = $this->wholeData["locationQtyData"] ?? "[]";
            $this->locationQtyData = $this->jsonHelper->jsonDecode($this->locationQtyData);
            if ($this->status == "picked") {
                if (count($this->locationQtyData) <= 0) {
                    $this->returnArray["message"] = __("Location and Quantity is not set");
                    return $this->getJsonResponse($this->returnArray);
                }
            }
        } else {
            $this->returnArray["message"] = __("Invalid Request");
            return $this->getJsonResponse($this->returnArray);
        }
    }
}
