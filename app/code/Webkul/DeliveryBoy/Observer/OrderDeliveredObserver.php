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
use Webkul\DeliveryBoy\Helper\Data as DeliveryboyDataHelper;
use Webkul\DeliveryBoy\Api\OrderTransactionRepositoryInterface as OrderTransactionRepository;
use Webkul\DeliveryBoy\Api\Data\OrderTransactionInterface;
use Webkul\DeliveryBoy\Model\OrderTransactionFactory;
use Webkul\DeliveryBoy\Model\OrderTransaction\Source\Status as OrderTransactionStatus;

class OrderDeliveredObserver implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * @var DeliveryboyDataHelper
     */
    private $deliveryboyDataHelper;

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
        DeliveryboyDataHelper $deliveryboyDataHelper,
        OrderTransactionFactory $orderTransactionF,
        OrderTransactionRepository $orderTransactionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        LoggerInterface $logger
    ) {
        $this->deliveryboyDataHelper = $deliveryboyDataHelper;
        $this->orderTransactionF = $orderTransactionF;
        $this->orderTransactionRepository = $orderTransactionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            $deliveryboyOrder = $observer->getDeliveryboyOrder();
            $amount = $observer->getAmount();
            $transactionId = $this->deliveryboyDataHelper
                ->generateDeliveryboyOrderTransactionId($deliveryboyOrder);
            $orderTransaction = $this->orderTransactionF->create()->load(
                $transactionId,
                OrderTransactionInterface::TRANSACTION_ID
            );
            $orderTransaction->setTransactionId($transactionId);
            $orderTransaction->setDeliveryboyOrderId($deliveryboyOrder->getId());
            $orderTransaction->setAmount($amount);
            $orderTransaction->setIsClosed(OrderTransactionStatus::IS_CLOSED_NO);
            $orderTransaction->save();
        } catch (\Throwable $e) {
            $this->logger->debug($e->getMessage());
        }
    }
}
