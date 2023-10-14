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
 * Add Tote for staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class AddTote extends \Webkul\WMS\Controller\ApiController
{
    const STARTED = "started";
    const INITIATED = "initiated";
    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * OrderStatusFactory
     *
     * @var \Webkul\WMS\Model\OrderStatusFactory
     */
    protected $orderStatus;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory
     */
    protected $toteCollection;

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
    protected $orderToteFactory;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory
     */
    protected $orderStatusFactory;

    /**
     * Constructor
     *
     * @param \Webkul\WMS\Helper\Data                                       $helper                 helper
     * @param \Magento\Framework\App\Action\Context                         $context                context
     * @param \Magento\Framework\Json\Helper\Data                           $jsonHelper             jsonHelper
     * @param \Webkul\WMS\Model\OrderStatusFactory                          $orderStatus            orderStatus
     * @param \Magento\Framework\App\ResourceConnection                     $resource               resource
     * @param \Webkul\WMS\Model\OrderTotesFactory                           $orderToteFactory       orderToteFactory
     * @param \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory        $toteCollection         toteCollection
     * @param \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory       $orderCollection        orderCollection
     * @param \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory       $staffCollection        staffCollection
     * @param \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatusFactory     orderStatusFactory
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\WMS\Model\OrderStatusFactory $orderStatus,
        \Magento\Framework\App\ResourceConnection $resource,
        \Webkul\WMS\Model\OrderTotesFactory $orderToteFactory,
        \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory $toteCollection,
        \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection,
        \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatusFactory
    ) {
        $this->resource = $resource;
        $this->orderStatus = $orderStatus;
        $this->toteCollection = $toteCollection;
        $this->staffCollection = $staffCollection;
        $this->orderCollection = $orderCollection;
        $this->orderToteFactory = $orderToteFactory;
        $this->orderStatusFactory = $orderStatusFactory;
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
            $staff = $this->staffCollection->create()->addFieldToFilter(
                "staff_token",
                $this->staffToken
            )->getFirstItem();
            $staffId = $staff->getId();
            if ($staffId) {
                // check order is assigned to any staff or not //////////////////////
                $order = $this->orderCollection->create()
                    ->addFieldToFilter(
                        "increment_id",
                        $this->incrementId
                    )
                    ->addFieldToFilter(
                        "staff_id",
                        $staffId
                    )
                ->getFirstItem();
                $orderId = $order->getOrderId();
                if ($orderId) {
                    // verifying staff for this order ///////////////////////////////
                    if ($order->getStaffId() != $staffId) {
                        $this->returnArray["message"] = __("Unauthorised access.");
                        return $this->getJsonResponse($this->returnArray);
                    }
                    $collection = $this->toteCollection->create()->addFieldToFilter(
                        "hash",
                        $this->toteToken
                    );
                    if (!$collection->getSize()) {
                        $this->returnArray["message"] = __("Invalid Tote.");
                        return $this->getJsonResponse($this->returnArray);
                    }
                    $wot = $this->resource->getTableName("wms_order_totes");
                    $where = "main_table.id=wot.assigned_tote_id";
                    $collection->getSelect()
                        ->joinLeft(
                            [
                                "wot" => $wot
                            ],
                            $where,
                            [
                                "order_id" => "order_id"
                            ]
                        );
                    $wos = $this->resource->getTableName("wms_order_status");
                    $where = "wot.order_id=wos.order_id";
                    $collection->getSelect()
                        ->joinLeft(
                            [
                                "wos" => $wos
                            ],
                            $where,
                            [
                                "status" => "status"
                            ]
                        );
                    $previousOrderId = 0;
                    $isToteVacant = true;
                    $toteDetails = new \Magento\Framework\DataObject();
                    foreach ($collection as $each) {
                        $toteDetails = $each;
                        if ($each->getStatus() && in_array($each->getStatus(), ["started", "picked"])) {
                            $previousOrderId = $each->getOrderId();
                            $isToteVacant = false;
                        }
                        break;
                    }
                    if (!$isToteVacant) {
                        $this->returnArray["message"] = __(
                            "Tote is already assiged to order %1.",
                            $previousOrderId
                        );
                        return $this->getJsonResponse($this->returnArray);
                    } else {
                        $this->orderToteFactory
                            ->create()
                            ->setOrderId($orderId)
                            ->setAssignedToteId($toteDetails->getId())
                            ->setAssignedToteTitle($toteDetails->getTitle())
                            ->save();
                        $this->returnArray["message"] = __(
                            "Tote Assigned Successfully."
                        );
                        $orderStatus = $this->orderStatusFactory
                            ->create()
                            ->addFieldToFilter("order_id", $orderId)
                            ->getFirstItem();
                        $orderStatusId = $orderStatus->getId();
                        if ($orderStatusId) {
                            $this->_changeOrderStatusByStatusId($orderStatus, $orderStatusId);
                        } else {
                            $this->_changeOrderStatus($orderId);
                        }
                    }
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
     * Change order status by status id
     *
     * @param int $orderStatusId
     * @return void
     */
    private function _changeOrderStatusByStatusId($orderStatus, $orderStatusId)
    {
        if ($orderStatus->getStatus() == self::INITIATED) {
            $this->orderStatus->create()->load(
                $orderStatusId
            )->setStatus(
                self::STARTED
            )->save();
        }
    }

    /**
     * Change order status
     *
     * @param int $orderId
     * @return void
     */
    private function _changeOrderStatus($orderId)
    {
        $this->orderStatus->create()
            ->setOrderId($orderId)
            ->setStatus(self::STARTED)
            ->save();
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
            $this->toteToken = $this->wholeData["toteToken"] ?? "";
            $this->staffToken = $this->wholeData["staffToken"] ?? "";
            $this->incrementId = $this->wholeData["incrementId"] ?? "";
        } else {
            $this->returnArray["message"] = __("Invalid Request");
            return $this->getJsonResponse($this->returnArray);
        }
    }
}
