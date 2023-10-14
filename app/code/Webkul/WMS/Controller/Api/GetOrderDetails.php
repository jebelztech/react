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
 * Get Order Details for staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class GetOrderDetails extends \Webkul\WMS\Controller\ApiController
{
    /**
     * Escaper
     *
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * ResourceConnection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $resource;

    /**
     * Imagehelper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * GetSourceCodesBySkus
     *
     * @var \Magento\Inventory\Model\GetSourceCodesBySkus
     */
    protected $sourceBySku;

    /**
     * OrderStatusFactory
     *
     * @var \Webkul\WMS\Model\OrderStatusFactory
     */
    protected $orderStatus;

    /**
     * OrderFactory
     *
     * @var \Webkul\WMS\Model\OrderFactory
     */
    protected $orderFactory;

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
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\ProductLocation\CollectionFactory
     */
    protected $locationFactory;

    /**
     * OrderInterfaceFactory
     *
     * @var \Magento\Sales\Api\Data\OrderInterfaceFactory
     */
    protected $mageOrderFactory;

    /**
     * WarehouseFactory
     *
     * @var \Webkul\WMS\Model\WarehouseFactory
     */
    protected $warehouseFactory;

    /**
     * CollectionFactory
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollection;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory
     */
    protected $orderStatusFactory;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory
     */
    protected $assignedToteCollection;

    /**
     * Initialized dependencies function
     *
     * @param \Webkul\WMS\Helper\Data $helper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Webkul\WMS\Model\OrderFactory $orderFactory
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Webkul\WMS\Model\OrderStatusFactory $orderStatus
     * @param \Magento\Framework\App\ResourceConnection $resource
     * @param \Webkul\WMS\Model\WarehouseFactory $warehouseFactory
     * @param \Magento\Inventory\Model\GetSourceCodesBySkus $sourceBySku
     * @param \Magento\Sales\Api\Data\OrderInterfaceFactory $mageOrderFactory
     * @param \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory $toteCollection
     * @param \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection
     * @param \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatusFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection
     * @param \Webkul\WMS\Model\ResourceModel\ProductLocation\CollectionFactory $locationFactory
     * @param \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory $assignedToteCollection
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Webkul\WMS\Model\OrderFactory $orderFactory,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\WMS\Model\OrderStatusFactory $orderStatus,
        \Magento\Framework\App\ResourceConnection $resource,
        \Webkul\WMS\Model\WarehouseFactory $warehouseFactory,
        \Magento\Inventory\Model\GetSourceCodesBySkus $sourceBySku,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $mageOrderFactory,
        \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory $toteCollection,
        \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection,
        \Webkul\WMS\Model\ResourceModel\OrderStatus\CollectionFactory $orderStatusFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollection,
        \Webkul\WMS\Model\ResourceModel\ProductLocation\CollectionFactory $locationFactory,
        \Webkul\WMS\Model\ResourceModel\OrderTotes\CollectionFactory $assignedToteCollection
    ) {
        $this->escaper = $escaper;
        $this->resource = $resource;
        $this->orderStatus = $orderStatus;
        $this->imageHelper = $imageHelper;
        $this->sourceBySku = $sourceBySku;
        $this->orderFactory = $orderFactory;
        $this->toteCollection = $toteCollection;
        $this->staffCollection = $staffCollection;
        $this->locationFactory = $locationFactory;
        $this->orderCollection = $orderCollection;
        $this->mageOrderFactory = $mageOrderFactory;
        $this->warehouseFactory = $warehouseFactory;
        $this->productCollection = $productCollection;
        $this->orderStatusFactory = $orderStatusFactory;
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
            $warehouseId = $staff->getWarehouseId();
            $assignmentIsRequired = false;
            $order = $mageOrder = new \Magento\Framework\DataObject();
            if ($staffId) {
                $orderId = 0;
                if ($this->toteToken == "") {
                    // check order is assigned to any staff or not //////////////////
                    $order = $this->orderCollection
                        ->create()
                        ->addFieldToFilter("staff_id", $staffId)
                        ->addFieldToFilter("increment_id", $this->incrementId)
                        ->getFirstItem();
                    $orderId = $order->getOrderId();
                } else {
                    $collection = $this->toteCollection
                        ->create()
                        ->addFieldToFilter("hash", $this->toteToken);
                    $wot = $this->resource->getTableName("wms_order_totes");
                    $where = "main_table.id=wot.assigned_tote_id";
                    $collection->getSelect()
                        ->joinRight(
                            [
                                "wot" => $wot
                            ],
                            $where,
                            [
                                "order_id" => "order_id"
                            ]
                        );
                    $wos = $this->resource->getTableName("wms_order_status");
                    $where = "wot.order_id=wos.order_id AND status!='packed'";
                    $collection->getSelect()
                        ->joinRight(
                            [
                                "wos" => $wos
                            ],
                            $where,
                            [
                                
                            ]
                        );
                    $toteDetails = new \Magento\Framework\DataObject();
                    foreach ($collection as $each) {
                        $toteDetails = $each;
                    }
                    $toteOrderId = $toteDetails->getOrderId();
                    $mageOrder = $this->mageOrderFactory
                        ->create()
                        ->load($toteOrderId);
                    if ($toteOrderId) {
                        $order = $this->orderCollection
                            ->create()
                            ->addFieldToFilter("order_id", $toteOrderId)
                            ->addFieldToFilter("staff_id", $staffId)
                            ->getFirstItem();
                        $orderId = $order->getOrderId();
                    } elseif ($collection->getSize() > 0) {
                        $this->returnArray["message"] = __(
                            "This tote is not assigned to any product."
                        );
                        return $this->getJsonResponse($this->returnArray);
                    } else {
                        $this->returnArray["message"] = __(
                            "Invalid tote."
                        );
                        return $this->getJsonResponse($this->returnArray);
                    }
                }
                if ($orderId) {
                    // verifying staff for this order ///////////////////////////////
                    if ($order->getStaffId() != $staffId) {
                        $this->returnArray["message"] = __(
                            "Order is already assigned to another staff."
                        );
                        return $this->getJsonResponse($this->returnArray);
                    }
                } else {
                    $assignmentIsRequired = true;
                    // updating order assignment status /////////////////////////////
                    $orderStatus = $this->orderStatusFactory
                        ->create()
                        ->addFieldToFilter("order_id", $orderId)
                        ->getFirstItem();
                    $orderStatusId = $orderStatus->getId();
                    if ($orderStatusId) {
                        $this->orderStatus
                            ->create()
                            ->load($orderStatusId)
                            ->setStatus("initiated")
                            ->save();
                    } else {
                        $this->orderStatus
                            ->create()
                            ->setOrderId($orderId)
                            ->setStatus("initiated")
                            ->save();
                    }
                }
                $warehouse = $this->warehouseFactory->create()->load($warehouseId);
                $warehouseSourceCode = $warehouse->getSource();
                if (!$mageOrder->getId()) {
                    $mageOrder = $this->mageOrderFactory
                        ->create()
                        ->loadByIncrementId($this->incrementId);
                }
                $mageOrderId = $mageOrder->getId();
                $productList = [];
                foreach ($mageOrder->getItemsCollection() as $item) {
                    if ($item->getProductType() == "configurable" && !$item->getParentItem()) {
                        continue;
                    }
                    $productId = $item->getProductId();
                    $order = $this->orderCollection
                        ->create()
                        ->addFieldToFilter("order_id", $mageOrderId)
                        ->addFieldToFilter("product_id", $productId)
                        ->addFieldToFilter("staff_id", $staffId)
                        ->getFirstItem();
                    if (!$order->getId()) {
                        continue;
                    }
                    $productSku = $item->getSku();
                    // showing only those product available in particular staff's warehouse
                    if (!$item->isDeleted()
                        && in_array(
                            $warehouseSourceCode,
                            $this->sourceBySku->execute(
                                [
                                    $productSku
                                ]
                            )
                        )
                    ) {
                        $eachProduct = [];
                        if ($assignmentIsRequired) {
                            $this->orderFactory
                                ->create()
                                ->setOrderId($mageOrderId)
                                ->setIncrementId($this->incrementId)
                                ->setWarehouseId($warehouseId)
                                ->setProductId($productId)
                                ->setStaffId($staffId)
                                ->save();
                        }
                        $product = $this->productCollection
                            ->create()
                            ->addAttributeToSelect("image")
                            ->addAttributeToSelect("entity_id")
                            ->addAttributeToSelect("thumbnail")
                            ->addAttributeToSelect("small_image")
                            ->addAttributeToFilter("entity_id", $productId)
                            ->getFirstItem();
                        $locationCollection = $this->locationFactory
                            ->create()
                            ->addFieldToFilter("product_id", $productId)
                            ->addFieldToFilter("warehouse_id", $warehouseId);
                        $eachProduct["location"] = [];
                        foreach ($locationCollection as $eachLocation) {
                            $eachProduct["location"][] = [
                                "id" => $eachLocation->getId(),
                                "row" => $eachLocation->getRow(),
                                "rack" => $eachLocation->getRack(),
                                "shelf" => $eachLocation->getShelf(),
                                "column" => $eachLocation->getColumn(),
                                "qty" => $eachLocation->getLocationQty()
                            ];
                        }
                        $eachProduct["sku"] = $productSku;
                        $eachProduct["qty"] = $item->getQtyOrdered()*1;
                        $eachProduct["name"] = $this->escaper->escapeXssInUrl($item->getName());
                        $eachProduct["thumbNail"] = $this->getImageUrl(
                            $product,
                            $this->width/2.5
                        );
                        $productList[] = $eachProduct;
                    }
                }
                $this->returnArray["productList"] = $productList;
                if (!isset($orderStatus)) {
                    $orderStatus = $this->orderStatusFactory
                        ->create()
                        ->addFieldToFilter("order_id", $orderId)
                        ->getFirstItem();
                    $this->returnArray["status"] = $orderStatus->getStatus();
                } else {
                    $this->returnArray["status"] = $orderStatus->getStatus();
                }
                $this->returnArray["incrementId"] = $mageOrder->getIncrementId();
                $toteArray = $this->assignedToteCollection
                    ->create()
                    ->addFieldToSelect("order_id")
                    ->addFieldToSelect("assigned_tote_title")
                    ->addFieldToFilter("order_id", $orderId)
                    ->toArray();
                $this->returnArray["toteList"] = $toteArray["items"];
                if ($assignmentIsRequired) {
                    $this->returnArray["message"] = __(
                        "Order is assigned to you, now you can pick items from warehouse."
                    );
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
     * Function verify Request to authenticate the request
     * Authenticates the request and logs the result for invalid requests
     *
     * @return Json
     */
    protected function verifyRequest()
    {
        if ($this->getRequest()->getMethod() == "GET" && $this->wholeData) {
            $this->width = $this->wholeData["width"] ?? 500;
            $this->toteToken = $this->wholeData["toteToken"] ?? "";
            $this->staffToken = $this->wholeData["staffToken"] ?? "";
            $this->incrementId = $this->wholeData["incrementId"] ?? "";
        } else {
            $this->returnArray["message"] = __("Invalid Request");
            return $this->getJsonResponse($this->returnArray);
        }
    }

    /**
     * Function to get Image Url
     *
     * @param \Magento\Catalog\Model\Product $product   product data
     * @param integer                        $size      size
     * @param string                         $imageType type of image
     * @param bool                           $keepFrame keep frame or not
     *
     * @return string
     */
    protected function getImageUrl(
        $product,
        $size,
        $imageType = "product_page_image_small",
        $keepFrame = true
    ) {
        return $this->imageHelper
            ->init($product, $imageType)
            ->keepFrame($keepFrame)
            ->resize($size)
            ->getUrl();
    }
}
