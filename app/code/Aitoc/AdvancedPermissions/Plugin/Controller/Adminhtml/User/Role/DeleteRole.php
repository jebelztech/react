<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Plugin\Controller\Adminhtml\User\Role;

use Aitoc\AdvancedPermissions\Model\Role;
use Aitoc\AdvancedPermissions\Model\Stores;
use Closure;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\Auth\Session;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;
use Magento\User\Controller\Adminhtml\User\Role\Delete as MagentoDeleteRole;
use Aitoc\AdvancedPermissions\Model\EditorTypeFactory;
use Aitoc\AdvancedPermissions\Model\EditorTabFactory;
use Aitoc\AdvancedPermissions\Model\EditorAttributeFactory;
use Aitoc\AdvancedPermissions\Api\Data\EditorTypeInterface;
use Aitoc\AdvancedPermissions\Api\Data\EditorTabInterface;
use Aitoc\AdvancedPermissions\Api\Data\EditorAttributeInterface;
use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Store\Api\WebsiteRepositoryInterface;

class DeleteRole
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var Role
     */
    protected $role;

    /**
     * @var Stores
     */
    protected $stores;

    /**
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Backend auth session
     *
     * @var Session
     */
    protected $authSession;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var ResultFactory
     */
    protected $resultFactory;

    /**
     * @var EditorTypeFactory
     */
    protected $editorTypeFactory;

    /**
     * @var EditorTabFactory
     */
    protected $editorTabFactory;

    /**
     * @var EditorAttributeFactory
     */
    protected $editorAttributeFactory;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * @var WebsiteRepositoryInterface
     */
    protected $websiteRepository;

    public function __construct(
        Context $context,
        Role $roleAdv,
        Registry $coreRegistry,
        Stores $storesAdv,
        Session $authSession,
        EditorTypeFactory $editorTypeFactory,
        EditorTabFactory $editorTabFactory,
        EditorAttributeFactory $editorAttributeFactory,
        Data $dataHelper,
        WebsiteRepositoryInterface $websiteRepository
    ) {
        $this->request = $context->getRequest();
        $this->objectManager = $context->getObjectManager();
        $this->coreRegistry = $coreRegistry;
        $this->role = $roleAdv;
        $this->stores = $storesAdv;
        $this->authSession = $authSession;
        $this->messageManager = $context->getMessageManager();
        $this->resultFactory = $context->getResultFactory();
        $this->editorTypeFactory = $editorTypeFactory;
        $this->editorTabFactory = $editorTabFactory;
        $this->editorAttributeFactory = $editorAttributeFactory;
        $this->dataHelper = $dataHelper;
        $this->websiteRepository = $websiteRepository;

    }

    /**
     * @param MagentoDeleteRole $object
     * @param $result
     * @return mixed
     */
    public function afterExecute(
        MagentoDeleteRole $object,
        $result
    ) {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $request = $this->request;
        $rid = $request->getParam('rid', false);
        try {
            $this->editorTabFactory->create()->deleteRole($rid);
            $this->editorTypeFactory->create()->deleteRole($rid);
            $this->editorAttributeFactory->create()->deleteRole($rid);

            return $result;
        } catch (\Magento\Framework\Exception\AuthenticationException $e) {
            $arguments = $rid ? ['rid' => $rid] : [];

            return $resultRedirect->setPath('*/*/editrole', $arguments);
        } catch (\Exception $exception) {
            $arguments = $rid ? ['rid' => $rid] : [];
            $this->messageManager->addErrorMessage($exception->getMessage());
            return $resultRedirect->setPath('*/*/editrole', $arguments);
        }


    }
}
