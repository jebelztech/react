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

namespace Webkul\Walletsystem\Block\Form;

/**
 * Webkul Walletsystem Block
 */
class Walletsystem extends \Magento\OfflinePayments\Block\Form\AbstractInstruction
{
    /**
     * Bank transfer template
     *
     * @var string
     */
    protected $_template = 'form/walletsystem.phtml';

    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * @var \Webkul\Knockout\Model\WalletPaymentConfigProvider
     */
    protected $configProvider;

    /**
     * Initialize dependencies
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Webkul\Walletsystem\Model\WalletPaymentConfigProvider $configProvider
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Webkul\Walletsystem\Model\WalletPaymentConfigProvider $configProvider,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout']) ? $data['jsLayout'] : [];
        $this->configProvider = $configProvider;
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        return \Zend_Json::encode($this->jsLayout);
    }

    /**
     * Get customer config
     *
     * @return mixed
     */
    public function getCustomConfig()
    {
        return $this->configProvider->getConfig();
    }
}
