<?php
/**
 * Webkul Software
 *
 * @category Webkul
 * @package Webkul_Walletsystem
 * @author Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license https://store.webkul.com/license.html
 */

namespace Webkul\Walletsystem\Controller\Transfer;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;

/**
 * Webkul Walletsystem Class
 */
class RequestDelete extends \Magento\Framework\App\Action\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Initialize dependencies
     *
     * @param Context     $context
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Webkul\Walletsystem\Model\AccountDetails $accountDetails
    ) {
        $this->accountDetails = $accountDetails;
        parent::__construct($context);
    }

    /**
     * Controller execute function
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($this->getRequest()->getParams('id')) {
            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $this->accountDetails->load($id)
                                    ->setRequestToDelete('1')
                                    ->save();
                $this->messageManager->addSuccess('Request Has Been Submitted To Admin');
            } else {
                $this->messageManager->addWarning('Please check the data entered');
            }
        } else {
            $this->messageManager->addWarning('Please check the data entered');
        }

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }
}
