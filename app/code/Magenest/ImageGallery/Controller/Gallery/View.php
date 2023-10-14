<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

namespace Magenest\ImageGallery\Controller\Gallery;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class GetListImage
 * @package Magenest\ImageGallery\Controller\Gallery
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_scopeConfig = $scopeConfig;
        $this->url = $url;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {

        $enable = $this->_scopeConfig->getValue('imagegallery/gallerypage/enablefullgallery', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        if ($enable == 0)
            {
                $norouteUrl = $this->url->getUrl('noroute');
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($norouteUrl);
                return $resultRedirect;
            }
        else
        {
            $resultPage = $this->resultPageFactory->create();
            return $resultPage;
        }

    }
}