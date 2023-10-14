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
//use \Magento\Sales\Model\Order\Email\Sender‌​\OrderSender;

 
class OrderObserver implements ObserverInterface
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

    public function __construct(
    	    StateInterface $inlineTranslation,
    	    TransportBuilder $transportBuilder,
    	    LoggerInterface $logLoggerInterface,
    	    ScopeConfigInterface $scopeConfig,
            StoreRepositoryInterface $repository,
            \Magento\Framework\Filesystem\DirectoryList $dir,
    	    array $data = []
        )
    {
        $this->repository = $repository;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->_logLoggerInterface = $logLoggerInterface;
        $this->scopeConfig = $scopeConfig;
        $this->dir = $dir;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
    	try
        {
            // Send Mail
            $order = $observer->getEvent()->getOrder();
            $orderId = $order->getId();
            $shippingAddress = $order->getShippingAddress();
            $city = $shippingAddress->getCity();
            $country_id = $shippingAddress->getCountryId();
            $state = $shippingAddress->getRegion();
            $storeData = $this->locationList($state,$city);
            $email_id = "order@alsultansweets.ae";
            $template_id = 6;
            $stores = $this->repository->getList();
            $this->_logLoggerInterface->debug("requested store: ".$storeData['store_name']);
            foreach ($stores as $store) {
                if($store->getName()==$storeData['store_name']) {
                    $this->_logLoggerInterface->debug("matched store: ".$store->getName());
                    $order->setStoreId($store->getId());
                    $order->save();
                    break;
                }
                //echo $store->getName()."<br/>";
            }
            $this->_sendEmail($template_id,$order,$storeData);
        } catch(\Exception $e){
            $this->_logLoggerInterface->debug("error: ".$e->getMessage());
            //exit;   
        }
     }
    public function _sendEmail($template_id,$order,$storeData) {
        try {
            if($storeData['store_email']!="" && $storeData['store_name']!="") {
                //$email_id = "mohammad.n@ezmartech.com";
                $email_id = $storeData['store_email'];
                $sendTo[] = array('email' => $email_id, 'name' => $storeData['store_name']);
                $send_from = array('email' => "order@alsultan.com", 'name' => 'New order received('.$order->getIncrementId().')');
                $bcc = array();
                $shippingMethod = '';
                if ($shippingInfo = $order->getShippingAddress()->getShippingMethod()) {
                    $data = explode('_', $shippingInfo);
                    $shippingMethod = $data[0];
                }
                $shippingMethod = $order->getShippingDescription();
                $paymentMethod = '';
                if ($paymentInfo = $order->getPayment()) {
                    $paymentMethod = $paymentInfo->getMethod();
                }
                $total = $order->getGrandTotal();
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $formatedAddress = $objectManager->get('\Magento\Sales\Model\Order\Address\Renderer');
                $dateTimeZone = (new \DateTime($order->getCreatedAt()));
                foreach ($sendTo as $recipient) {
                    $transport = $this->transportBuilder
                                    ->setTemplateIdentifier($template_id)
                                    ->setTemplateOptions(array('area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $order->getStoreId(), 'order_id' => $order->getId()))
                                    ->setTemplateVars(
                                        array(
                                            'customer_name' => $storeData['store_name'],
                                            'order_id' => $order->getId(),
                                            'increment_id' => $order->getIncrementId(),
                                            'created_at' => $order->getCreatedAt(),
                                            'billing_address' => $formatedAddress->format($order->getBillingAddress(), 'html'),
                                            'shipping_address' => $formatedAddress->format($order->getShippingAddress(), 'html'),
                                            'dateAndTime' =>  $dateTimeZone->format('Y/m/d H:i:s'), 
                                            'customer' => $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname(), 
                                            'customerEmail' => $order->getCustomerEmail(), 
                                            'shippingMethod' => $shippingMethod,
                                            'paymentMethod' => $this->scopeConfig->getValue('payment/' . $paymentMethod . '/title', \Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                                            'total' => $total
                                        )
                                    )
                                    ->setFrom($send_from)
                                    ->addTo($recipient['email'], $recipient['name'])
                                    ->getTransport();
                    $transport->sendMessage();
                    //echo "email sent to ".$recipient['name']." - ".$recipient['email']."<br/>";
                }
            }
        }
        catch (\Exception $exception) {
            //echo 'email not sent.......'; //exit;
            $this->_logLoggerInterface->debug( $exception->getMessage() );
            //exit;
            //log failed email send operation
            //$this->_logger->critical($exception->getMessage());
        }
    }
    public function locationList($location_state,$location_city) {
        $shipping_rates_file = $this->dir->getPath('var')."/custom_shipping_rates/shipping_rates.csv";
        //echo $shipping_rates_file."<br/>"; 
        $location_list = array();
        if(file_exists($shipping_rates_file)) {
            //echo "<br/>file exists ........<br/>"; 
            $handle = fopen($shipping_rates_file, "r");
            $i=0;
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                //if($i++<2) continue;
                $country = $data[0];
                $region_id = $data[1];
                $city = $data[2];
                $minimum = $data[3];
                $price = $data[4];
                $store_name = $data[5];
                $store_email = $data[6];
                $store_phone = $data[7];
                if($location_state==$region_id && $location_city==$city) {
                    $location_list = array('country'=>$country,'region_id'=>$region_id,'city'=>$city,'minimum'=>$minimum,'price'=>$price,'store_name'=>$store_name,'store_email'=>$store_email,'store_phone'=>$store_phone);
                    break;
                }
                /*$loc = array('country'=>$country,'region_id'=>$region_id,'city'=>$city,'minimum'=>$minimum,'price'=>$price,'store_name'=>$store_name,'store_email'=>$store_email,'store_phone'=>$store_phone);
                echo "<pre>"; print_r($loc); echo "</pre>";
                $location_list[] = $loc;*/
                
            }
        }
        else {
            $this->_logLoggerInterface->debug("file not exists ........");
        }
        return $location_list;
    }
}