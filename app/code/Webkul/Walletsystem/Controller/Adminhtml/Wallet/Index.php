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

namespace Webkul\Walletsystem\Controller\Adminhtml\Wallet;

use Webkul\Walletsystem\Controller\Adminhtml\Wallet as WalletController;
use Magento\Framework\Controller\ResultFactory;

/**
 * Webkul Walletsystem Class
 */
class Index extends WalletController
{
    /**
     * Controller Execute function
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Webkul_Walletsystem::walletsystem');
        $resultPage->getConfig()->getTitle()->prepend(__('Wallet System Details'));
        $resultPage->addBreadcrumb(__('Wallet System Details'), __('Wallet System Details'));
        return $resultPage;
    }
}
