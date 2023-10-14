<?php
namespace Satish\Manage\Controller\Adminhtml\imagess;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;


class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    public function __construct(Action\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();


        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $model = $this->_objectManager->create('Satish\Manage\Model\Imagess');

            $id = $this->getRequest()->getParam('id');
            if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }else {
				$data['created_at']=date('Y-m-d H:i:s');
			}
			
			
		
			if(isset($_FILES['image']['size']) && $_FILES['image']['size']>0){
			
						try{
							$uploader = $this->_objectManager->create(
								'Magento\MediaStorage\Model\File\Uploader',
								['fileId' => 'image']
							);
							$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
							/** @var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter */
							$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
							$uploader->setAllowRenameFiles(true);
							$uploader->setFilesDispersion(true);
							/** @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory */
							$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
								->getDirectoryRead(DirectoryList::MEDIA);
							$result = $uploader->save($mediaDirectory->getAbsolutePath('manage_imagess'));
								if($result['error']==0)
								{
									$data['image'] = 'manage_imagess' . $result['file'];
								}
						} catch (\Exception $e) {
							//unset($data['image']);
						}
						
			} else {
				
				
			$data['image']= $model->getImage();
			
			}
			
			
			
			
			
			
			
			//var_dump($data);die;
			if(isset($data['image']['delete']) && $data['image']['delete'] == '1'){
				$data['image'] = '';
			}

	/*
		
				if($_FILES['image_mobile']['name']>0){
			
			try{
				$uploader = $this->_objectManager->create(
					'Magento\MediaStorage\Model\File\Uploader',
					['fileId' => 'image_mobile']
				);
				$uploader->setAllowedExtensions(['jpg', 'jpeg', 'gif', 'png']);
				//@var \Magento\Framework\Image\Adapter\AdapterInterface $imageAdapter 
				$imageAdapter = $this->_objectManager->get('Magento\Framework\Image\AdapterFactory')->create();
				$uploader->setAllowRenameFiles(true);
				$uploader->setFilesDispersion(true);
				// @var \Magento\Framework\Filesystem\Directory\Read $mediaDirectory /
				$mediaDirectory = $this->_objectManager->get('Magento\Framework\Filesystem')
					->getDirectoryRead(DirectoryList::MEDIA);
				$result = $uploader->save($mediaDirectory->getAbsolutePath('manage_imagess'));
					if($result['error']==0)
					{
						$data['image_mobile'] = 'manage_imagess' . $result['file'];
					}
			} catch (\Exception $e) {
				//unset($data['image']);
            }
		}
		
		

			//var_dump($data);die;
			if(isset($data['image_mobile']['delete']) && $data['image_mobile']['delete'] == '1')
				$data['image_mobile'] = '';
*/



			
				
				
				
			$data['updated_at']=date('Y-m-d H:i:s');
		
            $model->setData($data);
			
            try {
                $model->save();
                $this->messageManager->addSuccess(__('The Imagess has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Imagess.'));
            }
			

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
			
        }
        return $resultRedirect->setPath('*/*/');
    }
}