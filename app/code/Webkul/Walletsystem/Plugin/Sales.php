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

namespace Webkul\Walletsystem\Plugin;

/**
 * Webkul Walletsystem Class
 */
class Sales
{
    /**
     * Initialize dependencies
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Sales\Model\Order $order,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->order = $order;
        $this->request = $request;
    }

    public function afterCanCreditmemo(
        \Magento\Sales\Model\Order $subject,
        $result
    ) {
        $orderId = $this->request->getParam('order_id');
        $order = $this->order->load($orderId);
        if ($order->getItemsCollection()->getFirstItem()->getSku()=="wk_wallet_amount") {
            return false;
        }
        return $result;
    }
}
