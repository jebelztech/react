<?php

namespace Etail\Career\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Session\SessionManagerInterface;
use GeoIp2\WebService\Client;

class Customer extends Action
{
    protected $_customerSession;
    private $sessionManager;
    private $resultPageFactory;
    protected $_storeManager;
    protected $_urlInterface;
    private $cookieMetadataFactory;
    private $cookieManager;
    private $state;
    protected $_client;
    public function __construct(
        Context $context,
        \Magento\Customer\Model\Session $session,
        SessionManagerInterface $sessionManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\State $state,
        \Magento\Framework\UrlInterface $urlInterface, 
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        PageFactory $resultPageFactory
    ) {
        //$this->_client = $client;
        parent::__construct($context);
        $this->_customerSession = $session;
        $this->state = $state;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
        $this->_storeManager = $storeManager;
        $this->_urlInterface = $urlInterface;
        $this->resultPageFactory = $resultPageFactory;
        
    }
    public function getGeoLocation($ip) {
        $client = new Client(729121, 'NhGaTfCX6KEOkI2a');
        $record = $client->city($ip);
        $response['country_code'] = strtolower($record->country->isoCode);
        $response['city_name'] = isset($record->city->names['en'])?strtolower($record->city->names['en']):'';
        return $response;
    }
    public function execute()
    {
        $ip = $this->getUserIP();
        $customerData = array();
        $customerData['redirect'] = "false";
        if($this->state->getAreaCode() == 'frontend') {
            $customerData['redirect'] = "false";
            $stores_check = array('default','arabic','abu_dhabi','al_rigga','jumeirah','sh_ar','sh_en');
            $current_store = $this->_storeManager->getStore()->getCode();

            $check_ip = true;
            $keys['ip'] = 'user_ip';
            $keys['store'] = 'store_code';
            $customerData['show_popup'] = "false";
            $user_ip = $this->getCustomCookie($keys['ip']);
            //$customerData['user_ip'] = $user_ip;
            if($user_ip = $this->getCustomCookie($keys['ip'])) {
                if($user_ip == $ip) {
                    $check_ip = false;
                }
                /*if($user_ip == $ip) {
                    if($user_store_code = $this->getCustomCookie($keys['store'])) {
                        if($user_store_code == $current_store) {
                            $check_ip = false;
                        }
                    }
                }*/
            }
            $customerData['check_ip'] = $check_ip;
            if($check_ip) {
                $user_store_code = $this->getCustomCookie($keys['store']);
                if(in_array($current_store,$stores_check)) {
                    $customerData['show_popup'] = "true";
                }
                else {
                    $this->setCustomCookie($keys['ip'],$ip);
                    $this->setCustomCookie($keys['store'],$current_store);
                }
            }
        }
        if ($this->_customerSession->isLoggedIn()) {
            $customerData['loggedIn'] = "true";
            die(json_encode($customerData));
        } else {
            $customerData['loggedIn'] = "false";
            die(json_encode($customerData));
        }
    }
    public function __execute()
    {
        $ip = $this->getUserIP();
        $customerData = array();
        $customerData['redirect'] = "false";
        //$ip = $this->getIP();
        if(isset($_GET['check_ip']) && $_GET['check_ip']=="1") {
            $response = $this->getGeoLocation($ip);
            //echo "IP: ".$ip." - ip: ".$ip."<br/>";
            echo "<pre>"; print_r($response);
        }
        if($this->state->getAreaCode() == 'frontend') {
            $customerData['redirect'] = "false";
            $current_store = $this->_storeManager->getStore()->getCode();
            $check_ip = true;
            $keys['ip'] = 'user_ip';
            $keys['store'] = 'store_code';
            $user_ip = $this->getCustomCookie($keys['ip']);
            //$customerData['user_ip'] = $user_ip;
            if($user_ip = $this->getCustomCookie($keys['ip'])) {
                
                if($user_ip == $ip) {
                    if($user_store_code = $this->getCustomCookie($keys['store'])) {
                        if($user_store_code == $current_store) {
                            $check_ip = false;
                        }
                    }
                }
            }
            //if(isset($_GET['check_ip']) && $_GET['check_ip']=="1") $check_ip = true;
            $customerData['check_ip'] = $check_ip;
            if($check_ip) {
                $user_store_code = $this->getCustomCookie($keys['store']);
                $arr_check = array('sh_en','sh_ar');
                //$geoData = $this->getGeoLocation($newIp);
                $geoData = $this->getGeoLocation($ip);
                //$customerData['geoData'] = $geoData;
                if(isset($geoData['country_code']) && strtolower($geoData['country_code'])=="ae") {
                    $contry_code = strtolower($geoData['country_code']);
                    $customerData['contry_code'] = $contry_code;
                    $customerData['city_name'] = $geoData['city_name'];
                    if($geoData['city_name']=="sharjah" || in_array($user_store_code,$arr_check)) {
                        $contry_code = "sh";
                        $store_code = $this->getStoreCode($contry_code,$current_store);
                        $current_store = $store_code;
                        //$customerData['redirect'] = "true";
                        $customerData['redirect'] = "false";
                        //$baseUrl = $this->_storeManager->getStore()->getCurrentUrl(true);
                        $url = "https://alsultansweets.ae/stores/store/redirect/___store/$store_code/___from_store/$current_store";
                        $customerData['request_url'] = $url;
                    }
                    /*else {
                        $contry_code = "default";
                        $store_code = $this->getStoreCode($contry_code,$current_store);
                        $customerData['redirect'] = "true";
                        $url = "https://alsultansweets.ae/stores/store/redirect/___store/default/___from_store/$current_store";
                        $customerData['request_url'] = $url;
                    }*/
                    /*$this->setCustomCookie($keys['ip'],$ip);
                    $this->setCustomCookie($keys['store'],$store_code);*/
                }
                $this->setCustomCookie($keys['ip'],$ip);
                $this->setCustomCookie($keys['store'],$current_store);
            }
        }
        
        /*if($this->sessionManager->getIsRedirectedToOther()!=$ip) {
            $this->sessionManager->setIsRedirectedToOther($ip);
            $customerData['redirect'] = "true";
            $locationInfo = $this->getLocationInfoByIp();
            //echo "<pre>"; print_r($locationInfo); exit;
            $current_store_code = $this->_storeManager->getStore()->getCode();
            //$baseUrl = $this->_storeManager->getStore()->getCurrentUrl(true);
            $baseUrl = $this->_storeManager->getStore()->getBaseUrl();
            if(isset($locationInfo['country_code']) && $locationInfo['country_code']=="AE" && isset($locationInfo['city']) && $locationInfo['city']=="Sharjah") { 
                $url = $baseUrl."stores/store/redirect/___store/sh_en/___from_store/".$current_store_code."/set/true";
            }
            else if(isset($locationInfo['country_code']) && $locationInfo['country_code']=="AE") {
                $url = $baseUrl."stores/store/redirect/___store/default/___from_store/".$current_store_code."/set/true";
            }
            else if(isset($locationInfo['country_code']) && $locationInfo['country_code']=="TR") {
                $url = $baseUrl."stores/store/redirect/___store/tr_en/___from_store/".$current_store_code."/set/true";
            }
            else if(isset($locationInfo['country_code']) && $locationInfo['country_code']=="BH") {
                $url = $baseUrl."stores/store/redirect/___store/br_en/___from_store/".$current_store_code."/set/true";
            }
            else {
                $url = $baseUrl."stores/store/redirect/___store/us_en/___from_store/".$current_store_code."/set/true";
            }
            $customerData['url'] = $url;
        }*/
        //$customerData['redirect'] = "false";
    	if ($this->_customerSession->isLoggedIn()) {
    		$customerData['loggedIn'] = "true";
    		die(json_encode($customerData));
		} else {
			$customerData['loggedIn'] = "false";
    		die(json_encode($customerData));
		}
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
        return $store_code;
    }
    function getUserIP() {
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
        $url = "https://api.ip2location.com/v2/?key=VN6MNGKPTU&ip=".$ip."&format=json&package=WS25&&addon=continent,country,region,city,geotargeting,country_groupings,time_zone_info&lang=zh-cn";
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