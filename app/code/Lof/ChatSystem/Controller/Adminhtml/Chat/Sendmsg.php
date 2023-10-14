<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/terms
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_ChatSystem
 * @copyright  Copyright (c) 2021 Landofcoder (https://www.landofcoder.com/)
 * @license    https://landofcoder.com/terms
 */

namespace Lof\ChatSystem\Controller\Adminhtml\Chat;

use Magento\Framework\App\Action\Context;

class Sendmsg extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * @var \Magento\Framework\Controller\Result\RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Lof\ChatSystem\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Controller\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var \Lof\ChatSystem\Model\ChatMessage
     */
    protected $_message;

    /**
     * @var \Lof\ChatSystem\Model\Sender
     */
    protected $sender;

    /**
     * @var \Lof\ChatSystem\Model\ChatFactory
     */
    protected $_chatModelFactory;

    /**
     * Sendmsg constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Lof\ChatSystem\Helper\Data $helper
     * @param \Lof\ChatSystem\Model\ChatMessage $message
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Lof\ChatSystem\Model\Sender $sender
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\ChatSystem\Model\ChatFactory $chatModelFactory
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\ChatSystem\Helper\Data $helper,
        \Lof\ChatSystem\Model\ChatMessage $message,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Lof\ChatSystem\Model\Sender $sender,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\ChatSystem\Model\ChatFactory $chatModelFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_message = $message;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_customerSession = $customerSession;
        $this->_request = $context->getRequest();
        $this->sender = $sender;
        $this->_chatModelFactory = $chatModelFactory;
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $data = $this->_request->getPostValue();
        $data['current_time'] = $this->_helper->getCurrentTime();
        $data = $this->_helper->xss_clean_array($data);
        if (!empty($data)) {
            $message = $this->_message;

            try {
                $message->setData($data)->save();
                $chat = $this->_chatModelFactory->create()->load($data['chat_id']);
                $number_message = $chat->getData('number_message') + 1;
                $chat->setUserName($data['user_name'])->setData('is_read', 3)->setData(
                    'answered',
                    0
                )->setData('number_message', $number_message)->save();
                $this->_cacheTypeList->cleanType('full_page');
                if ($data['customer_name'] && $this->_helper->getConfig('email_settings/enable_email')) {
                    $data['url'] = $this->_helper->getUrl();
                    $this->sender->sendAdminChat($data);
                }
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t process your request right now. Sorry, that\'s all we know.')
                );
                return;
            }
        }
    }
}
