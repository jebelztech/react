<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin;

use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\Permissions;
use Closure;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Authorization as FrameworkAuthorization;

class Authorization
{
    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var Permissions
     */
    protected $permissionsModel;

    /**
     * @param Context $context
     * @param Data $helper
     * @param Permissions $permissionsModel
     */
    public function __construct(
        Context $context,
        Data $helper,
        Permissions $permissionsModel
    ) {
        $this->request          = $context->getRequest();
        $this->helper           = $helper;
        $this->permissionsModel = $permissionsModel;
    }

    /**
     * Disallow for direct link.
     *
     * @param FrameworkAuthorization $object
     * @param Closure $proceed
     * @param string $resource
     * @param null $privilege
     *
     * @return bool
     */
    public function aroundIsAllowed(
        FrameworkAuthorization $object,
        Closure $proceed,
        $resource,
        $privilege = null
    ) {
        return $this->permissionsModel->isAllowedResource($resource, $proceed($resource, $privilege));
    }
}
