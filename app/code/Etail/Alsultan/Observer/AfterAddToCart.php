<?php
namespace Etail\Alsultan\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Response\Http;
use Magento\Backend\Model\View\Result\RedirectFactory;
  
class AfterAddToCart implements ObserverInterface
{
    private $request;
    private $storeManager;
    private $response;
    private $resultRedirectFactory;
    private $_checkoutSession;
    public function __construct(
    	    \Magento\Framework\App\RequestInterface $request,
            RedirectFactory $resultRedirectFactory,
            \Magento\Checkout\Model\Session $checkoutSession,
            Http $response,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
    	    array $data = []
        )
    {
        $this->_checkoutSession = $checkoutSession;
        $this->storeManager = $storeManager; 
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	$post = $this->request->getParams();
        //echo "<pre>"; print_r($post); exit;
        if(isset($post['buy-now'])) {
            
            $baseUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
            //$this->_checkoutSession->setNoCartRedirect(true);
            $url = $baseUrl."checkout";
            $this->_checkoutSession->setContinueShoppingUrl($url);
            //$this->_checkoutSession->setRedirectUrl($url);
            //$observer->getControllerAction()->getResponse()->setRedirect($url);
            header('Location: '.$url);
            exit;
            
            //$this->response->setNoCacheHeaders();
            //$resultRedirect = $this->resultRedirectFactory->create();
            //return $resultRedirect->setUrl($url);
        }
        //return $this;
    }
}