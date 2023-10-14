<?php
/**
* @author Amasty Team
* @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
* @package Amasty_CheckoutLayoutBuilder
*/

declare(strict_types=1);

namespace Amasty\CheckoutLayoutBuilder\Model;

use Amasty\Base\Model\ConfigProviderAbstract;
use Amasty\Base\Model\Serializer;
use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider extends ConfigProviderAbstract
{
    /**
     * Path Prefix For Config
     */
    const PATH_PREFIX = 'amasty_checkout/';

    const LAYOUT_BUILDER_BLOCK = 'layout_builder/';
    const DESIGN_BLOCK = 'design/';

    const FIELD_FRONTEND_LAYOUT_CONFIG = 'frontend_layout_config';
    const FIELD_LAYOUT_BUILDER_CONFIG = 'layout_builder_config';
    const FIELD_CHECKOUT_DESIGN = 'checkout_design';
    const FIELD_CHECKOUT_LAYOUT = 'layout';
    const FIELD_CHECKOUT_LAYOUT_MODERN = 'layout_modern';

    /**
     * xpath prefix of module (section)
     */
    protected $pathPrefix = self::PATH_PREFIX;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(ScopeConfigInterface $scopeConfig, Serializer $serializer)
    {
        parent::__construct($scopeConfig);
        $this->serializer = $serializer;
    }

    /**
     * @param ?int $store
     * @return array
     */
    public function getCheckoutBlocksConfig(int $store = null): array
    {
        $value = $this->getValue(self::LAYOUT_BUILDER_BLOCK . self::FIELD_FRONTEND_LAYOUT_CONFIG, $store);

        return $this->serializer->unserialize($value);
    }
}
