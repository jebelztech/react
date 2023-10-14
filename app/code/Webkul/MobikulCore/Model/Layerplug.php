<?php
 
namespace Webkul\MobikulCore\Model;
 
class Layerplug extends \Magento\Catalog\Model\Product
{
    public function beforePrepareProductCollection(
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection $collection
    ) {
        $this->setSortParams($layer, $collection);
    }
    
    public function setSortParams($layer,$collection)
    {
        if(strpos($_SERVER['REQUEST_URI'],'mobikulhttp') == true){
        }
    }
}
