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
 * Get Order List for staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class GetOrderList extends \Webkul\WMS\Controller\ApiController
{
    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * Status
     *
     * @var \Webkul\WMS\Model\Order\Source\Status
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
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory
     */
    protected $assignedToteCollection;

    /**
     * Initailized Constructor
     *
     * @param \Webkul\WMS\Helper\Data                                      $helper
     * @param \Magento\Framework\App\Action\Context                        $context
     * @param \Magento\Framework\Json\Helper\Data                          $jsonHelper
     * @param \Webkul\WMS\Model\Order\Source\Status                        $orderStatus
     * @param \Magento\Framework\App\ResourceConnection                    $resource
     * @param \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory      $orderCollection
     * @param \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory      $staffCollection
     * @param \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory $assignedToteCollection
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\WMS\Model\Order\Source\Status $orderStatus,
        \Magento\Framework\App\ResourceConnection $resource,
        \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection,
        \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory $assignedToteCollection
    ) {
        $this->resource = $resource;
        $this->orderStatus = $orderStatus;
        $this->staffCollection = $staffCollection;
        $this->orderCollection = $orderCollection;
        $this->assignedToteCollection = $assignedToteCollection;
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
                $collection = $this->orderCollection
                    ->create()
                    ->addFieldToFilter("staff_id", $staffId);
                if ($this->searchQuery != "") {
                    $collection->addFieldToFilter(
                        "main_table.increment_id",
                        [
                            "like" => "%".$this->searchQuery."%"
                        ]
                    );
                }
                $collection->getSelect()->group("main_table.order_id");
                $so = $this->resource->getTableName("sales_order");
                $where = "main_table.order_id=so.entity_id";
                $collection->getSelect()
                    ->joinLeft(
                        [
                            "so" => $so
                        ],
                        $where,
                        [
                            "total_qty_ordered" => "total_qty_ordered"
                        ]
                    );
                $wos = $this->resource->getTableName("wms_order_status");
                if ($this->status == "") {
                    $where = "main_table.order_id=wos.order_id";
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
                } else {
                    $where = "main_table.order_id=wos.order_id AND wos.status='$this->status'";
                    $collection->getSelect()
                        ->joinRight(
                            [
                                "wos" => $wos
                            ],
                            $where,
                            [
                                "status" => "status"
                            ]
                        );
                }
                $collection->setOrder(
                    "entity_id",
                    "DESC"
                );
                $counter = clone $collection;
                $counter = $counter->toArray();
                $this->returnArray["totalCount"] = count($counter["items"]);
                if ($this->pageNumber >= 1) {
                    $from = $this->pageNumber-1;
                    $collection->getSelect()->limit($this->limit, $from);
                }
                $orderList = [];
                foreach ($collection as $eachOrder) {
                    $oneOrder = [];
                    $oneOrder["qty"] = $eachOrder->getTotalQtyOrdered() * 1;
                    $oneOrder["status"] = $eachOrder->getStatus();
                    $oneOrder["incrementId"] = $eachOrder->getIncrementId();
                    $toteArray = $this->assignedToteCollection
                        ->create()
                        ->addFieldToSelect("order_id")
                        ->addFieldToSelect("assigned_tote_title")
                        ->addFieldToFilter("order_id", $eachOrder->getOrderId())
                        ->toArray();
                    $oneOrder["toteList"] = $toteArray["items"];
                    $orderList[] = $oneOrder;
                }
                $this->returnArray["orderList"] = $orderList;
                $this->returnArray["orderStatus"] = $this->orderStatus
                    ->toOptionArray();
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
     * Function verify Request to authenticate the request
     * Authenticates the request and logs the result for invalid requests
     *
     * @return Json
     */
    protected function verifyRequest()
    {
        if ($this->getRequest()->getMethod() == "GET" && $this->wholeData) {
            $this->status = $this->wholeData["status"] ?? "";
            $this->pageNumber = $this->wholeData["pageNumber"] ?? 1;
            $this->staffToken = $this->wholeData["staffToken"] ?? "";
            $this->searchQuery = $this->wholeData["searchQuery"] ?? "";
            $this->limit = $this->wholeData["limit"] ?? 22;
        } else {
            $this->returnArray["message"] = __("Invalid Request");
            return $this->getJsonResponse($this->returnArray);
        }
    }
}
