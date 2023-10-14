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

namespace Lof\ChatSystem\Block\Chat;

class Chat extends \Magento\Framework\View\Element\Template
{
    /**
     * @var int
     */
    private $_username = -1;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Lof\ChatSystem\Model\Chat
     */
    protected $chat;

    /**
     * @var \Lof\ChatSystem\Helper\Data
     */
    protected $helper;

    /**
     * @var \Lof\ChatSystem\Helper\Url
     */
    protected $_customerUrl;

    /**
     * Chat constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Lof\ChatSystem\Helper\Url $customerUrl
     * @param \Lof\ChatSystem\Helper\Data $helper
     * @param \Lof\ChatSystem\Model\Chat $chat
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Lof\ChatSystem\Helper\Url $customerUrl,
        \Lof\ChatSystem\Helper\Data $helper,
        \Lof\ChatSystem\Model\Chat $chat,
        array $data = []
    ) {
        $this->helper = $helper;
        $this->chat = $chat;
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
        parent::__construct($context, $data);
    }

    /**
     * @return bool
     */
    public function isLogin()
    {
        if ($this->_customerSession->isLoggedIn()) {
            return true;
        }
        return false;
    }

    /**
     * @return array|mixed|null
     */
    public function getChatId()
    {
        if ($this->isLogin()) {
            $chat = $this->chat->getCollection()->addFieldToFilter(
                'customer_email',
                $this->getCustomer()->getData('email')
            );
            if (count($chat) > 0) {
                $chat_id = $chat->getFirstItem()->getData('chat_id');
            } else {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $chatModel = $objectManager->create('Lof\ChatSystem\Model\Chat');

                $chatModel->setCustomerId($this->getCustomerSession()->getCustomerId())->setCustomerName($this->getCustomer()->getData('firstname') . ' ' . $this->getCustomer()->getData('lastname'))->setCustomerEmail($this->getCustomer()->getData('email'));
                $chatModel->save();
                $chat_id = $chatModel->getData('chat_id');
            }
        } else {
            $chat = $this->chat->getCollection()->addFieldToFilter('ip', $this->helper->getIp());
            if (count($chat) > 0) {
                $chat_id = $chat->getFirstItem()->getData('chat_id');
            } else {
                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $chatModel = $objectManager->create('Lof\ChatSystem\Model\Chat');
                $chatModel->setIp($this->helper->getIp());
                $chatModel->save();
                $chat_id = $chatModel->getData('chat_id');
            }
        }
        return $chat_id;
    }

    /**
     * @return string
     */
    public function getCurrentUrl()
    {
        return $this->_urlBuilder->getCurrentUrl();
    }

    /**
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->getCustomerSession()->getCustomer();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        $post_action_url = $this->_customerUrl->getLoginPostUrl();
        $post_action_url = str_replace("/lofchatsystem/", "/", $post_action_url);
        return $post_action_url;
    }

    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->_customerUrl->getForgotPasswordUrl();
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername(true);
        }
        return $this->_username;
    }

    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled()
    {
        return ( bool )!$this->_scopeConfig->getValue(
            \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
}
