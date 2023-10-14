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

namespace Lof\ChatSystem\Controller\Chat;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Msglog extends \Magento\Framework\App\Action\Action
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
     * @var \Lof\ChatSystem\Model\Chat
     */
    protected $chat;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * Msglog constructor.
     * @param Context $context
     * @param \Magento\Store\Model\StoreManager $storeManager
     * @param PageFactory $resultPageFactory
     * @param \Lof\ChatSystem\Helper\Data $helper
     * @param \Lof\ChatSystem\Model\ChatMessage $message
     * @param \Lof\ChatSystem\Model\Chat $chat
     * @param \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        Context $context,
        \Magento\Store\Model\StoreManager $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Lof\ChatSystem\Helper\Data $helper,
        \Lof\ChatSystem\Model\ChatMessage $message,
        \Lof\ChatSystem\Model\Chat $chat,
        \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->chat = $chat;
        $this->resultPageFactory = $resultPageFactory;
        $this->_helper = $helper;
        $this->_message = $message;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->_coreRegistry = $registry;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_customerSession = $customerSession;
        $this->_request = $context->getRequest();
        parent::__construct($context);
    }

    /**
     * Default customer account page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $chat = $this->chat->load($this->_helper->getIp(), 'ip');

        if ($this->_customerSession->getCustomer()->getEmail()) {
            $message = $this->_message->getCollection()->addFieldToFilter(
                'customer_email',
                $this->_customerSession->getCustomer()->getEmail()
            );
        } else {
            $message = $this->_message->getCollection()->addFieldToFilter('chat_id', $chat->getId());
        }
        $count = count($message);
        $i = 0;

        foreach ($message as $key => $_message) {
            $i++;
            $date_sent = $_message['created_at'];
            $day_sent = substr($date_sent, 8, 2);
            $month_sent = substr($date_sent, 5, 2);
            $year_sent = substr($date_sent, 0, 4);
            $hour_sent = substr($date_sent, 11, 2);
            $min_sent = substr($date_sent, 14, 2);
            $body_msg = $this->_helper->xss_clean($_message['body_msg']);
            if (!$_message['user_id']) {
                print '<div class="msg-user">
                        <p>' . $body_msg . '</p>
                        <div class="info-msg-user">
                            ' . __("You") . '
                        </div>
                    </div> ';

            } else {

                print '<div class="msg">
                    <p>' . $body_msg . '</p>
                    <div class="info-msg">
                        ' . $_message['user_name'] . '
                    </div>
                </div>';
                if ($count == $i) {
                    echo "
                    <script>require(['jquery'],function($) { $('.chat-message-counter').css('display','inline'); });</script>
                    ";
                }
            }
        }
        exit;
    }
}
