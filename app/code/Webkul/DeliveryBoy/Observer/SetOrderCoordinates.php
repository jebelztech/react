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
namespace Webkul\DeliveryBoy\Observer;

use Psr\Log\LoggerInterface;
use Throwable;

class SetOrderCoordinates implements \Magento\Framework\Event\ObserverInterface
{
    private $jsonHelper;
    private $deliveryboyHelper;
    private $deliveryAutomationHelper;
    private $logger;
    private $orderLocCollF;
    /**
     * @param \Magento\Store\Model\App\Emulation $emulate
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Webkul\DeliveryBoy\Helper\Data $deliveryboyHelper
     * @param \Webkul\DeliveryBoy\Model\TokenFactory $deviceTokenFactory
     * @param \Webkul\DeliveryBoy\Model\OrderFactory $deliveryboyOrderFactory
     * @param \Webkul\DeliveryBoy\Helper\Operation $operationHelper
     * @param LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Webkul\DeliveryBoy\Helper\Data $deliveryboyHelper,
        \Webkul\DeliveryBoy\Helper\DeliveryAutomation $deliveryAutomationHelper,
        LoggerInterface $logger,
        \Webkul\DeliveryBoy\Model\ResourceModel\OrderLocation\CollectionFactory $orderLocCollF
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->deliveryboyHelper = $deliveryboyHelper;
        $this->deliveryAutomationHelper = $deliveryAutomationHelper;
        $this->logger = $logger;
        $this->orderLocCollF = $orderLocCollF;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getOrder();
            $orderId = $order->getId();
            $orderLocation = $this->orderLocCollF->create()
                ->addFieldToFilter('order_id', $orderId)
                ->getFirstItem();
            $deliveryAddress = $this->getDeliveryAddress($order);
            $orderCoord = $this->deliveryAutomationHelper
                ->getAddressCoordinates($deliveryAddress);
            $this->logger->debug(json_encode($orderCoord));

            if (is_array($orderCoord)) {
                $orderLocation->setLatitude($orderCoord['latitude'])
                    ->setLongitude($orderCoord['longitude'])
                    ->setOrderId($orderId)
                    ->save();
            }
            
        } catch (Throwable $e) {
            $this->logger->debug(__CLASS__);
            $this->logger->debug($e->getMessage());
        }
    }

    public function getDeliveryAddress($order)
    {
        return $order->getShippingAddress() ?? $order->getBillingAddress();
    }
}
