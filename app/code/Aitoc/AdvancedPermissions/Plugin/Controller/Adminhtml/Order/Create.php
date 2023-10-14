<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Plugin\Controller\Adminhtml\Order;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Backend\Model\Session\Quote;
use Magento\Sales\Controller\Adminhtml\Order\Create as CreateOrderController;

class Create
{
    /**
     * @var Data
     */
    protected $helper;
    
    /**
     * Quote session object
     *
     * @var Quote
     */
    protected $_session;

    /**
     * @param Data $helper
     * @param Quote $quoteSession
     */
    public function __construct(
        Data $helper,
        Quote $quoteSession
    ) {
        $this->helper = $helper;
        $this->_session = $quoteSession;
    }
    
    /**
     * Create Order
     *
     * @param CreateOrderController $object
     *
     * @return void
     */
    public function beforeExecute(
        CreateOrderController $object
    ) {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $ids = $this->helper->getAllowedStoreViewIds();

            if (isset($ids[0]) && count($ids) == 1) {
                $this->_session->setStoreId($ids[0]);
            }
        }
    }
}
