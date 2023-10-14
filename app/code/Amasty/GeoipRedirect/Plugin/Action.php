<?php

namespace Amasty\GeoipRedirect\Plugin;

use Amasty\Base\Model\MagentoVersion;
use Amasty\Base\Model\Serializer;
use Amasty\Geoip\Model\Geolocation;
use Amasty\GeoipRedirect\Helper\Data;
use Amasty\GeoipRedirect\Model\RedirectUrl\UrlProcessor;
use Amasty\GeoipRedirect\Model\Source\Logic;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\FrontControllerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Router\Base;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Locale\ResolverInterface;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\Data\StoreInterface;
use Magento\Store\Api\StoreCookieManagerInterface;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Action
{
    const LOCAL_IP = '127.0.0.1';
    const URL_TRIM_CHARACTER = '/';
    const HOME = 'cms_index_index';
    const FIRST_REDIRECT_WITH_POPUP = null;

    protected $redirectAllowed = false;

    protected $addressPath = [
        'HTTP_X_REAL_IP',
        'HTTP_CLIENT_IP',
        'HTTP_X_FORWARDED_FOR'
    ];

    /**
     * @var RemoteAddress
     */
    private $remoteAddress;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var Data
     */
    private $geoipHelper;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Geolocation
     */
    private $geolocation;

    /**
     * @var Session
     */
    private $customerSession;

    /**
     * @var StoreCookieManagerInterface
     */
    private $storeCookieManager;

    /**
     * @var RedirectFactory
     */
    private $resultRedirectFactory;

    /**
     * @var Http
     */
    private $response;

    /**
     * @var SessionManagerInterface $sessionManager
     */
    private $sessionManager;

    /**
     * @var ResolverInterface
     */
    private $resolver;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var UrlFinderInterface
     */
    private $urlFinder;

    /**
     * @var Base
     */
    private $baseRouter;

    /**
     * @var MagentoVersion
     */
    private $magentoVersion;

    /**
     * @var UrlProcessor
     */
    private $urlProcessor;

    public function __construct(
        RemoteAddress $remoteAddress,
        ScopeConfigInterface $scopeConfig,
        Data $geoipHelper,
        UrlInterface $urlBuilder,
        StoreManagerInterface $storeManager,
        Geolocation $geolocation,
        Session $customerSession,
        StoreCookieManagerInterface $storeCookieManager,
        RedirectFactory $resultRedirectFactory,
        Http $response,
        SessionManagerInterface $sessionManager,
        ResolverInterface $resolver,
        Serializer $serializer,
        UrlFinderInterface $urlFinder,
        Base $baseRouter,
        MagentoVersion $magentoVersion,
        UrlProcessor $urlProcessor
    ) {
        $this->remoteAddress = $remoteAddress;
        $this->scopeConfig = $scopeConfig;
        $this->geoipHelper = $geoipHelper;
        $this->urlBuilder = $urlBuilder;
        $this->storeManager = $storeManager;
        $this->geolocation = $geolocation;
        $this->customerSession = $customerSession;
        $this->storeCookieManager = $storeCookieManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->response = $response;
        $this->sessionManager = $sessionManager;
        $this->resolver = $resolver;
        $this->serializer = $serializer;
        $this->urlFinder = $urlFinder;
        $this->baseRouter = $baseRouter;
        $this->magentoVersion = $magentoVersion;
        $this->urlProcessor = $urlProcessor;
    }

    /**
     * @param FrontControllerInterface $subject
     * @param \Closure $proceed
     * @param RequestInterface $request
     * @return Redirect
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundDispatch(
        FrontControllerInterface $subject,
        \Closure $proceed,
        RequestInterface $request
    ) {

        $currentStoreId = $this->storeManager->getStore()->getId();
        $resultRedirect = $this->resultRedirectFactory->create();
        $session = $this->sessionManager;
        if(!$this->sessionManager->getIsRedirectedToSharjah()) {
            $locationInfo = $this->getLocationInfoByIp();
            //echo "<pre>"; print_r($locationInfo); echo "</pre>"; exit;
            if(isset($locationInfo['city']) && $locationInfo['city']=="Sharjah") {
                $this->sessionManager->setIsRedirectedToSharjah(1);
                $baseUrl = $this->storeManager->getStore()->getBaseUrl();
                $url = $baseUrl."stores/store/redirect/___store/sh_en/___from_store/default";
                $this->response->setNoCacheHeaders();
                return $resultRedirect->setUrl($url);
            }
        }
        $this->sessionManager->setIsRedirectedToSharjah(1);
        $scopeStores = ScopeInterface::SCOPE_STORES;

        if (!$this->scopeConfig->isSetFlag('amgeoipredirect/general/enable', $scopeStores, $currentStoreId)
            || $this->isNeedToProceed($request)
        ) {
            $session->setIsRedirectedToSharjah(1);
            return $proceed($request);
        }

        
        $countRedirectStore = $countRedirectCurrency = $countRedirectUrl = 0;
        $isNotFirstTime = null;
        $changeCurrency = false;

        if ($this->scopeConfig->getValue('amgeoipredirect/restriction/first_visit_redirect')) {
            // session value getters should be before processed request, otherwise will return null with FPC enabled
            $isNotFirstTime = $session->getIsNotFirstTime();
            $countRedirectStore = $session->getAmYetRedirectStore();
            $countRedirectCurrency = $session->getAmYetRedirectCurrency();
            $countRedirectUrl = $session->getAmYetRedirectUrl();
            $session->setIsNotFirstTime(1);
            $session->setIsRedirectedToSharjah(1);
        }
        
        $this->applyLogic($request);
        /** @var Redirect $resultRedirect */
        

        if (!$this->scopeConfig->getValue('amgeoipredirect/general/enable', $scopeStores, $currentStoreId)
            || !$this->redirectAllowed
        ) {
            /*if(isset($_GET['___store'])) {
                echo "<pre>"; echo "aaaaaaaaaaaaaaaaaaaaa"; echo "</pre>"; exit;
            }*/
            $session->setIsRedirectedToSharjah(1);
            return $proceed($request);
        }

        $currentIp = $this->getCurrentIp($request);
        if ($this->isIpBlock($currentIp)) {
            $websiteId = $this->storeManager->getWebsite()->getId();
            $page = $this->scopeConfig->getValue(
                'amgeoipredirect/restrict_by_ip/cms_to_show',
                ScopeInterface::SCOPE_WEBSITE,
                $websiteId
            );
            $url = $this->urlBuilder->getUrl($page);
            if (rtrim($this->urlProcessor->parseUrl($url)['path'], '/')
                !== rtrim($this->urlProcessor->parseUrl($this->urlBuilder->getCurrentUrl())['path'], '/')
            ) {
                $this->response->setNoCacheHeaders();
                return $resultRedirect->setUrl($url);
            }
        }
        $location = $this->geolocation->locate($currentIp);
        $country = $location->getCountry();

        if (!$countRedirectCurrency
            && !$isNotFirstTime
            && $this->scopeConfig->getValue('amgeoipredirect/country_currency/enable_currency')
        ) {
            $changeCurrency = true;
        }

        if (!$countRedirectUrl
            && !$isNotFirstTime
            && $this->scopeConfig->getValue('amgeoipredirect/country_url/enable_url')
            && $country
        ) {
            $urlMapping = $this->serializer->unserialize(
                $this->scopeConfig->getValue(
                    'amgeoipredirect/country_url/url_mapping',
                    $scopeStores,
                    $currentStoreId
                )
            );

            $currentUrl = trim($this->urlBuilder->getCurrentUrl(), self::URL_TRIM_CHARACTER);
            //$locationInfo = $this->getLocationInfoByIp();
            //echo "11111111111<pre>"; print_r($locationInfo); exit;

            foreach ($urlMapping as $countries => $url) {
                $url = trim($url, self::URL_TRIM_CHARACTER);

                if (strpos($countries, $country) !== false && $url != $currentUrl) {
                    $session->setAmYetRedirectUrl(1);
                    $this->response->setNoCacheHeaders();

                    if ($this->needShowRedirectionPopup()) {
                        $session->setAmYetRedirectUrl(null);
                        $session->setIsRedirectedToSharjah(1);
                        $session->setIsNotFirstTime(self::FIRST_REDIRECT_WITH_POPUP);
                        $session->setAmPopupCountry($country);
                        return $proceed($request);
                    }

                    if ($this->sessionManager->getWillRedirect() !== false) {
                        return $resultRedirect->setUrl($url);
                    }
                }
            }
        }

        if (!$countRedirectStore
            && !$isNotFirstTime
            && $this->scopeConfig->getValue('amgeoipredirect/country_store/enable_store')
        ) {
            $allStores = $this->storeManager->getStores();
            // foreach ($allStores as $store) {
            //     echo "<pre>"; print_r($store->getData()); 
            // }exit;
            //if($locationInfo['city'])
            foreach ($allStores as $store) {
                //if( $locationInfo['city']=="Sharjah" && !in_array($store->getCode(),$sharjah_stores) ) continue; 
                $currentStoreUrl = str_replace('&amp;', '&', $store->getCurrentUrl(false));
                $redirectStoreUrl = trim($currentStoreUrl, '/');

                $countries = $this->scopeConfig->getValue(
                    'amgeoipredirect/country_store/affected_countries',
                    $scopeStores,
                    $store->getId()
                );
                if (!$this->scopeConfig->getValue('amgeoipredirect/restriction/redirect_between_websites')) {
                    $useMultistores = $store->getWebsiteId() == $this->storeManager->getStore()->getWebsiteId();
                } else {
                    $useMultistores = true;
                }

                if ($country && $countries && strpos($countries, $country) !== false
                    && $store->getId() != $currentStoreId
                    && $useMultistores
                ) {
                    $currentUrl = $this->urlBuilder->getCurrentUrl();
                    $neededBaseUrl = $store->getBaseUrl();
                    /*if(isset($_GET['___store'])) {
                        echo "currentUrl: ".$currentUrl."<br/>";
                        echo "neededBaseUrl: ".$neededBaseUrl."<br/>";
                    }*/

                    if ((strpos($currentUrl, $neededBaseUrl) === false)
                        || !$this->_compareEqualUrlFromStore($request, $store)
                    ) {
                        if ($changeCurrency) {
                            $this->_setNewCurrencyIfExist($country, $scopeStores, $store->getId());
                        }
                        /*if(isset($_GET['___store'])) {
                            echo "111: ".$redirectStoreUrl."<br/>";
                        }*/
                        $redirectStoreUrl = $this->urlProcessor->updateRedirectUrlQueryParams(
                            $redirectStoreUrl,
                            $request,
                            $this->storeManager->getStore(),
                            $store
                        );
                        /*if(isset($_GET['___store'])) {
                            echo "222: ".$redirectStoreUrl."<br/>";
                        }*/

                        if ($this->needShowRedirectionPopup()) {
                            $session->setIsRedirectedToSharjah(1);
                            $session->setAmYetRedirectStore(null);
                            $session->setIsNotFirstTime(self::FIRST_REDIRECT_WITH_POPUP);
                            $session->setRedirectStoreId($store->getId());
                            $session->setAmPopupCountry($country);

                            return $proceed($request);
                        } elseif ($this->sessionManager->getWillRedirect() !== false) {

                            /*if(isset($_GET['___store'])) {
                                echo "333: ".$store->getCode()."<br/>"; exit;
                            }*/
                            $this->_setDefaultLocale($store);
                            $session->setIsRedirectedToSharjah(1);
                            $session->setAmYetRedirectStore(1);
                            $this->storeCookieManager->setStoreCookie($store);
                            $this->response->setNoCacheHeaders();

                            return $resultRedirect->setUrl(
                                $this->tryReplaceWithUrlRewrite($request, $redirectStoreUrl, $store->getWebsiteId())
                            );
                        }
                    }
                }
            }
        }

        if ($changeCurrency && !empty($country)) {
            $this->_setNewCurrencyIfExist($country, $scopeStores, $currentStoreId);
        }

        return $proceed($request);
    }
    public function getLocationInfoByIp() {
        /*$client  = @$_SERVER['HTTP_CLIENT_IP'];
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
        $ip_data = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=".$ip));    
        if($ip_data && $ip_data->geoplugin_countryName != null){
            $result['country'] = $ip_data->geoplugin_countryCode;
            $result['city'] = $ip_data->geoplugin_city;
        }
        return $result;*/
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
        $url = "https://api.freegeoip.app/json/".$ip."?apikey=a6fa0fe0-ae90-11ec-9946-c3a107088f5d";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($ch);
        curl_close($ch);
        //print_r($response);
        //$ip_url = 'https://ip-get-geolocation.com/api/json/'.$ip.'?key=99a78a66b411ef493dbc8b832238f217';
        //$LocationArray = json_decode( file_get_contents($ip_url), true);
        /*$result  = array('country'=>'', 'city'=>'');
        if(!empty($LocationArray['country']) && !empty($LocationArray['city'])) $result  = array('country'=>$LocationArray['country'], 'city'=>$LocationArray['city']);*/
        //echo "<pre>"; print_r($LocationArray); exit;
        /*echo $LocationArray['country'];     
        echo $LocationArray['city'];    
        echo $LocationArray['region'];  
        echo $LocationArray['timezone'];
        exit;*/
        $response = json_decode($response,true);
        if(isset($_GET['check_ip']) && $_GET['check_ip']=="true") {
            echo "<pre>"; print_r($response); exit;
        }
        return $response;
        /*echo $LocationArray['country'];     
        echo $LocationArray['city'];    
        echo $LocationArray['region'];  
        echo $LocationArray['timezone']; */
    }
    /**
     * @param RequestInterface $request
     * @param string $url
     * @param int $storeId
     * @return string
     * @throws NoSuchEntityException
     */
    private function tryReplaceWithUrlRewrite(RequestInterface $request, string $url, int $storeId): string
    {
        $rewrite = $this->urlFinder->findOneByData([
            UrlRewrite::REQUEST_PATH => ltrim($request->getPathInfo(), '/'),
            UrlRewrite::STORE_ID => $this->storeManager->getStore()->getId(),
        ]);

        if ($rewrite) {
            $rewriteToOtherStore = $this->urlFinder->findOneByData([
                UrlRewrite::TARGET_PATH => $rewrite->getTargetPath(),
                UrlRewrite::STORE_ID => $storeId,
            ]);

            if ($rewriteToOtherStore) {
                return str_replace(
                    ltrim($request->getPathInfo(), '/'),
                    $rewriteToOtherStore->getRequestPath(),
                    $url
                );
            }
        }

        return $url;
    }

    protected function needShowRedirectionPopup()
    {
        $storeId = $this->storeManager->getStore()->getId();
        $needPopup = $this->scopeConfig->getValue(
            'amgeoipredirect/general/redirection_decline',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        if ($needPopup && $this->sessionManager->getNeedShow() !== false) {
            $this->sessionManager->setNeedShow(true);

            return true;
        }

        return false;
    }

    /**
     * @param StoreInterface $store
     * @return bool
     */
    protected function _setDefaultLocale($store)
    {
        if ($store->getId()) {
            $localeCode = $this->scopeConfig->getValue(
                'general/locale/code',
                ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
            $this->resolver->setDefaultLocale($localeCode)->setLocale($localeCode);
        } else {
            return false;
        }
    }

    /**
     * @param $country
     * @param $scopeStores
     * @param $currentStoreId
     * @return $this
     */
    protected function _setNewCurrencyIfExist($country, $scopeStores, $currentStoreId)
    {
        $currencyMapping = $this->serializer->unserialize(
            $this->scopeConfig->getValue(
                'amgeoipredirect/country_currency/currency_mapping',
                $scopeStores,
                $currentStoreId
            )
        );

        foreach ($currencyMapping as $countries => $currency) {
            if (strpos($countries, $country) !== false
                && $this->storeManager->getStore()
                    ->getCurrentCurrencyCode() != $currency
            ) {
                $this->sessionManager->setAmYetRedirectCurrency(1);
                $this->storeManager->getStore()->setCurrentCurrencyCode($currency);
            }
        }

        return $this;
    }

    /**
     * @param RequestInterface $request
     * @param $checkStore
     * @return bool
     */
    protected function _compareEqualUrlFromStore($request, $checkStore)
    {
        if (version_compare($this->magentoVersion->get(), '2.3.0', '>=')) {
            $param = StoreManagerInterface::PARAM_NAME;
        } else {
            $param = StoreResolverInterface::PARAM_NAME;
        }
        if ($request->getParam($param)) {
            return ($checkStore
                && ($request->getParam($param) != $checkStore->getCode()))
                ? false : true;
        }

        return false;
    }

    /**
     * Is redirect allowed
     *
     * @param $request
     * @return mixed|string
     * @throws NoSuchEntityException
     */
    protected function applyLogic($request)
    {
        $applyLogic = $this->scopeConfig->getValue('amgeoipredirect/restriction/apply_logic');
        $currentUrl = $this->urlBuilder->getCurrentUrl();

        switch ($applyLogic) {
            case Logic::SPECIFIED_URLS:
                $acceptedUrls = explode(
                    PHP_EOL,
                    $this->scopeConfig->getValue('amgeoipredirect/restriction/accepted_urls')
                );

                foreach ($acceptedUrls as $url) {
                    $url = trim($url);

                    if ($url && $currentUrl && $this->_compareUrls($currentUrl, $url)) {
                        $this->redirectAllowed = true;
                        return $url;
                    }
                }
                break;
            case Logic::HOMEPAGE_ONLY:
                if ($this->isHomePage($request)) {
                    $this->redirectAllowed = true;
                }
                break;
            default:
                $exceptedUrls = explode(
                    PHP_EOL,
                    $this->scopeConfig->getValue('amgeoipredirect/restriction/excepted_urls')
                );

                foreach ($exceptedUrls as $url) {
                    $url = trim($url);

                    if ($url && $currentUrl && strpos($currentUrl, $url) !== false) {
                        $this->redirectAllowed = false;

                        return $url;
                    } else {
                        $this->redirectAllowed = true;
                    }
                }
        }

        return '';
    }

    /**
     * @param string $currentUrl
     * @param string $comapareUrl
     * @return bool
     */
    protected function _compareUrls($currentUrl, $comapareUrl)
    {
        $urlParse = $this->urlProcessor->parseUrl($comapareUrl);
        $currentUrlParse = $this->urlProcessor->parseUrl($currentUrl);

        return (is_array($urlParse)
            && is_array($currentUrlParse)
            && (!isset($urlParse['host']) || $urlParse['host'] === $currentUrlParse['host'])
            && (
                !isset($urlParse['path']) && !isset($currentUrlParse['path'])
                || (isset($urlParse['path'])
                    && isset($currentUrlParse['path'])
                    && str_replace('/', '', $urlParse['path']) === str_replace('/', '', $currentUrlParse['path']))
            )
        );
    }

    /**
     * @param RequestInterface $request
     *
     * @return bool
     */
    protected function isHomePage($request)
    {
        $cloneRequest = clone $request;
        $this->baseRouter->match($cloneRequest);

        return $cloneRequest->getFullActionName() === self::HOME;
    }

    /**
     * Is redirect by GeoIP has not needed
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    protected function isNeedToProceed($request)
    {
        if ($this->isIpRestricted($request)
            || $request->isXmlHttpRequest()
        ) {
            return true;
        }

        $isApi = $request->getControllerModule() == 'Mage_Api';

        if ($isApi || !$this->geoipHelper->isModuleOutputEnabled('Amasty_Geoip')) {
            return true;
        }

        $userAgent = $request->getHeader('USER_AGENT');
        $userAgentsIgnore = $this->scopeConfig->getValue('amgeoipredirect/restriction/user_agents_ignore');

        if ($userAgent && !empty($userAgentsIgnore)) {
            $userAgentsIgnore = array_map("trim", explode(',', $userAgentsIgnore));

            foreach ($userAgentsIgnore as $agent) {
                if ($agent && stripos($userAgent, $agent) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * is IP in restricted list
     *
     * @param RequestInterface $request
     *
     * @return bool
     */
    private function isIpRestricted($request)
    {
        $ipRestriction = $this->scopeConfig->getValue('amgeoipredirect/restriction/ip_restriction');
        $currentIp = $this->getCurrentIp($request);

        if ($currentIp && !empty($ipRestriction)) {
            $ipRestriction = array_map("rtrim", explode(PHP_EOL, $ipRestriction));

            foreach ($ipRestriction as $ip) {
                if ($currentIp == $ip) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @param RequestInterface $request
     * @return string
     */
    private function getCurrentIp($request)
    {
        foreach ($this->addressPath as $path) {
            $ip = $request->getServer($path);

            if ($ip) {
                if (strpos($ip, ',') !== false) {
                    $addresses = explode(',', $ip);
                    foreach ($addresses as $address) {
                        if (trim($address) != self::LOCAL_IP) {
                            return trim($address);
                        }
                    }
                } else {
                    if ($ip != self::LOCAL_IP) {
                        return $ip;
                    }
                }
            }
        }

        return $this->remoteAddress->getRemoteAddress();
    }

    /**
     * @param $userIp
     * @return bool
     * @throws LocalizedException
     */
    private function isIpBlock($userIp)
    {
        $websiteId = $this->storeManager->getWebsite()->getId();
        $configIpsToBlock = $this->scopeConfig->getValue("amgeoipredirect/restrict_by_ip/ip_to_block");
        $websiteIpsToBlock = $this->scopeConfig->getValue(
            "amgeoipredirect/restrict_by_ip/ip_to_block",
            ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        if (empty($websiteIpsToBlock) && empty($configIpsToBlock)) {
            return false;
        }

        $ipsWeb = preg_split('/\n|\r\n?/', $websiteIpsToBlock);
        $ipsConfig = preg_split('/\n|\r\n?/', $configIpsToBlock);
        $ips = array_unique(array_merge($ipsWeb, $ipsConfig));

        foreach ($ips as $ip) {
            if (trim($ip) === $userIp) {
                return true;
            }
        }

        return false;
    }
}
