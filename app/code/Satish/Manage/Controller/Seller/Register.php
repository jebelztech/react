<?php

namespace Satish\Manage\Controller\Seller;

class Register extends \Magento\Framework\App\Action\Action
{
    public function execute()
    {
		//die("aa");
		
        $this->_view->loadLayout();
        $this->_view->getLayout()->initMessages();
        $this->_view->renderLayout();
    }
}