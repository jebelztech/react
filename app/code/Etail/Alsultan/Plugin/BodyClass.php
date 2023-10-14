<?php
namespace Etail\Alsultan\Plugin;
use \Magento\Framework\Search\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\View\Result\Page as ResultPage;
class BodyClass
{
    private $product;
    private $coreRegistry;
    protected $_storeManager;
    public function __construct( 
        \Magento\Catalog\Model\Product $product,
        \Magento\Store\Model\StoreManagerInterface $storeManager, 
        \Magento\Catalog\Block\Product\Context $context,
        array $data = []
    ) {
        $this->_storeManager = $storeManager; 
        $this->coreRegistry = $context->getRegistry();
        $this->product = $product;
    }
    public function beforeRenderResult(
        ResultPage $subject,
        ResponseInterface $response
    ) : array {
        /*if($this->coreRegistry->registry('current_product')) {
            $curPro = $this->coreRegistry->registry('current_product');
            $auctionOpt = $curPro->getAuctionType();
            $auctionOpt = explode(',', $auctionOpt);
            if (in_array(2, $auctionOpt)) {
                $subject->getConfig()->addBodyClass('auction-product');
            }
            else {
                $subject->getConfig()->addBodyClass('catalog-product');
            }
        }*/
        $subject->getConfig()->addBodyClass($this->_storeManager->getStore()->getCode());
        return [$response];
    }
}
