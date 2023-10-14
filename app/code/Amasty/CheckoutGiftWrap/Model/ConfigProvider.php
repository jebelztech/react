<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutGiftWrap
*/

declare(strict_types=1);

namespace Amasty\CheckoutGiftWrap\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Magento\Store\Model\ScopeInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * Path Prefix For Config
     */
    const PATH_PREFIX = 'amasty_checkout/';

    /**
     * Gift Wrap Config Prefix
     */
    const GIFT_WRAP_PREFIX = 'amgiftwrap/';

    const GENERAL_BLOCK = 'general/';
    const GIFTS = 'gifts/';

    const GIFT_WRAP = 'gift_wrap';
    const GIFT_WRAP_FEE = 'gift_wrap_fee';
    const GIFT_WRAP_MODULE = 'enabled';

    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = self::PATH_PREFIX;

    /**
     * @param int|string|ScopeInterface|null $website
     * @return bool
     */
    public function isGiftWrapEnabled($website = null): bool
    {
        return $this->isSetFlag(
            self::GIFTS . self::GIFT_WRAP,
            $website,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * @param int|string|ScopeInterface|null $website
     * @return float
     */
    public function getGiftWrapFee($website = null): float
    {
        return (float)$this->getValue(
            self::GIFTS . self::GIFT_WRAP_FEE,
            $website,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * @return bool
     */
    public function isGiftWrapModuleEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::GIFT_WRAP_PREFIX .
            self::GENERAL_BLOCK .
            self::GIFT_WRAP_MODULE,
            ScopeInterface::SCOPE_STORES
        );
    }
}
