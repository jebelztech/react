<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */

/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Product\Form;

use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ResourceModel\Category\Collection;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Data\Form\Element\CollectionFactory as FormElementCollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Multiselect;
use Magento\Framework\Escaper;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\View\LayoutInterface;

class Category extends Multiselect
{
    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * Backend data
     *
     * @var Data
     */
    protected $backendData;

    /**
     * @var CategoryCollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * @param Factory $factoryElement
     * @param FormElementCollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param CategoryCollectionFactory $collectionFactory
     * @param Data $backendData
     * @param LayoutInterface $layout
     * @param EncoderInterface $jsonEncoder
     * @param AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        FormElementCollectionFactory $factoryCollection,
        Escaper $escaper,
        CategoryCollectionFactory $collectionFactory,
        Data $backendData,
        LayoutInterface $layout,
        EncoderInterface $jsonEncoder,
        AuthorizationInterface $authorization,
        array $data = []
    ) {
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->jsonEncoder       = $jsonEncoder;
        $this->collectionFactory = $collectionFactory;
        $this->backendData       = $backendData;
        $this->authorization      = $authorization;
        $this->layout = $layout;
        if (!$this->isAllowed()) {
            $this->setType('hidden');
            $this->addClass('hidden');
        }
    }

    /**
     * Get values for select
     *
     * @return array
     */
    public function getValues()
    {
        $collection = $this->_getCategoriesCollection();
        $values     = $this->getValue();
        if (!is_array($values)) {
            $values = explode(',', $values);
        }
        $collection->addAttributeToSelect('name');
        $collection->addIdFilter($values);

        $options = [];

        foreach ($collection as $category) {
            $options[] = ['label' => $category->getName(), 'value' => $category->getId()];
        }

        return $options;
    }

    /**
     * Get categories collection
     *
     * @return Collection
     */
    protected function _getCategoriesCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * Attach category suggest widget initialization
     *
     * @return string
     */
    public function getAfterElementHtml()
    {
        if (!$this->isAllowed()) {
            return '';
        }
        $htmlId             = $this->getHtmlId();
        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions    = $this->jsonEncoder->encode($this->_getSelectorOptions());


        $return = <<<HTML
    <input id="{$htmlId}-suggest" placeholder="$suggestPlaceholder" />
    <script>
        require(["jquery", "mage/mage"], function($){
            $('#{$htmlId}-suggest').mage('aitapTreeSuggest', {$selectorOptions});
        });
    </script>
HTML;

        return $return;
    }

    /**
     * Get selector options
     *
     * @return array
     */
    protected function _getSelectorOptions()
    {
        return [
            'source'      => $this->backendData->getUrl('catalog/category/suggestCategories'),
            'valueField'  => '#' . $this->getHtmlId(),
            'className'   => 'category-select',
            'multiselect' => true,
            'showAll'     => true,
            'root_ids'    => $this->getRootIds(),
            'show_id'     => $this->getShowId()
        ];
    }

    /**
     * Whether permission is granted
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->authorization->isAllowed('Magento_Catalog::categories');
    }
}
