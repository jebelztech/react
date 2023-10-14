<?php
/**
 *   Copyright © 2019 Magenest. All rights reserved.
 *   See COPYING.txt for license details.
 *
 *   Magenest_ImageGallery extension
 *   NOTICE OF LICENSE
 *
 */

/**
 * Created by PhpStorm.
 */

namespace Magenest\ImageGallery\Controller\Adminhtml\Gallery;

use \Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Psr\Log\LoggerInterface;
use Magenest\ImageGallery\Model\ResourceModel\Gallery\CollectionFactory;
use Magenest\ImageGallery\Model\GalleryImageFactory;
use Magenest\ImageGallery\Model\ResourceModel\GalleryImage\CollectionFactory as GalleryImageCollection;
use Magenest\ImageGallery\Model\GalleryFactory;
use Magenest\ImageGallery\Model\ImageFactory;
use Magento\UrlRewrite\Model\UrlRewriteFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlFinderInterface;

/**
 * Class Save
 * @package Magenest\ImageGallery\Controller\Adminhtml\Gallery
 */
class Save extends \Magento\Backend\App\Action
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var Filesystem
     */
    protected $_filesystem;

    /**
     * @var UploaderFactory
     */
    protected $_fileUploaderFactory;

    /**
     * @var CollectionFactory
     */
    protected $galleryCollectionFactory;

    /**
     * @var GalleryImageFactory
     */
    protected $galleryImageFactory;

    /**
     * @var GalleryImageCollection
     */
    protected $galleryImageCollectionFactory;

    /**
     * @var GalleryFactory
     */
    protected $galleryFactory;

    protected $imageFactory;

    protected $_urlRewriteFactory;

    protected $urlFinder;

    protected $_scopeConfig;
    /**
     * Save constructor.
     * @param Context $context
     * @param Filesystem $filesystem
     * @param UploaderFactory $fileUploaderFactory
     */
    public function __construct(
        Context $context,
        Filesystem $filesystem,
        UploaderFactory $fileUploaderFactory,
        CollectionFactory $galleryCollectionFactory,
        GalleryImageFactory $galleryImageFactory,
        GalleryImageCollection $galleryImageCollectionFactory,
        GalleryFactory $galleryFactory,
        ImageFactory $imageFactory,
        UrlRewriteFactory $urlRewriteFactory,
        UrlFinderInterface $urlFinder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    )
    {
        $this->_filesystem = $filesystem;
        $this->_scopeConfig = $scopeConfig;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->galleryCollectionFactory = $galleryCollectionFactory;
        $this->galleryImageFactory = $galleryImageFactory;
        $this->galleryImageCollectionFactory = $galleryImageCollectionFactory;
        $this->galleryFactory = $galleryFactory;
        $this->imageFactory = $imageFactory;
        $this->_urlRewriteFactory = $urlRewriteFactory;
        $this->urlFinder = $urlFinder;
        parent::__construct($context);
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        $file = $this->getRequest()->getFiles()['gallery_image'];
        $banner_file = $this->getRequest()->getFiles()['gallery_banner'];
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $postObject = new \Magento\Framework\DataObject();
            $postObject->setData($post);

            //check if category already have slider
            if ($post['category_id'] == 0)
                $post['category_id'] = null;
            else {
                if (isset($post['gallery_id'])) {
                    $galleryCollection = $this->galleryCollectionFactory->create()
                        ->addFieldToFilter('gallery_id', array('neq' => $post['gallery_id']))
                        ->addFieldToFilter('category_id', array('neq' => null))
                        ->addFieldToFilter('category_id', $post['category_id'])
                        ->getData();

                    $sliderPosition=[];
                    foreach ($galleryCollection as $gallery) {
                        array_push($sliderPosition,$gallery['category_slider_position']);
                    }

                    if (in_array(0,$sliderPosition) && in_array(1,$sliderPosition))
                        array_push($sliderPosition,2);

                    $errorMessage ='';
                    if (in_array(2,$sliderPosition))
                        $errorMessage = 'This category already has a slider (top & footer)!!!';
                    elseif (in_array($post['category_slider_position'],$sliderPosition))
                    {
                        switch ($post['category_slider_position']) {
                            case 0 :
                                $errorMessage = 'This category already has a slider on the top!!!';
                                break;
                            case 1 :
                                $errorMessage = 'This category already has a slider at footer!!!';
                                break;
                        }
                    }
                    elseif (!empty($sliderPosition) && $post['category_slider_position'] == 2)
                    {
                        switch ($sliderPosition[0]) {
                            case 0 :
                                $errorMessage = 'This category already has a slider on the top!!!';
                                break;
                            case 1 :
                                $errorMessage = 'This category already has a slider at footer!!!';
                                break;
                        }
                    }

                    if ($errorMessage != '')
                    {
                        $this->messageManager->addErrorMessage( __($errorMessage));
                        $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($post);
                        return $resultRedirect->setPath('imagegallery/gallery/edit', ['id' => $post['gallery_id']]);
                    }
                }
            }
            if (!isset($post['gallery_image'])) {
                $post['gallery_image'] = [];
            }
            if (!isset($post['gallery_banner'])) {
                $post['gallery_banner'] = [];
            }

            $array = [
                'title' => isset($post['titleGallery']) ? $post['titleGallery'] : null,
                'status' => isset($post['status']) ? $post['status'] : null,
                'description'=>isset($post['description2']) ? $post['description2'] : null ,
                'width'=>isset($post['width']) ? $post['width'] : null ,
                'height'=>isset($post['height']) ? $post['height'] : null ,
                'product_id'=>isset($post['attach_products']) ? $post['attach_products'] : null ,
                'category_id'=>isset($post['category_id']) ? $post['category_id'] : null,
                'layout_type' =>isset($post['layout_type']) ? $post['layout_type'] : null,
                'category_slider_position' =>isset($post['category_slider_position']) ? $post['category_slider_position'] : null,
                'number_image_slider'=>isset($post['number_image_slider']) ? $post['number_image_slider'] : null ,
            ];

            $image = $this->saveBackGround($file, $post['gallery_image'],'gallery_image');
            if (is_array($post['gallery_image']) && !empty($post['gallery_image'])) {
                $post['image'] = $post['image']['value'];
            }
            if ($image == 'deleted' || $image == '') {
                $post['image'] = null;
            } else {
                $post['image'] = $image;
            }
            if (!$post) {
                return $resultRedirect->setPath('imagegallery/image/');
            }
            if($post['image']!=null) {
                $array['gallery_image'] = $post['image'];
            }

            $banner = $this->saveBackGround($banner_file, $post['gallery_banner'],'gallery_banner');
            /*if (is_array($post['gallery_banner']) && !empty($post['gallery_banner'])) {
                $post['image'] = $post['image']['value'];
            }
            if ($image == 'deleted' || $image == '') {
                $post['image'] = null;
            } else {
                $post['image'] = $banner;
            }
            if($post['image']!=null) {
                $array['gallery_banner'] = $post['image'];
            }*/

            if($banner) {
                //echo "gallery_banner"." -- "; print_r($banner); exit;
                $array['gallery_banner'] = $banner;
            }

            $model = $this->galleryFactory->create();
            if (isset($post['gallery_id'])) {
                $model->load($post['gallery_id']);
            }
            $model->addData($array);

            $model->save();

            $galleryId =  $model->getData('gallery_id');

            $url_key = str_replace(' ', '-', strtolower($post['url']));
            $url_path = $this->_scopeConfig->getValue('imagegallery/galleryseoconfig/urlkey', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $requestPath = $url_path."/".$url_key;
            $storeId = 1;
            if(!$this->getRewrite($requestPath, $storeId)) {
                $urlRewriteModel = $this->_urlRewriteFactory->create();
                $urlRewriteModel->setStoreId(1);
                $urlRewriteModel->setIsSystem(0);
                $urlRewriteModel->setIdPath(rand(1, 100000));
                $urlRewriteModel->setTargetPath("imagegallery/gallery/view/id/".$galleryId);
                $urlRewriteModel->setRequestPath($requestPath);
                $urlRewriteModel->save();

                $model->addData(array('url_key'=>$url_key));
                $model->save();
            }

            if (!empty($post['image_id3']))
            {
                $imageId = explode(',', $post['image_id3']);
                $galleryImageCollection = $this->galleryImageCollectionFactory->create()
                    ->addFieldToFilter('gallery_id',$galleryId)
                    ->addFieldToFilter('image_id', array('nin' => $imageId));

                foreach ($galleryImageCollection as $item)
                    $item->delete();

                foreach ($imageId as $item)
                {
                    $galleryImageCollection = $this->galleryImageCollectionFactory->create()
                        ->addFieldToFilter('gallery_id',$galleryId)
                        ->addFieldToFilter('image_id',$item);
                    if (empty($galleryImageCollection->getData()))
                    {
                        $galleryImageFactory = $this->galleryImageFactory->create();
                        $galleryImageFactory->setData('gallery_id',$galleryId);
                        $galleryImageFactory->setData('image_id',$item);
                        $galleryImageFactory->save();
                    }
                }
            }
            else
            {
                $galleryImageCollection = $this->galleryImageCollectionFactory->create()
                    ->addFieldToFilter('gallery_id',$galleryId);
                foreach ($galleryImageCollection as $item)
                    $item->delete();
            }

            $this->messageManager->addSuccess(__('The rule has been saved.'));
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData(false);
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError($e, __('Something went wrong while saving the rule.'));
            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->_objectManager->get('Magento\Backend\Model\Session')->setPageData($post);
            return $resultRedirect->setPath('imagegallery/gallery/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
    }
    public function getRewrite($requestPath, $storeId = null)
    {
        if($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        return $this->urlFinder->findOneByData(
            [
                UrlRewrite::REQUEST_PATH => ltrim($requestPath, '/'),
                UrlRewrite::STORE_ID => $storeId,
            ]
        );
    }
    public function saveBackGround($value, $post, $fieldId)
    {
        /*if($fieldId=="gallery_banner") {
            echo "<pre>";
            print_r($value);
            print_r($post);
            print($fieldId);
            exit;
        }*/ 
        if (!empty($value['name'])) {
            /** Deleted file */
            /*if (!empty($post['delete']) && !empty($post['value'])) {
                $path = $this->_filesystem->getDirectoryRead(
                    DirectoryList::MEDIA
                );
                if ($path->isFile($post['value'])) {
                    $this->_filesystem->getDirectoryWrite(
                        DirectoryList::MEDIA
                    )->delete($post['value']);
                }
                if (empty($value['name'])) {
                    return 'deleted';
                }
            }
            if (empty($value['name']) && !empty($post)) {
                return $post['value'];
            }*/
            $path = $this->_filesystem->getDirectoryRead(
                DirectoryList::MEDIA
            )->getAbsolutePath(
                'imagegallery/template/'
            );
            try {
                /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
                $uploader = $this->_fileUploaderFactory->create(['fileId' => $fieldId]);
                $uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
                $uploader->setAllowRenameFiles(false);
                $result = $uploader->save($path);
                //echo "<pre>"; print_r($result); exit;
                if (is_array($result) && !empty($result['name'])) {
                    return 'imagegallery/template/' . $result['name'];
                }
            } catch (\Exception $e) {
                if ($e->getCode() != Uploader::TMP_NAME_EMPTY) {
                    $this->_logger->critical($e);
                }
                $this->_logger->critical($e);
            }
        }

        return '';
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return true;
    }
}
