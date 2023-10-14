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

/**
 * Class Assign to staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Assign extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\HTTP\Client\Curl
     */
    private $curl;

    /**
     * Helper
     *
     * @var \Webkul\MobikulCore\Helper\Data
     */
    protected $helper;

    /**
     * JsonHelper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

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
     * StaffFactory
     *
     * @var \Webkul\WMS\Model\StaffFactory
     */
    protected $staffFactory;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory
     */
    protected $orderCollection;

    /**
     * Contructor Method
     *
     * @param \Webkul\WMS\Helper\Data                                 $helper          helper
     * @param \Webkul\WMS\Model\OrderFactory                          $orderFactory    orderFactory
     * @param \Magento\Backend\App\Action\Context                     $context         context
     * @param \Webkul\WMS\Model\StaffFactory                          $staffFactory    staffFactory
     * @param \Magento\Framework\Json\Helper\Data                     $jsonHelper      jsonHelper
     * @param \Webkul\WMS\Model\OrderStatusFactory                    $orderStatus     orderStatus
     * @param \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection orderCollection
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Webkul\WMS\Model\OrderFactory $orderFactory,
        \Magento\Backend\App\Action\Context $context,
        \Webkul\WMS\Model\StaffFactory $staffFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\WMS\Model\OrderStatusFactory $orderStatus,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Webkul\WMS\Model\ResourceModel\Order\CollectionFactory $orderCollection
    ) {
        $this->curl = $curl;
        $this->helper = $helper;
        $this->jsonHelper = $jsonHelper;
        $this->orderStatus = $orderStatus;
        $this->staffFactory = $staffFactory;
        $this->orderFactory = $orderFactory;
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
        $wholeData = $this->getRequest()->getParams();
        $staffIds = [];
        foreach ($wholeData["warehouse_id"] as $key => $each) {
            if ($each != "") {
                $assignedOrder = $this->orderCollection->create()
                    ->addFieldToFilter("product_id", $key)
                    ->addFieldToFilter("order_id", $wholeData["order_id"])
                    ->addFieldToFilter("increment_id", $wholeData["increment_id"])
                    ->getFirstItem();
                $order = $this->orderFactory->create();
                if ($assignedOrder->getId()) {
                    $order = $order->load($assignedOrder->getId());
                }
                $staffIds[] = $wholeData["staff_id"][$key];
                $order
                    ->setProductId($key)
                    ->setWarehouseId($each)
                    ->setOrderId($wholeData["order_id"])
                    ->setStaffId($wholeData["staff_id"][$key])
                    ->setIncrementId($wholeData["increment_id"])
                    ->save();
            }
        }
        $orderStatus = $this->orderStatus
            ->create()
            ->getCollection()
            ->addFieldToFilter("order_id", $wholeData["order_id"])
            ->getFirstItem();
        if ($orderStatus->getId()) {
            $this->orderStatus
                ->create()
                ->load($orderStatus->getId())
                ->setStatus("initiated")
                ->save();
        } else {
            $this->orderStatus
                ->create()
                ->setOrderId($wholeData["order_id"])
                ->setStatus("initiated")
                ->save();
        }
        // sending notification to staff about order assignment /////////////////////
        $staffCollection = $this->staffFactory->create()
            ->getCollection()
            ->addFieldToFilter("id", ["in"=>$staffIds]);
        foreach ($staffCollection as $staff) {
            $message = [
                "id" => $wholeData["order_id"],
                "body" => __("Please check order details for picking."),
                "title" => __("New Order Assigned."),
                "sound" => "default",
                "message" => __("Please check order details for picking."),
                "incrementId" => $wholeData["increment_id"],
                "notificationType" => "order_assign"
            ];
            $url = "https://fcm.googleapis.com/fcm/send";
            $authKey = $this->helper->getConfigData("wms/configuration/fcmkey");
            $headers = [
                "Authorization:key=".$authKey,
                "Content-Type:application/json",
            ];
            if ($authKey != "" && $staff->getDeviceToken()) {
                $fields = [
                    "to" => $staff->getDeviceToken(),
                    "priority" => "high",
                    "time_to_live" => 30,
                    "notification" => $message,
                    "delay_while_idle" => true,
                    "content_available" => true
                ];
                $message["click_action"] = "FLUTTER_NOTIFICATION_CLICK";
                $fields["data"] = $message;
                $this->curl->addHeader("Authorization", "key=".$authKey);
                $this->curl->addHeader("Content-Type", "application/json");
                $this->curl->post($url, $this->jsonHelper->jsonEncode($fields));
                $result = $this->curl->getBody();

                if ($this->isJson($result)) {
                    $result = $this->jsonHelper->jsonDecode($result);
                }
            }
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $this->messageManager->addSuccess(
            __(
                "Order Assigned to staff for Pick & Pack."
            )
        );
        $resultRedirect->setPath(
            "sales/order/view",
            [
                "order_id"=>$wholeData["order_id"]
            ]
        );
        return $resultRedirect;
    }

    /**
     * Function isJson to check if a string is jSon
     *
     * @param string $string string
     *
     * @return bool
     */
    public function isJson($string)
    {
        json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
