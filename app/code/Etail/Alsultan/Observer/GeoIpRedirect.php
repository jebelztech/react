<?php
namespace Etail\Alsultan\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\App\RequestInterface;
use \Magento\Framework\Mail\Template\TransportBuilder;
use \Magento\Framework\Translate\Inline\StateInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Api\StoreRepositoryInterface;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\App\Response\Http;
use GeoIp2\WebService\Client;
use Magento\Store\Api\StoreCookieManagerInterface;
//use \Magento\Sales\Model\Order\Email\Sender‌​\OrderSender;

 
class GeoIpRedirect implements ObserverInterface
{
	const XML_PATH_EMAIL_ADMIN_QUOTE_SENDER = 'Email Sender';
    const XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION = 'Your Template Path';
    const XML_PATH_EMAIL_ADMIN_NAME = 'Sender Name';
    const XML_PATH_EMAIL_ADMIN_EMAIL = 'Receiver Email';

    protected $inlineTranslation;
    protected $transportBuilder;
    protected $_logLoggerInterface;
    private $scopeConfig;
    protected $dir;
    protected $orderSender;
    private $resultPageFactory;
    private $cookieManager;
    private $cookieMetadataFactory;
    private $storeManager;
    private $resultRedirectFactory;
    private $state;
    private $response;
    protected $_eventManager;
    public $request;
    private $storeCookieManager;

