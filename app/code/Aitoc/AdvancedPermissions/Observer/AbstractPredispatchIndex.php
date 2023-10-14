<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Observer;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * @event eav_collection_abstract_load_before
 */
abstract class AbstractPredispatchIndex
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    public function __construct(
        Data $helper,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->helper = $helper;
        $this->url = $url;
        $this->responseFactory = $responseFactory;
        $this->request = $request;
    }

    /**
     * @param Observer $observer
     * @param $urlPath
     * @param array $additionalParams
     * @return $this
     */
    protected function redirectIfNeeded(\Magento\Framework\Event\Observer $observer, $urlPath, $additionalParams = [])
    {
        if ($this->helper->isHideAllStoreViews() && !$this->request->getParam('store')) {
            $availableStoreIds = $this->helper->getAllowedStoreIds();

            if (isset($availableStoreIds[0])) {
                $params = ['store' => $availableStoreIds[0]] + $additionalParams;
                $redirectionUrl = $this->url->getUrl($urlPath, $params);
                $observer->getControllerAction()
                    ->getResponse()
                    ->setRedirect($redirectionUrl);
            }
        }

        return $this;
    }
}
