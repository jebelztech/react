<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutStyleSwitcher
*/

declare(strict_types=1);

namespace Amasty\CheckoutStyleSwitcher\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * Path Prefix For Config
     */
    const PATH_PREFIX = 'amasty_checkout/';

    const DESIGN_BLOCK = 'design/';

    const FIELD_CHECKOUT_DESIGN = 'checkout_design';
    const FIELD_CHECKOUT_LAYOUT_MODERN = 'layout_modern';
    const PLACE_BUTTON_LAYOUT = 'place_button_layout';

    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = self::PATH_PREFIX;

    /**
     * @param ?int $store
     * @return string
     */
    public function getCheckoutDesign(int $store = null): string
    {
        return (string)$this->getValue(self::DESIGN_BLOCK . self::FIELD_CHECKOUT_DESIGN, $store);
    }

    /**
     * @param ?int $store
     * @return string
     */
    public function getLayoutModernTemplate(int $store = null): string
    {
        return (string)$this->getValue(self::DESIGN_BLOCK . self::FIELD_CHECKOUT_LAYOUT_MODERN, $store);
    }

    /**
     * @param ?int $store
     * @return string
     */
    public function getPlaceOrderPosition(int $store = null): string
    {
        return (string)$this->getValue(self::DESIGN_BLOCK . self::PLACE_BUTTON_LAYOUT, $store);
    }

    /**
     * @param ?int $store
     * @return int
     */
    public function getBillingAddressDisplayOn(int $store = null): int
    {
        return (int)$this->scopeConfig->getValue(
            'checkout/options/display_billing_address_on',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
