<?php
namespace Etail\Career\Controller\Adminhtml\careers;

use Magento\Backend\App\Action;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\UrlRewrite\Model\UrlRewriteFactory;

class Save extends \Magento\Backend\App\Action
{

    /**
     * @param Action\Context $context
     */
    protected $_urlRewriteFactory;
    public function __construct(UrlRewriteFactory $urlRewriteFactory, Action\Context $context)
    {
        $this->_urlRewriteFactory = $urlRewriteFactory;
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
            $model = $this->_objectManager->create('Etail\Career\Model\Careers');

            $id = $this->getRequest()->getParam('career_id');
            if ($id) {
                $model->load($id);
                $model->setCreatedAt(date('Y-m-d H:i:s'));
            }
			

            $model->setData($data);

            try {
                $model->save();
                //if (!$id) {
                    $urlRewriteModel = $this->_urlRewriteFactory->create();
                    /* set current store id */
                    $urlRewriteModel->setStoreId(1);
                    /* this url is not created by system so set as 0 */
                    $urlRewriteModel->setIsSystem(0);
                    /* unique identifier - set random unique value to id path */
                    $urlRewriteModel->setIdPath(rand(1, 100000));
                    /* set actual url path to target path field */
                    $url_key = $this->getRequest()->getParam('url_key');
                    $name = $this->getRequest()->getParam('name');
                    if(empty($url_key)) $url_key = str_replace(' ', '-', strtolower($name));
                    $urlRewriteModel->setTargetPath("career/index/career/id/".$model->getId());
                    /* set requested path which you want to create */
                    $urlRewriteModel->setRequestPath("careers/".$url_key);
                    /* set current store id */
                    $urlRewriteModel->save();
                //}

                $this->messageManager->addSuccess(__('The Careers has been saved.'));
                $this->_objectManager->get('Magento\Backend\Model\Session')->setFormData(false);
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['career_id' => $model->getId(), '_current' => true]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Careers.'));
            }

            $this->_getSession()->setFormData($data);
            return $resultRedirect->setPath('*/*/edit', ['career_id' => $this->getRequest()->getParam('career_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}