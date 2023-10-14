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
use Magento\Backend\App\Action;
use Webkul\Walletsystem;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\Walletsystem\Model\Wallettransaction;

/**
 * Webkul Walletsystem Class
 */
class MassupdateBanktransfer extends WalletController
{
    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var Walletsystem\Model\ResourceModel\Walletcreditrules\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Initialize dependencies
     *
     * @param Action\Context                                           $context
     * @param Filter                                                   $filter
     * @param Walletsyste\Api\WalletTransactionRepositoryInterface                 $creditRuleRepository
     * @param Walletsyste\Model\ResourceModel\Wallettransaction\CollectionFactory $collectionFactory
     */
    public function __construct(
        Action\Context $context,
        \Webkul\Walletsystem\Helper\Mail $mailHelper,
        Filter $filter,
        Walletsystem\Model\ResourceModel\Wallettransaction\CollectionFactory $collectionFactory
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->mailHelper = $mailHelper;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Mass Update action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $resultRedirect = $this->resultRedirectFactory->create();
            $data = $this->getRequest()->getParams();
            
            if (isset($data['selected'])) {
                $selected = count($data['selected']);
            } else {
                $selected = __("All Selected");
            }
            
            $status = Wallettransaction::WALLET_TRANS_STATE_APPROVE;
            $collection = $this->filter->getCollection($this->collectionFactory->create())
                                        ->addFieldToFilter('bank_details', ["neq" => ""]);
            $totalCount = $collection->getSize();
            $collection = $collection->addFieldToFilter('status', Wallettransaction::WALLET_TRANS_STATE_PENDING);
            $entityIds = $collection->getAllIds();
            $pendingTransCount = count($entityIds);
            if (!empty($entityIds)) {
                $coditionArr = [];
                foreach ($entityIds as $key => $id) {
                    $condition = "`entity_id`=".$id;
                    array_push($coditionArr, $condition);
                }
                $coditionData = implode(' OR ', $coditionArr);
                $creditRuleCollection = $this->collectionFactory->create();
                $creditRuleCollection->setTableRecords(
                    $coditionData,
                    ['status' => $status]
                );
                /**
                 * Send mail to all approved transactions
                 * @param wallet transfer collection$collection
                 **/
                $this->mailHelper->sendCustomerBulkTransferApproveMail($collection);
                if (($totalCount-$pendingTransCount) > 0) {
                    $this->messageManager->addSuccess(
                        __('%1 record(s) successfully updated.', $pendingTransCount)
                    );
                    $this->messageManager->addError(
                        __('%1 record(s) cannot be updated.', ($totalCount-$pendingTransCount))
                    );
                } else {
                    $this->messageManager->addSuccess(
                        __('%1 record(s) successfully updated.', $selected)
                    );
                }
            } else {
                $this->messageManager->addError(
                    __('%1 record(s) cannot be updated.', $selected)
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\RuntimeException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException(
                $e,
                __($e->getMessage())
            );
        }
        return $resultRedirect->setPath(
            '*/*/bankdetails',
            ['sender_type'=>Wallettransaction::CUSTOMER_TRANSFER_BANK_TYPE]
        );
    }
}
