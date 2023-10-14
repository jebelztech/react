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

namespace Webkul\Walletsystem\Plugin\Model\Order;

use Magento\Sales\Model\Order;

/**
 * Webkul Walletsystem Class
 */
class State
{
    /**
     * After plugin of check function
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Handler\State $subject
     * @param $result
     * @param Order $order
     * @return $this
     */
    public function afterCheck(
        \Magento\Sales\Model\ResourceModel\Order\Handler\State $subject,
        $result,
        Order $order
    ) {
        $currentState = $order->getState();
        $sku = "";
        foreach ($order->getAllItems() as $item) {
            $sku = $item->getSku();
        }
        if (($currentState == Order::STATE_CLOSED) && ($sku == "wk_wallet_amount")) {
            if ($order->getEntityId()) {
                $order->setState(Order::STATE_COMPLETE)
                ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_COMPLETE));
            } else {
                $order->setState(Order::STATE_PROCESSING)
                    ->setStatus($order->getConfig()->getStateDefaultStatus(Order::STATE_PROCESSING));
            }
            return $this;
        }
        return $result;
    }
}
