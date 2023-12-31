<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */
namespace Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Main
 * @package Magenest\ImageGallery\Block\Adminhtml\Gallery\Edit\Tab
 */
class Main extends Generic implements TabInterface
{
    /**
     * @var
     */
    protected $_prepareForm;

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $_groupRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $_searchCriteriaBuilder;

    /**
     * @var \Magento\Framework\Convert\DataObject
     */
    protected $_objectConverter;

    /**
     * @var
     */
    protected $_fieldFactory;

    /**
     * @var \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory
     */
    protected $galleryImageCollectionFactory;
    protected $_websiteFactory;
    protected $systemStore;

    /**
     * Main constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\Convert\DataObject $objectConverter
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\Convert\DataObject $objectConverter,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        \Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory $galleryImageCollectionFactory,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        $this->_websiteFactory = $websiteFactory;
        $this->_groupRepository = $groupRepository;
        $this->_searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->_objectConverter = $objectConverter;
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * prepare form
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('gallery');

        $galleryId = $this->getRequest()->getParam('id');

        if (isset($galleryId))
        {
            $imageId = '';
            $galleryImageCollection = $this->galleryImageCollectionFactory->create()->addFieldToFilter('gallery_id',$galleryId);
            foreach ($galleryImageCollection as $item)
                $imageId = $imageId . $item->getData('image_id') . "," ;
            $imageId =  substr($imageId, 0, -1);
            $model->setData('image_id3',$imageId);
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('page_');

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Gallery Information')]);
        $store_id = 0;
        if ($model->getId()) {
            $fieldset->addField(
                'gallery_id',
                'hidden',
                [
                    'name' =>'gallery_id'
                ]
            );
            $store_id = $model->getStoreId();
        }

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'titleGallery',
                'label' => __('Gallery Title'),
                'title' => __('Gallery Title'),
                'required' => true,
            ]
        );

        $fieldset->addType('required_image', 'Magenest\ImageGallery\Block\Adminhtml\Image\Helper\Required');
        $fieldset->addField(
            'gallery_image',
            'required_image',
            [
                'name' => 'gallery_image',
                'label' => __('Gallery Image'),
                'title' => __('Gallery Image'),
                'note' => __('Allow extension : gif,jpeg,jpg,png'),
                'required' => true,
                'enctype' => 'multipart/form-data'
            ]
        )->setAfterElementHtml('
            <script>
                require(["jquery"], function($){
                    $(document).ready(function () {
                        if($("#page_image_id").attr("value")){
                            $("#page_image").removeClass("required-file");
                        }else{
                            $("#page_image").addClass("required-file");
                        }
                        $("#page_image").addClass("validate-filesize");
                    $( "#page_image" ).attr( "accept", "image/gif,image/jpeg,image/jpg,image/png" );
                });
            });
            </script>');

        $fieldset->addType('required_image', 'Magenest\ImageGallery\Block\Adminhtml\Image\Helper\Required');
        $fieldset->addField(
            'gallery_banner',
            'required_image',
            [
                'name' => 'gallery_banner',
                'label' => __('Gallery Banner'),
                'title' => __('Gallery Banner'),
                'note' => __('Allow extension : gif,jpeg,jpg,png'),
                'required' => true,
                'enctype' => 'multipart/form-data'
            ]
        )->setAfterElementHtml('
            <script>
                require(["jquery"], function($){
                    $(document).ready(function () {
                        if($("#page_image_id").attr("value")){
                            $("#page_image").removeClass("required-file");
                        }else{
                            $("#page_image").addClass("required-file");
                        }
                        $("#page_image").addClass("validate-filesize");
                    $( "#page_image" ).attr( "accept", "image/gif,image/jpeg,image/jpg,image/png" );
                });
            });
            </script>');

        $fieldset->addField(
            'url',
            'text',
            [
                'name' => 'url',
                'label' => __('Url Key'),
                'title' => __('Url Key'),
                'note' => 'Unique URL Key for each gallery.',
                'required' => true,
            ]
        );

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'options' => [
                    0 => 'Enabled',
                    1 => 'Disabled',
                ],
            ]
        );

        /*$websites = $this->_websiteFactory->create()->getCollection()->toOptionArray();
        $fieldset->addField(
            'store_ids',
            'select',
            [
                'name' => 'website_id',
                'label' => __('Website'),
                'value' => $model->getWebsiteId(),
                'values' => $websites,
                'required' => true,
                'disabled' => false
            ]
        );*/

        $field = $fieldset->addField(
            'store_id',
            'multiselect',
            [
                'label' => __('Store View'),
                'title' => __('Store View'),
                'name' => 'store_id',
                'value' => $store_id,
                'values' => $this->_systemStore->getStoreValuesForForm(false, true),
                'required' => true,
//                set first argument true and second to false to add blank option which value is blank
//                set second argument true to add "All Store Views" option which value is 0
            ]
        );

        $fieldset->addField(
            'image_id3',
            'hidden',
            [
                'name' => 'image_id3',
            ]
        );

        $fieldset->addField(
            'image_id2',
            'hidden',
            [
                'name' => 'image_id2',
            ]
        );

        $fieldset->addField(
            'description',
            'hidden',
            [
                'name' => 'description',
            ]
        );

        $fieldset->addField(
            'description2',
            'textarea',
            [
                'name' => 'description2',
                'label' => __('Description'),
                'title' => __('Description'),
            ]
        );

        $fieldset->addField(
            'width',
            'text',
            [
                'class' => 'validate-number input-text validate-digits-range digits-range-1-100',
                'name' => 'width',
                'note' => 'Width must be from 1% to 100%',
                'label' => __('Width(%) of slider'),
                'title' => __('Width(%) of slider'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'height',
            'text',
            [
                'class' => 'validate-number input-text validate-zero-or-greater',
                'name' => 'height',
                'label' => __('Height(px) of slider'),
                'title' => __('Height(px) of slider'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'product_id',
            'hidden',
            [
                'name' => 'product_id',
            ]
        );

        $fieldset->addField(
            'number_image_slider',
            'text',
            [
                'class' => 'validate-number input-text validate-digits-range digits-range-1-20',
                'name' => 'number_image_slider',
                'label' => __('Number of image in slider'),
                'title' => __('Number of image in slider'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'layout_type',
            'select',
            [
                'name' => 'layout_type',
                'label' => __('Layout Type'),
                'title' => __('Layout Type'),
                'required' => true,
                'options' => [
                    0 => '4 Columns',
                    1 => 'Grid',
                ],
            ]
        );

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Gallery');
    }

    /**
     * Prepare title for tab
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('Gallery');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Check permission for passed action
     *
     * @param string $resourceId
     * @return bool
     */
    protected function _isAllowedAction($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
