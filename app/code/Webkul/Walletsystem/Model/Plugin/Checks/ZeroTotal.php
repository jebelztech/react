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

namespace Webkul\Walletsystem\Model\Plugin\Checks;

/**
 * Webkul Walletsystem Class
 */
class ZeroTotal
{
    /**
     * @var \Webkul\Walletsystem\Helper\Data
     */
    private $walletHelper;

    /**
     * Initialize dependencies
     *
     * @param \Webkul\Walletsystem\Helper\Data $walletHelper
     */
    public function __construct(
        \Webkul\Walletsystem\Helper\Data $walletHelper
    ) {
        $this->walletHelper = $walletHelper;
    }

    /**
     * @param \Magento\Payment\Model\Checks\ZeroTotal $subject
     * @param bool $result
     * @return void
     */
    public function afterIsApplicable(
        \Magento\Payment\Model\Checks\ZeroTotal $subject,
        $result
    ) {
        if (!$result) {
            return $this->walletHelper->getPaymentisEnabled();
        }
        return $result;
    }
}
