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

namespace Lof\ChatSystem\Block\Adminhtml\Notifications;

class Chat extends \Magento\Framework\View\Element\Template
{

    /**
     * @var \Lof\ChatSystem\Model\ChatMessage
     */
    protected $message;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Lof\ChatSystem\Model\ChatMessage $message
    ) {
        $this->message = $message;
        parent::__construct($context);
    }

    /**
     * @return int|void
     */
    public function countUnread()
    {
        $message = $this->message->getCollection()->addFieldToFilter(
            'user_id',
            ['null' => true]
        )->addFieldToFilter('is_read', 1);
        return count($message);
    }
}
