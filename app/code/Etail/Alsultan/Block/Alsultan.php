<?php

namespace Etail\Alsultan\Block;

class Alsultan extends \Magento\Framework\View\Element\Template {
    protected $storeManager;
    protected $collection;
    protected $request;
    protected $_productCollectionFactory;
    private $categoryManagement;
    protected $_categoryFactory;
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Catalog\Block\Product\Context $context, 
        \Magento\Catalog\Api\CategoryManagementInterface $categoryManagement,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->categoryManagement = $categoryManagement;
        $this->_categoryFactory = $categoryFactory; 
    }
    protected function _prepareLayout()
    {
        return parent::_prepareLayout();
    }
    public function getItemsCount($salelist_id) {
        $collection = $this->_productCollectionFactory->create();
        $collection->addAttributeToFilter('salelist_list',$salelist_id);
        return $collection->getSize();
    }
    public function getResultFromCount() {
        $p = 1;
        if($this->request->getParam('p')) {
            $p = ( $this->request->getParam('p') * 10 ) + 1;
        }
        return $p;
    }
    public function getResultToCount() {
        $p = 1;
        if($this->request->getParam('p')) {
            $p = ( $this->request->getParam('p') * 10 ) + 10;
        }
        if($p >= $this->collection->getSize()) {
            $p = $this->collection->getSize();
        }
        return $p;
    }
    public function getStoreData() {
        $storeManagerDataList = $this->storeManager->getStores();
        $options = array();
        $options["default"] = 'UAE';
        $options["sh_en"] = 'Sharjah';
        $options["br_en"] = 'Bahrain';
        $options["tr_en"] = 'Turkey';
        $options["us_en"] = 'International';
        /*foreach ($storeManagerDataList as $key => $value) {
            //$options[] = ['label' => $value['name'].' - '.$value['code'], 'value' => $key];
            $options[$value['code']] = $value['name'];
        }*/
        $options['current'] = $this->storeManager->getStore()->getCode();
        return $options;
    }
    public function getStoreRedirectUrl($current) {
        $storeCode = $this->storeManager->getStore()->getCode();
        return $this->storeManager->getStore()->getBaseUrl()."stores/store/redirect/___store/".$current."/___from_store/".$storeCode;
    }
    public function getCategory($categoryId)
    {
        $category = $this->_categoryFactory->create()->load($categoryId);
        return $category;
    }
    public function getCategoryTree()
    {
        $allCategoryId = 59;
        try {
            $categoryTree = $this->categoryManagement->getTree($allCategoryId);
            //if($this->request->getParam('cat')) {
                //$cats = $this->getCategory($allCategoryId)->getAllChildren(true);
            $parent_cats = explode(',',$this->getCategory($allCategoryId)->getChildren());
            $parent_cats = $this->getCategory($allCategoryId)->getChildrenCategories();
            $category_tree = array();
            foreach($parent_cats as $parent) {
                $parentcat_url = $parent->getUrl();
                $parentcat_name = $parent->getName();
                $category_tree[$parent->getId()] = array('url'=>$parentcat_url,'name'=>$parentcat_name);
                //$category_tree[$parent->getId()] = $parent->getData();
                $sub_cats = $this->getCategory($parent->getId())->getChildrenCategories();
                foreach($sub_cats as $sub_cat) {
                    $subcat_url = $sub_cat->getUrl();
                    $subcat_name = $sub_cat->getName();
                    $category_tree[$parent->getId()]['categories'][$sub_cat->getId()] = array('url'=>$subcat_url,'name'=>$subcat_name);
                    $categoryProducts = $sub_cat->getProductCollection()->addAttributeToSelect('*');
                    foreach($categoryProducts as $product) {
                        $product_url = $product->getProductUrl();
                        $product_name = $product->getName();
                        $category_tree[$parent->getId()]['categories'][$sub_cat->getId()]['products'][$product->getId()] = array('url'=>$product_url,'name'=>$product_name);
                    }
                }
            }
            return $category_tree;
                /*echo "<pre>";
                print_r($category_tree);
                exit;
                echo "<pre>"; print_r($parent_cats); exit;
                foreach($categoryTree as $cat) {
                    echo "<pre>"; print_r($cat['children_data']);
                }
                exit;*/
            //}
            /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/Rohan.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info(print_r($categoryTree->debug(),true)); // Here, You will get category tree array object in log file.*/
        } catch (\Exception $exception) {
            throw new \Exception($exception->getMessage());
        }
    }
}