<?php

namespace Webkul\DeliveryBoy\Block\Adminhtml\OrderTransaction;

use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Webkul\DeliveryBoy\Controller\RegistryConstants;
use Webkul\DeliveryBoy\Model\ResourceModel\Order\CollectionFactory as DeliveryboyOrderCollF;
use Webkul\DeliveryBoy\Model\OrderTransaction\Source\Status as OrderTransactionStatus;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

class View extends Template
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        Registry $registry,
        DeliveryboyOrderCollF $deliveryboyOrderCollF,
        PriceCurrencyInterface $priceCurrency,
        OrderTransactionStatus $orderTransactionStatus,
        TimezoneInterface $timezone,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->deliveryboyOrderCollF = $deliveryboyOrderCollF;
        $this->priceCurrency = $priceCurrency;
        $this->orderTransactionStatus = $orderTransactionStatus;
        $this->timezone = $timezone;
        parent::__construct($context, $data);
    }

    public function getCurrentTransaction()
    {
        $currentTransaction = $this->registry
            ->registry(RegistryConstants::CURRENT_ORDER_TRANSACTION);
        return $currentTransaction;
    }

    public function getIsClosedStatusString()
    {
        $status = $this->getCurrentTransaction()->getIsClosed();
        $statusString = $this->orderTransactionStatus->getOptionsWithLabel()[$status];
        return $statusString;
    }

    public function getAmount()
    {
        $orderTransaction = $this->getCurrentTransaction();
        $amount = $orderTransaction->getAmount();
        $deliveryBoyOrderId = $orderTransaction->getDeliveryboyOrderId();
        $deliveryBoyOrderColl = $this->deliveryboyOrderCollF->create()->addFieldToFilter(
            'id',
            $deliveryBoyOrderId
        );
        $deliveryBoyOrderColl->joinOrderTable($deliveryBoyOrderColl->getSelect());
        $deliveryboyOrder = $deliveryBoyOrderColl->getFirstItem();
        $orderCurrencyCode = $deliveryboyOrder->getOrderCurrencyCode();
        $amount = $this->priceCurrency->convert($amount, null, $orderCurrencyCode);
        return $amount;
    }

    public function formatLocalSpecificDate($date)
    {
        $formattedDate = $this->timezone->formatDate($date);
        return $formattedDate;
    }
}
