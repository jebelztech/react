<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_Mobikulwallet
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Mobikulwallet\Plugin\Controller\Customer;
use \Magento\Framework\Controller\ResultFactory;
use Magento\Store\Model\ScopeInterface;

class OrderList     {

    protected $_helper;
    protected $_request;
    protected $_jsonHelper;
    protected $_viewTemplate;
    protected $_resultFactory;
    protected $scopeConfig;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $_date;
    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    public function __construct(
        ResultFactory $resultFactory,
        \Webkul\MobikulCore\Helper\Data $helper,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Sales\Model\Order $_order,
        \Magento\Framework\View\Element\Template $viewTemplate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->_helper                 = $helper;
        $this->_date                   = $date;
        $this->_order                  = $_order;
        $this->_request                = $request;
        $this->_jsonHelper             = $jsonHelper;
        $this->scopeConfig             = $scopeConfig;
        $this->_viewTemplate           = $viewTemplate;
        $this->_resultFactory          = $resultFactory;
    }

    public function afterExecute(\Webkul\MobikulApi\Controller\Customer\OrderList $subject, $response)     {
        if ($response->getRawData()) {
            $returnArray = json_decode($response->getRawData(), true);
            $wholeData   = $this->_request->getParams();
            
            $authKey     = $this->_request->getHeader("authKey");
            $authData    = $this->_helper->isAuthorized($authKey);
            $resultJson  = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
            
            if ($returnArray && $authData["code"] == 1 && $returnArray["success"]) {
                $storeId       =  isset($wholeData["storeId"]) ? $wholeData["storeId"] : 1;
                foreach($returnArray['orderList'] as &$items) {
                    $order = $this->_order->load($items['id']);
                    $orderItems = $order->getAllVisibleItems();
                    $items['walletReview'] = false;
                    foreach ($orderItems as $item) {
                        if ($item->getSku() == "wk_wallet_amount") {
                            $items['walletReview'] = true;
                        }
                    }
                }
                
            } else {
                $resultJson->setHttpResponseCode(\Magento\Framework\Webapi\Exception::HTTP_UNAUTHORIZED);
                $resultJson->setHeader("token", $authData["token"], true);
            }
            $resultJson->setData($returnArray);
            return $resultJson;
        }
        return $response;
    }
}