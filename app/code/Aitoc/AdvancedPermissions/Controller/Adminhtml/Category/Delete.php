<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Controller\Adminhtml\Category;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Api\CategoryRepositoryInterface;

class Delete extends \Magento\Catalog\Controller\Adminhtml\Category\Delete
{
    /**
     * @var Data
     */
    protected $helper;

    /**
     * Delete constructor.
     *
     * @param Context $context
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Data $helper
     */
    public function __construct(
        Context $context,
        CategoryRepositoryInterface $categoryRepository,
        Data $helper
    ) {
        parent::__construct($context, $categoryRepository);
        $this->helper = $helper;
    }

    /**
     * Delete category action
     *
     * @return Redirect
     */
    public function execute()
    {

        if ($this->helper->isAdvancedPermissionEnabled() && !$this->helper->getRole()->getAllowDelete()) {
            $this->messageManager->addError(__('Deleting categories is not allowed'));
            $resultRedirect = $this->resultRedirectFactory->create();
            
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }

        return parent::execute();
    }
}
