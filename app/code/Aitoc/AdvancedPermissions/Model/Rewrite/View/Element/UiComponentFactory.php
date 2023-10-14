<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Model\Rewrite\View\Element;

use Magento\Framework\Config\DataInterface;
use Magento\Framework\Config\DataInterfaceFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Data\Argument\InterpreterInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponent\Config\ManagerInterface;
use Magento\Framework\View\Element\UiComponent\ContextFactory;
use Magento\Framework\Phrase;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Framework\View\Element\UiComponent\Factory\ComponentFactoryInterface;
use Aitoc\AdvancedPermissions\Helper\Data;
use Aitoc\AdvancedPermissions\Model\EditorTabFactory;

class UiComponentFactory extends \Magento\Framework\View\Element\UiComponentFactory
{
    const PRODUCT_FORM_DATA_NAME = 'product_form';

    /**
     * @var ComponentFactoryInterface[]
     */
    private $componentChildFactories;

    /**
     * @var DataInterfaceFactory
     */
    private $configFactory;

    /**
     * @var \Magento\Ui\Config\Reader\Definition\Data
     */
    private $definitionData;

    /**
     * @var Data
     */
    private $helperData;

    /**
     * @var EditorTabFactory
     */
    private $editorTabFactory;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ManagerInterface $componentManager,
        InterpreterInterface $argumentInterpreter,
        ContextFactory $contextFactory,
        Data $helperData,
        EditorTabFactory $editorTabFactory,
        array $data = [],
        array $componentChildFactories = [],
        DataInterface $definitionData = null,
        DataInterfaceFactory $configFactory = null
    ) {
        parent::__construct(
            $objectManager,
            $componentManager,
            $argumentInterpreter,
            $contextFactory,
            $data,
            $componentChildFactories,
            $definitionData,
            $configFactory
        );

        $this->definitionData = $definitionData ?:
            $this->objectManager->get(DataInterface::class);
        $this->componentChildFactories = $componentChildFactories;
        $this->helperData = $helperData;
        $this->editorTabFactory = $editorTabFactory;
        $this->configFactory = $configFactory ?: $this->objectManager->get(DataInterfaceFactory::class);
    }

    /**
     * @param string $identifier
     * @param array $bundleComponents
     * @param bool $reverseMerge
     * @return array
     * @throws LocalizedException
     */
    protected function mergeMetadata($identifier, array $bundleComponents, $reverseMerge = false)
    {
        $dataProvider = $this->getDataProvider($identifier, $bundleComponents);
        if ($dataProvider instanceof DataProviderInterface) {
            $metadata = [
                $identifier => [
                    'children' => $dataProvider->getMeta(),
                ],
            ];
            $bundleComponents = $this->mergeMetadataItem($bundleComponents, $metadata, $reverseMerge);
        }

        if (array_key_exists(self::PRODUCT_FORM_DATA_NAME, $bundleComponents)
            && $this->helperData->isAdvancedPermissionEnabled()
        ) {
            $editorTab    = $this->editorTabFactory->create();
            $disabledTabs = $editorTab->getDisabledTabs($this->helperData->getRole()->getId());

            if ($disabledTabs) {
                foreach ($disabledTabs as $tab) {
                    if ($tab == 'websites_restrict') {
                        $tab = 'websites';
                    }

                    if (array_key_exists($tab, $bundleComponents[self::PRODUCT_FORM_DATA_NAME]['children'])) {
                        unset($bundleComponents[self::PRODUCT_FORM_DATA_NAME]['children'][$tab]);
                    }
                }
            }
        }

        return $bundleComponents;
    }
}
