<?php
namespace Etail\Alsultan\Plugin;
use \Magento\Framework\Search\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Page as ResultPage;
class RegionFilter
{
    private $product;
    private $coreRegistry;
    protected $_storeManager;
    protected $scopeConfig;
    public function __construct( 
        \Magento\Catalog\Model\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->_storeManager = $storeManager; 
        $this->coreRegistry = $context->getRegistry();
        $this->product = $product;
        $this->scopeConfig = $scopeConfig;
    }
    public function afterToOptionArray($subject, $options) {
        $store_code = $this->_storeManager->getGroup()->getCode();
        $dubai_stores = array('Abu Dhabi','Ajman','Al Ain','Dubai','Fujairah','Ras Al Khaimah','Umm Al Quwain');
        $sharjah_stores = array("Sharjah");
        foreach($options as $key=>$option) {
            if($store_code=="dubai_store" && isset($option['label']) && !in_array($option['label'],$dubai_stores)) unset($options[$key]);
            if($store_code=="sharjah_store" && isset($option['label']) && !in_array($option['label'],$sharjah_stores)) unset($options[$key]);
        }
        return $options;
    }
}
