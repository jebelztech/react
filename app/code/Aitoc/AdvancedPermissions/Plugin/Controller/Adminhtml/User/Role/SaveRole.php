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
use Magento\User\Controller\Adminhtml\User\Role\SaveRole as MagentoSaveRole;
use Aitoc\AdvancedPermissions\Model\EditorTypeFactory;
use Aitoc\AdvancedPermissions\Model\EditorTabFactory;
use Aitoc\AdvancedPermissions\Model\EditorAttributeFactory;
use Aitoc\AdvancedPermissions\Api\Data\EditorTypeInterface;
use Aitoc\AdvancedPermissions\Api\Data\EditorTabInterface;
use Aitoc\AdvancedPermissions\Api\Data\EditorAttributeInterface;
use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Store\Api\WebsiteRepositoryInterface;

class SaveRole
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
     * @param MagentoSaveRole $object
     * @param Closure $proceed
     */
    public function afterExecute(
        MagentoSaveRole $object,
        $result
    ) {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $request = $this->request;
        $params = $request->getParams();
        $rid = $this->coreRegistry->registry('current_role')->getId();
        try {
            $this->validateUser();
            if (isset($params['radio_limits'])) {
                $radioLimits = $params['radio_limits'];
                $roleAdvanced = $this->objectManager->create(\Aitoc\AdvancedPermissions\Model\Role::class)
                    ->loadOriginal($rid);
                $roleAdvancedId = (int)$roleAdvanced->getId();
                $webSiteId = 0;
                $originalId = (int)$rid;
                $stores = $this->stores->getCollection()->setRoleFilter($roleAdvancedId);
                $scope = 0;
                if ($radioLimits == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE) {
                    if (!$roleAdvancedId) {
                        $roleAdvanced = $this->objectManager->create(\Aitoc\AdvancedPermissions\Model\Role::class);
                    }
                    $scope = \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE;
                }
                if ($radioLimits == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_WEBSITE) {
                    $websites = isset($params['websites']) ? $params['websites'] : [0];

//                    if (!$websites) {
//                        $webSiteId = $this->websiteRepository->getDefault()->getId();
//                    } else {
                        $webSiteId = implode(",", $websites);
//                    }
                }
                $this->fullSave($roleAdvanced, $stores, (int)$radioLimits, $webSiteId, $originalId, $params, $scope);
            }

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

    /**
     * @param $roleAdvancedId
     * @param $stores
     * @param $radioLimits
     * @param $webSiteId
     * @param $originalId
     * @param null $params
     * @param int $scope
     */
    public function fullSave(
        $roleAdvanced,
        $stores,
        $radioLimits,
        $webSiteId,
        $originalId,
        $params = null,
        $scope = 0
    ) {
        $this->removeUnAvailableStoresForRoleAdvancedId($roleAdvanced, $stores);
        $this->saveSettings($roleAdvanced, $params['setting']);
        $this->setParamsForRoleAdvancedAndSave($roleAdvanced, $radioLimits, $webSiteId, $originalId, $params, $scope);

        $this->saveRoleProductCreatorPermissions($roleAdvanced, $params);
        $this->saveRoleProductTabsPermissions($roleAdvanced->getId(), $params);

        $this->saveAttributeArray('is_allow_ids', $roleAdvanced->getId(), 1);
        $this->saveAttributeArray('is_disable_ids', $roleAdvanced->getId(), 0);
        $this->saveAttributeArray('is_hidden_ids', $roleAdvanced->getId(), 2);
    }

    /**
     * Remove unavailable stores from the list
     *
     * @param integer $roleAdvancedId
     * @param array $stores
     *
     * @return null
     */
    private function removeUnAvailableStoresForRoleAdvancedId($roleAdvancedId, $stores)
    {
        if ($roleAdvancedId) {
            foreach ($stores as $store) {
                $store->delete();
            }
        }

        return null;
    }

    /**
     *
     * Set parameters for advanced role and save
     *
     * @param $roleAdvanced
     * @param $radioLimits
     * @param $webSiteId
     * @param $originalId
     * @param null $params
     * @param int $scope
     */
    private function setParamsForRoleAdvancedAndSave(
        $roleAdvanced,
        $radioLimits,
        $webSiteId,
        $originalId,
        $params = null,
        $scope = 0
    ) {
        $roleAdvanced->setScope($radioLimits);
        $roleAdvanced->setWebsiteId($webSiteId);
        $roleAdvanced->setOriginalId($originalId);
        $this->saveModel($roleAdvanced);
        if ($scope == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE) {
            $this->saveStores($roleAdvanced->getId(), $params);
        }
    }

    /**
     * Save fields from settings
     *
     * @param $role
     * @param $params
     */
    public function saveSettings($role, $params)
    {
        $data = $role->getOptions();
        foreach ($data as $value) {
            $use = 0;
            $scope = 0;
            if (isset($params['use_config_' . $value])) {
                $use = $params['use_config_' . $value];
            }
            $role->setData('use_config_' . $value, $use);
            if (isset($params[$value])) {
                $role->setData($value, $params[$value]);
            }
        }
    }

    /**
     * Save Model
     *
     * @param $model
     */
    public function saveModel($model)
    {

        try {
            $model->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while saving this role.'));
        }
    }

    /**
     * Save Stores
     *
     * @param $roleId
     * @param $params
     */
    public function saveStores($roleId, $params)
    {
        if (isset($params['store'])) {
            $newStores = $params['store'];
            foreach ($newStores as $el) {
                $storeNew = $this->objectManager->create('Aitoc\AdvancedPermissions\Model\Stores');
                $storeNew->setStoreId($el);
                if (isset($params['storesview'])) {
                    if (isset($params['storesview'][$el])) {
                        $storeNew->setStoreViewIds(implode(",", $params['storesview'][$el]));
                    }
                }
                if (isset($params['category_ids' . $el])) {
                    $storeNew->setCategoryIds(implode(",", $params['category_ids' . $el]));
                }
                $storeNew->setAdvancedId($roleId);
                $storeNew->save();
            }
        }
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\AuthenticationException
     * @throws \Magento\Framework\Exception\State\UserLockedException
     */
    protected function validateUser()
    {
        $password = $this->request->getParam(
            \Magento\User\Block\Role\Tab\Info::IDENTITY_VERIFICATION_PASSWORD_FIELD
        );
        $user = $this->authSession->getUser();
        $user->performIdentityCheck($password);

        return $this;
    }

    /**
     * @param $name
     * @param $roleId
     * @param $allow
     */
    protected function saveAttributeArray($name, $roleId, $allow)
    {
        if ($this->request->getParam($name) != $this->request->getParam($name . '_old')) {
            $attributes    = $this->getArrayByName($name);
            $attributesOld = $this->getArrayByName($name . '_old');

            $arrayAdd = array_diff($attributes, $attributesOld);
            $arrayDel = array_diff($attributesOld, $attributes);

            $collection = $this->editorAttributeFactory->create()->getCollection();
            $collection->setPostRoleId($roleId);
            $collection->setPostAllow($allow);

            if (!empty($arrayDel)) {
                $collection->deleteAttributeByRole($arrayDel);
            }

            if (!empty($arrayAdd)) {
                $collection->addAttributeByRole($arrayAdd);
            }
        }
    }

    /**
     * @param $name
     * @return array|mixed
     */
    protected function getArrayByName($name)
    {
        $array = $this->request->getParam($name);
        parse_str($array, $array);

        $array = array_keys($array);

        return $array;
    }

    /**
     * @param $roleId
     * @param $params
     * @return bool
     */
    protected function saveRoleProductCreatorPermissions($role, $params)
    {
        if (isset($params['allow_create_product'])) {
            $role->setCanCreateProducts($params['allow_create_product']);
            $role->save();
        }

        if (isset($params['apply_to']) && $params['apply_to']) {
            $types = $params['apply_to'];

            $this->editorTypeFactory->create()->deleteRole($role->getId());

            foreach ($types as $type) {
                $editorType = $this->editorTypeFactory->create();
                $editorType->setData(EditorTypeInterface::ADVANCED_ROLE_ID, $role->getId());
                $editorType->setData(EditorTypeInterface::TYPE, $type);
                $editorType->save();
            }

            return true;
        }

        return false;
    }

    /**
     * @param $roleId
     * @param $params
     * @return bool|void
     */
    protected function saveRoleProductTabsPermissions($roleId, $params)
    {
        $tabs = $this->dataHelper->getProductTabs();
        $tabs = array_keys($tabs);

        if (!isset($params) || !is_array($params)) {
            return;
        }

        $this->editorTabFactory->create()->deleteRole($roleId);

        foreach ($tabs as $tab) {
            if (array_key_exists($tab, $params)) {
                $editorType = $this->editorTabFactory->create();
                $editorType->setData(EditorTabInterface::ADVANCED_ROLE_ID, $roleId);
                $editorType->setData(EditorTabInterface::TAB_CODE, $tab);
                $editorType->save();
            }
        }

        return true;
    }
}
