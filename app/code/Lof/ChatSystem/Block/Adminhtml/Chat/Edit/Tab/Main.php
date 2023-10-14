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

namespace Lof\ChatSystem\Block\Adminhtml\Chat\Edit\Tab;

class Main extends \Magento\Framework\View\Element\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var string
     */
    protected $_template = 'Lof_ChatSystem::chat/chat.phtml';

    /**
     * @var string
     */
    protected $_columnDate = 'main_table.created_at';

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Lof\ChatSystem\Model\ChatMessage
     */
    protected $messsage;

    /**
     * @var \Lof\ChatSystem\Model\ChatFactory
     */
    protected $_chatModelFactory;

    /**
     * Main constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Lof\ChatSystem\Model\ChatMessage $messsage
     * @param \Lof\ChatSystem\Model\ChatFactory $chatModelFactory
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Lof\ChatSystem\Model\ChatMessage $messsage,
        \Lof\ChatSystem\Model\ChatFactory $chatModelFactory
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->formKey = $context->getFormKey();
        $this->authSession = $authSession;
        $this->messsage = $messsage;
        $this->_chatModelFactory = $chatModelFactory;
    }

    /**
     * @return mixed|null
     */
    public function getCurrentChat()
    {
        return $this->_coreRegistry->registry('lofchatsystem_chat');
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

    /**
     * @return \Magento\User\Model\User|null
     */
    public function getUser()
    {
        $user = $this->authSession->getUser();
        return $user;
    }

    /**
     *
     */
    public function isRead()
    {
        $chat = $this->_chatModelFactory->create()->load($this->getCurrentChat()->getData('chat_id'));
        //$messsage = $objectManager->create('Lof\ChatSystem\Model\ChatMessage')->load()->getCollection();
        $messsage = $this->messsage->getCollection()->addFieldToFilter(
            'chat_id',
            $this->getCurrentChat()->getData('chat_id')
        )->addFieldToFilter('is_read', 1);
        foreach ($messsage as $key => $_messsage) {
            $_messsage->setData('is_read', 0)->save();
        }

        $chat->setData('is_read', 0)->save();

        return;
    }
}
