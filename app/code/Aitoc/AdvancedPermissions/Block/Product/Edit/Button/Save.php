<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Block\Product\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Ui\Component\Control\Container;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\EditorTypeFactory;

/**
 * Class Save
 */
class Save extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Button\Save
{
    /**
     * @var Data
     */
    private $helper;

    /**
     * @var EditorTypeFactory
     */
    private $editorTypeFactory;

    public function __construct(
        Context $context,
        Registry $registry,
        Data $data,
        EditorTypeFactory $editorTypeFactory
    ) {
        parent::__construct($context, $registry);
        $this->helper = $data;
        $this->editorTypeFactory = $editorTypeFactory;
    }

    /**
     * Retrieve options
     *
     * @return array
     */
    protected function getOptions()
    {
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return parent::getOptions();
        }

        if ($this->helper->getRole()->getCanCreateProducts()) {
            $restrictedTypes = $this->getRestrictedProductTypes($this->helper->getRole()->getId());
            $typeId = $this->getProduct()->getTypeId();

            if ($restrictedTypes && array_search($typeId, $restrictedTypes) !== false) {
                $options[] = [
                    'id_hard' => 'save_and_new',
                    'label' => __('Save & New'),
                    'data_attribute' => [
                        'mage-init' => [
                            'buttonAdapter' => [
                                'actions' => [
                                    [
                                        'targetName' => $this->getSaveTarget(),
                                        'actionName' => $this->getSaveAction(),
                                        'params' => [
                                            true,
                                            [
                                                'back' => 'new'
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                ];

                if (!$this->context->getRequestParam('popup') && $this->getProduct()->isDuplicable()) {
                    $options[] = [
                        'label' => __('Save & Duplicate'),
                        'id_hard' => 'save_and_duplicate',
                        'data_attribute' => [
                            'mage-init' => [
                                'buttonAdapter' => [
                                    'actions' => [
                                        [
                                            'targetName' => $this->getSaveTarget(),
                                            'actionName' => $this->getSaveAction(),
                                            'params' => [
                                                true,
                                                [
                                                    'back' => 'duplicate'
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                    ];
                }
            }
        }

        $options[] = [
            'id_hard' => 'save_and_close',
            'label' => __('Save & Close'),
            'data_attribute' => [
                'mage-init' => [
                    'buttonAdapter' => [
                        'actions' => [
                            [
                                'targetName' => $this->getSaveTarget(),
                                'actionName' => $this->getSaveAction(),
                                'params' => [
                                    true
                                ]
                            ]
                        ]
                    ]
                ]
            ],
        ];

        return $options;
    }

    /**
     * @param $roleId
     * @return mixed
     */
    private function getRestrictedProductTypes($roleId)
    {
        if (!$roleId) {
            return [];
        }

        return $this->editorTypeFactory->create()->getRestrictedTypes($roleId);
    }
}
