<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Satish\Manage\Controller\Rewrite;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class CustomerIndex extends \Magento\Customer\Controller\AbstractAccount implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {



        // redirect if seller
        $_objectManager = \Magento\Framework\App\ObjectManager::getInstance(); 
        $storeManager = $_objectManager->get('Magento\Store\Model\StoreManagerInterface'); 
        $storeCode = $storeManager->getStore()->getCode();
        if($storeCode=='seller'){


            $resultRedirect = $this->resultRedirectFactory->create();
              $resultRedirect->setPath('/*/*');
            return $resultRedirect;

        }
        // redirect if seller end
        return $this->resultPageFactory->create();
    }
}
