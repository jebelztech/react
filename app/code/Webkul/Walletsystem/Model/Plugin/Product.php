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

namespace Webkul\Walletsystem\Model\Plugin;

/**
 * Webkul Walletsystem Class
 */
class Product
{
    /**
     * @var \Webkul\Walletsystem\Helper\Data
     */
    private $walletHelper;
    
    /**
     * @var Magento\Framework\App\State
     */
    protected $appState;

    /**
     * Initialize dependencies
     *
     * @param \Webkul\Walletsystem\Helper\Data $walletHelper
     * @param \Magento\Framework\App\State     $appState
     */
    public function __construct(
        \Webkul\Walletsystem\Helper\Data $walletHelper,
        \Magento\Framework\App\State $appState
    ) {
        $this->walletHelper = $walletHelper;
        $this->appState = $appState;
    }

    /**
     * Around plugin of addAttributeToSelect function
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $subject
     * @param \Closure $proceed
     * @param string $attribute
     * @param boolean $joinType
     * @return void
     */
    public function aroundAddAttributeToSelect(
        \Magento\Catalog\Model\ResourceModel\Product\Collection $subject,
        \Closure $proceed,
        $attribute,
        $joinType = false
    ) {
        $appState = $this->appState;
        $areCode = $appState->getAreaCode();
        $walletSku = $this->walletHelper::WALLET_PRODUCT_SKU;
        $result = $proceed($attribute, $joinType = false);
        $code = \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE;
        if ($appState->getAreaCode() == $code) {
            $result->addFieldToFilter('sku', ['neq' => $walletSku]);
        }

        return $result;
    }
}
