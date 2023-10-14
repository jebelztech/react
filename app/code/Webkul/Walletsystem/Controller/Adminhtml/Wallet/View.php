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

use Magento\Backend\App\Action;
use Webkul\Walletsystem\Controller\Adminhtml\Wallet as WalletController;
use Magento\Framework\Controller\ResultFactory;
use Webkul\Walletsystem\Model\WallettransactionFactory;

/**
 * Webkul Walletsystem Class
 */
class View extends WalletController
{

    protected $walletTransaction;

    /**
     * Initialize dependencies
     *
     * @param Action\Context $context
     * @param WallettransactionFactory $transactionFactory
     */
    public function __construct(
        Action\Context $context,
        WallettransactionFactory $transactionFactory
    ) {
        $this->walletTransaction = $transactionFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $params = $this->getRequest()->getParams();
        if (is_array($params) && array_key_exists('entity_id', $params) && $params['entity_id']!='') {
            $walletTransactionModel = $this->walletTransaction->create()
                ->load($params['entity_id']);
            if (!empty($walletTransactionModel) && $walletTransactionModel->getEntityId()) {
                $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
                $resultPage->setActiveMenu('Webkul_Walletsystem::walletsystem');
                $resultPage->getConfig()->getTitle()->prepend(__('Wallet System Transaction View'));
                $resultPage->addBreadcrumb(__('Wallet System Transaction View'), __('Wallet System Transaction View'));
                return $resultPage;
            }
        }
        $this->messageManager->addError(
            __('Transaction not exists.')
        );
        return $resultRedirect->setPath('walletsystem/wallet/index');
    }
}
