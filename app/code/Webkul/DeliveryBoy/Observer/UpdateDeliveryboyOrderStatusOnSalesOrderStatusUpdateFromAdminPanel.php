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

class UpdateDeliveryboyOrderStatusOnSalesOrderStatusUpdateFromAdminPanel implements
    \Magento\Framework\Event\ObserverInterface
{
    private $deliveryboyOrderCollectionFactory;
    private $logger;
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
        \Webkul\DeliveryBoy\Model\ResourceModel\Order\CollectionFactory $deliveryboyOrderCollectionFactory,
        LoggerInterface $logger
    ) {
        $this->deliveryboyOrderCollectionFactory = $deliveryboyOrderCollectionFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $order = $observer->getOrder();
            $deliveryboyOrderCollection = $this->getDeliveryboyOrderCollection($order);
            $this->syncDeliveryboyOrderStatus($deliveryboyOrderCollection, $order);
        } catch (Throwable $e) {
            $this->logger->debug(__CLASS__);
            $this->logger->debug($e->getMessage());
        }
    }

    public function syncDeliveryboyOrderStatus($deliveryboyOrderCollection, $order)
    {
        foreach ($deliveryboyOrderCollection as $deliveryboyOrder) {
            $deliveryboyOrder->setOrderStatus($order->getState())->save();
        }
    }

    public function getDeliveryboyOrderCollection($order)
    {
        return $this->deliveryboyOrderCollectionFactory->create()
        ->addFieldToFilter(
            "increment_id",
            $order->getIncrementId()
        );
    }
}
