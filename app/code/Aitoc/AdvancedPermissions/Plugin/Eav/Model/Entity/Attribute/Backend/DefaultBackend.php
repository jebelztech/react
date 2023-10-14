<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Eav\Model\Entity\Attribute\Backend;

use Aitoc\AdvancedPermissions\Helper\Data as PermissionHelper;
use Closure;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend as EavDefault;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Plugin for @see EavDefault
 * name = save_eav_value
 */
class DefaultBackend
{
    /**
     * @var PermissionHelper
     */
    private $helper;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @param PermissionHelper $helper
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        PermissionHelper $helper,
        ProductRepositoryInterface $productRepository
    ) {
        $this->helper = $helper;
        $this->productRepository = $productRepository;
    }

    /**
     * Delete values for restricted attributes
     *
     * @param EavDefault $object
     * @param Closure $closure
     * @param DataObject $entity
     *
     * @return EavDefault
     */
    public function aroundBeforeSave(EavDefault $object, Closure $closure, $entity)
    {
        $result = $closure($entity);
        
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return $result;
        }

        if ($this->helper->isAttributeRestricted($object->getAttribute()) && !$entity->isObjectNew()) {
            $code = $object->getAttribute()->getAttributeCode();
            $attr = ['type_id'];

            if (!in_array($code, $attr)) {
                $entityId = $entity->getId();
                $oldAttributeValue = $this->getOldAttributeValue($entityId, $code);
                $entity->setData($code, $oldAttributeValue);
            }
        }

        return $result;
    }

    /**
     * @param int $entityId
     * @param string $code
     * @return mixed
     * @throws NoSuchEntityException
     */
    protected function getOldAttributeValue($entityId, $code)
    {
        $product = $this->productRepository->getById($entityId);

        return $product[$code];
    }
}
