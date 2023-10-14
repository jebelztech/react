<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\Sales\Model\Order\Payment\Transaction as PaymentTransaction;
use Magento\Framework\DB\Transaction;
use Magento\Framework\Session\SessionManager;
use Magento\Sales\Model\Order;

/**
 * Webkul Walletsystem Class
 */
class SalesOrderInvoiceSaveAfterObserver implements ObserverInterface
{
    /**
     * @var \Webkul\Walletsystem\Helper\Mail
     */
    protected $mailHelper;
    
    /**
     * @param \Webkul\Walletsystem\Helper\Mail $mailHelper
     * @param \Psr\Log\LoggerInterface $log
     * @param InvoiceService $invoiceService
     * @param SessionManager $coreSession
     * @param PaymentTransaction\BuilderInterface $transactionBuilder
     * @param Transaction $dbTransaction
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Webkul\Walletsystem\Helper\Mail $mailHelper,
        \Psr\Log\LoggerInterface $log,
        InvoiceService $invoiceService,
        SessionManager $coreSession,
        PaymentTransaction\BuilderInterface $transactionBuilder,
        Transaction $dbTransaction,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->mailHelper = $mailHelper;
        $this->logger = $log;
        $this->dbTransaction = $dbTransaction;
        $this->coreSession = $coreSession;
        $this->transactionBuilder = $transactionBuilder;
        $this->invoiceService = $invoiceService;
        $this->messageManager = $messageManager;
    }

    /**
     * Invoice save after
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $invoice = $observer->getInvoice();
        $order = $invoice->getOrder();
        if ($order->getItemsCollection()->getFirstItem()->getSku() == "wk_wallet_amount") {
            try {
                $this->mailHelper->checkAndUpdateWalletAmount($order);
                $this->messageManager->addSuccess(__('Wallet Amount Updated'));
                $order->setTotalPaid($order->getGrandTotal());
                $orderState = Order::STATE_COMPLETE;
                $order->setState($orderState)->setStatus(Order::STATE_COMPLETE);
                $order->save();
            } catch (\Exception $e) {
                $this->messageManager->addError(__('Something Went Wrong with Wallet Amount'));
            }
            return true;
        } elseif (!$this->coreSession->getCustomInvoiceId()) {
            $this->coreSession->setCustomInvoiceId($invoice->getId());
        } else {
            $this->coreSession->unsCustomInvoiceId();
            return false;
        }
        //$this->logger->addInfo("invoice id ".$invoice->getId());
        $this->mailHelper->checkAndUpdateWalletAmount($order);
        $order->setWalletInvoiced(1);
        $order->save();
        $this->checkAndProcessWalletInvoice($invoice, $order);
        $this->createTransaction($order);
    }

    /**
     * Check and process wallet invoice
     *
     * @param object $invoice
     * @param object $order
     * @return void
     */
    public function checkAndProcessWalletInvoice($invoice, $order)
    {
        try {
            $walletAmount = -$order->getWalletAmount();
            $invoiceTotal = $invoice->getGrandTotal();
            $invoice->setGrandTotal($invoiceTotal + $walletAmount);
            $invoice->save();
            return true;
        } catch (\Exception $e) {
            $this->logger->addInfo("exception while creating invoice for wallet ".$e->getMessage());
        }
    }

    /**
     * Create traansaction
     *
     * @param object $order
     * @return void
     */
    public function createTransaction($order)
    {
        $transId = 'wk_wallet_system'.$order->getId().'-'.rand();
        $transArray = [
            'id' => $transId,
            'amount' => - (int) $order->getWalletAmount()
        ];

        $payment = $order->getPayment();
        $payment->setLastTransId($transArray['id']);
        $payment->setTransactionId($transArray['id']);
        $payment->setAdditionalInformation(
            [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $transArray]
        );
        $formatedPrice = $order->getBaseCurrency()->formatTxt(
            -$order->getWalletAmount()
        );

        $message = __('The authorized amount is %1.', $formatedPrice);

        $trans = $this->transactionBuilder;
        $transaction = $trans->setPayment($payment)
            ->setOrder($order)
            ->setTransactionId($transArray['id'])
            ->setAdditionalInformation(
                [\Magento\Sales\Model\Order\Payment\Transaction::RAW_DETAILS => (array) $transArray]
            )
            ->setFailSafe(true)
            ->build(\Magento\Sales\Model\Order\Payment\Transaction::TYPE_CAPTURE);

        $payment->addTransactionCommentsToOrder(
            $transaction,
            $message
        );
        $payment->setParentTransactionId(null);
        $payment->save();
        $order->save();
    }
}