    public function __construct(
    	    StateInterface $inlineTranslation,
    	    TransportBuilder $transportBuilder,
    	    LoggerInterface $logLoggerInterface,
    	    ScopeConfigInterface $scopeConfig,
            StoreRepositoryInterface $repository,
            \Magento\Framework\Filesystem\DirectoryList $dir,
            \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Magento\Framework\App\State $state,
            \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
            RedirectFactory $resultRedirectFactory,
            \Magento\Framework\Event\ManagerInterface $eventManager,
            \Magento\Framework\App\RequestInterface $request,
            StoreCookieManagerInterface $storeCookieManager,
            Http $response,
    	    array $data = []
        )
    {
        $this->storeManager = $storeManager; 
        $this->cookieManager = $cookieManager;
        $this->state = $state;
        $this->request = $request;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->response = $response;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->repository = $repository;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->_logLoggerInterface = $logLoggerInterface;
        $this->scopeConfig = $scopeConfig;
        $this->dir = $dir;
        $this->storeCookieManager = $storeCookieManager;
        $this->_eventManager = $eventManager;
    }
    public function getGeoLocation($ip) {
        $client = new Client(729121, 'NhGaTfCX6KEOkI2a');
        $record = $client->city($ip);
        $response['country_code'] = strtolower($record->country->isoCode);
        $response['city_name'] = isset($record->city->names['en'])?strtolower($record->city->names['en']):'';
        return $response;
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if(isset($_GET['check_ip']) && $_GET['check_ip']=="true") { 
            if($this->state->getAreaCode() == 'frontend') {
                $ip = $this->getIP();
                $current_store = $this->storeManager->getStore()->getCode();
                $check_ip = true;
                $keys['ip'] = 'user_ip';
                $keys['store'] = 'store_code';
                if($user_ip = $this->getCustomCookie($keys['ip'])) {
                    
                    //echo "user_ip: ".$user_ip."<br/>";
                    
                    if($user_ip == $ip) {
                        if($user_store_code = $this->getCustomCookie($keys['store'])) {
                            //echo "user_store_code: ".$user_store_code."<br/>";
                            if($user_store_code == $current_store) {
                                $check_ip = false;
                            }
                        }
                    }
                }
                if($check_ip) {
                    //$geoData = $this->getGeoData($ip);
                    $geoData = $this->getGeoLocation($ip);
                    $contry_code = strtolower($geoData['country_code']);
                    if(strtolower($geoData['city_name'])=="dubai" && strtolower($geoData['country_code'])=="ae") {
                        $contry_code = "sh";
                        $store_code = $this->getStoreCode($contry_code,$current_store);
                        //echo "store_code: ".$store_code."<br/>";

                        $this->setCustomCookie($keys['ip'],$ip);
                        $this->setCustomCookie($keys['store'],$store_code);
                        //$baseUrl = $this->storeManager->getStore()->getCurrentUrl(true);//$this->storeManager->getStore()->getBaseUrl();
                        //$url = $baseUrl."stores/store/redirect/___store/$store_code/___from_store/$current_store";
                        $this->response->setNoCacheHeaders();
                        $store = $this->storeManager->getStore($store_code);
                        $storeId = $store->getId();
                        $this->storeManager->setCurrentStore($storeId);
                        

                        $params = $_SERVER;
                        $params[\Magento\Store\Model\StoreManager::PARAM_RUN_CODE] = 'store';
                        $params[\Magento\Store\Model\StoreManager::PARAM_RUN_TYPE] = $store_code;
                        $bootstrap = \Magento\Framework\App\Bootstrap::create(BP, $params);

                        #$bootstrap = Bootstrap::create(BP, $_SERVER);
                        /** @var \Magento\Framework\App\Http $app */
                        $app = $bootstrap->createApplication(\Magento\Framework\App\Http::class);
                        $bootstrap->run($app);

                        $this->_eventManager->dispatch(
                            'controller_action_predispatch',
                            ['request' => $this->request, 'store'=>$store]
                        );
                        $this->storeCookieManager->setStoreCookie($store);
                        
                        //$this->storeManager->setCurrentStore($store_code);
                        //header('Location: '.$url);
                        //exit;
                        //$resultRedirect = $this->resultRedirectFactory->create();
                        //echo $url; exit;
                        //return $resultRedirect->setUrl($url);
                        //echo $url;
                    }
                }

                //exit;
            }
            //if($check_ip) {
                //$geoData = $this->getGeoData($ip);
                /**/
            //}
        }
        return $this;
    }
    public function getStoreCode($contry_code,$current_store) {
        $store = "";
        if($current_store=="default") {
            $store = "en";
        }
        else if($current_store=="arabic") {
            $store = "ar";
        }
        else if(preg_match('/\_/',$current_store)) {
            $store_lang = explode('_',$current_store);
            if(!empty($store_lang[1]) && $store_lang[1]=="en") {
                $store = "en";
            }
            else if(!empty($store_lang[1]) && $store_lang[1]=="ar") {
                $store = "ar";
            }
        }
        $store_code = "us_en";
        if($contry_code=="us") {
            $store_code = "us_en";
        }
        else if($contry_code=="tr") {
            $store_code = "tr_".$store;
        }
        else if($contry_code=="ae") {
            if($store=="ar") $store_code = "arabic";
            else $store_code = "default";
        }
        else if($contry_code=="bh") {
            $store_code = "br_".$store;
        }
        else if($contry_code=="sh") {
            $store_code = "sh_".$store;
        }
        else if($contry_code=="de") {
            $store_code = "de_".$store;
        }
        //echo "current_store: ".$current_store."<br/>";
        //echo "store_code: ".$store_code."<br/>";
        return $store_code;
        /*$stores = $this->repository->getList();
        foreach ($stores as $store) {

            echo 'Store: ' . $store->getId()." - ".$store->getCode()."<br/>";
            echo ' --- ';
        }*/
    }
    public function getIP() {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = @$_SERVER['REMOTE_ADDR'];
        $result  = array('country'=>'', 'city'=>'');
        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }else{
            $ip = $remote;
        }
        return $ip;
    }
    public function getGeoData($ip) {
        //$url = "https://api.freegeoip.app/json/".$ip."?apikey=a6fa0fe0-ae90-11ec-9946-c3a107088f5d";
        //$url = "http://api.ipapi.com/api/".$ip."?access_key=acead5b432d1166e435c9a221f325a70";
        //$url = "https://api.ipgeolocation.io/ipgeo?apiKey=906c6797856441eb9137f768c6037dbf&ip=".$ip;
        $url = "https://api.ip2location.com/v2/?key=MFAWSP8PZA&ip=".$ip."&format=json&package=WS25&&addon=continent,country,region,city,geotargeting,country_groupings,time_zone_info&lang=zh-cn";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);

        $response = json_decode($response,true);
        return $response;
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