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

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Webkul Walletsystem Class
 */
class RemoveBlockForDiscount implements ObserverInterface
{
    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $walletHelper;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\Walletsystem\Helper\Data $walletHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->walletHelper = $walletHelper;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();
        $block = $layout->getBlock('checkout.cart.coupon');

        if ($block) {
            if (!$this->walletHelper->getDiscountEnable() && !$this->walletHelper->getCartStatus()) {
                $layout->unsetElement('checkout.cart.coupon');
                return $this;
            }
            return $this;
        }
        return $this;
    }
}
