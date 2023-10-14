<?php

namespace Etail\Alsultan\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Store\Api\StoreRepositoryInterface;

class Index extends Action
{
    protected $dir;
    private $resultPageFactory;
    private $repository;
    public function __construct(
        Context $context,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        StoreRepositoryInterface $repository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->dir = $dir;
        $this->resultPageFactory = $resultPageFactory;
    }
    public function execute()
    {
        $stores = $this->repository->getList();
        foreach ($stores as $store) {
            echo $store->getName()."<br/>";
        }
        $shipping_rates_file = $this->dir->getPath('var')."/custom_shipping_rates/shipping_rates.csv";
        $handle = fopen($shipping_rates_file, "r");
        $i=0;
        while (($data = fgetcsv($handle)) !== FALSE) {
            if($i++<2) continue;
            $country = $data[0];
            $retion = $data[1];
            $city = $data[2];
            $minimum = $data[3];
            $price = $data[4];
            echo "<pre>"; print_r($data); echo "</pre>";
            //var_dump($data);
        }
        echo "testing......";
        /*$this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();*/
    }
}