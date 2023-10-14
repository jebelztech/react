<?php

namespace Etail\Alsultan\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\RequestInterface;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Session\SessionManagerInterface;

class SetCookies extends Action
{
    private $resultPageFactory;
    protected $inlineTranslation;
    protected $transportBuilder;
    protected $_logLoggerInterface;
    private $scopeConfig;
    private $cookieMetadataFactory;
    private $cookieManager;
    protected $dir;
    protected $_storeManager;
    private $sessionManager;
    protected $_responseFactory;

    public function __construct(
            Context $context,
            StateInterface $inlineTranslation,
            TransportBuilder $transportBuilder,
            LoggerInterface $logLoggerInterface,
            ScopeConfigInterface $scopeConfig,
            \Magento\Framework\Filesystem\DirectoryList $dir,
            \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\App\ResponseFactory $responseFactory,
            SessionManagerInterface $sessionManager,
            array $data = []
        )
    {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->_logLoggerInterface = $logLoggerInterface;
        $this->scopeConfig = $scopeConfig;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
        $this->dir = $dir;
        $this->_responseFactory = $responseFactory;
        $this->_storeManager = $storeManager;
    }

    public function execute()
    {
        try
        {
            $cookie_user_ip = $this->getCustomCookie('user_ip');
            $cookie_store_code = $this->getCustomCookie('store_code');

            $stores_check = array('default','arabic','abu_dhabi','al_rigga','jumeirah','sh_ar','sh_en');
            $url = "https://alsultansweets.ae/";
            $current_store = $this->_storeManager->getStore()->getCode();

            $current_ip = $this->getUserIP();

            $params = $this->getRequest()->getParams();
            $requested_store_code = $params['store'];

            if($cookie_user_ip!=$current_ip) {
                $url = "https://alsultansweets.ae/stores/store/redirect/___store/$requested_store_code/___from_store/$current_store";
                //echo $url; exit;
                $this->setCustomCookie('user_ip',$current_ip);
                $this->setCustomCookie('store_code',$requested_store_code);
            }
            else if($cookie_store_code!=$current_store) {
                $url = "https://alsultansweets.ae/stores/store/redirect/___store/$requested_store_code/___from_store/$current_store"; 
                //echo $url; exit;
                $this->setCustomCookie('user_ip',$current_ip);
                $this->setCustomCookie('store_code',$requested_store_code);  
            }
            $this->_responseFactory->create()->setRedirect($url)->sendResponse();
            return;

        } catch(\Exception $e){
            $this->_logLoggerInterface->debug($e->getMessage());
        }
    }
    public function getUserIP() {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
    public function setCustomCookie($key,$value)
    {
        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata();
        $publicCookieMetadata->setDurationOneYear();
        $publicCookieMetadata->setPath('/');
        $publicCookieMetadata->setHttpOnly(false);

        $this->cookieManager->setPublicCookie(
            $key,
            $value,
            $publicCookieMetadata
        );
    }
    public function getCustomCookie($key)
    {
        return $this->cookieManager->getCookie(
            $key
        );
    }
}