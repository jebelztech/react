<?php

namespace Etail\Career\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Escaper;
use Magento\Email\Model\Template\SenderResolver;
use Mageplaza\Smtp\Helper\Data as SmtpData;
use Mageplaza\Smtp\Mail\Rse\Mail;
use Magento\Framework\App\Area;

class Career extends \Magento\Framework\App\Action\Action
{
    protected $_inlineTranslation;
    protected $_transportBuilder;
    protected $_transportBuilder1;
    protected $_template;
    protected $_storeManager;
    private $fileSystem;
    protected $_mediaDirectory;
    protected $_fileUploaderFactory;
    private $redirectFactory;
    protected $escaper;
    protected $logger;
    protected $senderResolver;
    protected $mailResource;
    protected $smtpDataHelper;
    protected $scopeConfig;

    const TYPE_OCTETSTREAM         = 'application/octet-stream';
    const DISPOSITION_ATTACHMENT   = 'attachment';
    const ENCODING_BASE64          = 'base64';
    public function __construct(
        Context $context,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder1,
        \Etail\Career\Model\Mail\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        PageFactory $resultPageFactory,
        RedirectFactory $redirectFactory,
        Escaper $escaper,
        SmtpData $smtpDataHelper,
        Mail $mailResource,
        SenderResolver $senderResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->_inlineTranslation = $inlineTranslation;
        $this->_transportBuilder = $transportBuilder;
        $this->_transportBuilder1 = $transportBuilder1;
        $this->_storeManager = $storeManager;
        $this->fileSystem = $filesystem;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->redirectFactory = $redirectFactory;
        $this->escaper = $escaper;
        $this->smtpDataHelper    = $smtpDataHelper;
        $this->mailResource      = $mailResource;
        $this->senderResolver    = $senderResolver;
        $this->scopeConfig      = $scopeConfig;       
    }
    public function execute()
    {
        if($this->getRequest()->getParam('career_detail')) {
            $this->submitCareer();
        }
        else {
            if($this->getRequest()->getParam('id')) { 
                $this->_view->loadLayout();
                $this->_view->getLayout()->initMessages();
                $this->_view->renderLayout();  
            }
            else {
                header('Location: '."https://alsultansweets.ae/career");
                exit;
                $redirect = $this->redirectFactory->create();
                $redirect->setPath("https://alsultansweets.ae/career");
                return $redirect;
            }
        }
        
    }
    public function setSmtpOptionsNew() {
        $storeId = $this->_storeManager->getStore()->getId();
        $host = $this->getConfig('smtp/configuration_option/host',$storeId);
        $username = $this->getConfig('smtp/configuration_option/username',$storeId);
        $password = $this->getConfig('smtp/configuration_option/password',$storeId);
        $authentication = $this->getConfig('smtp/configuration_option/authentication',$storeId);

        $config = [
            'type'       => 'smtp',
            'host'       => $host,
            'auth'       => $authentication,
            'username'   => $username,
            'ignore_log' => true,
            'force_sent' => true
        ];
            
        $config['ssl'] = $this->getConfig('smtp/configuration_option/protocol',$storeId);;
        $config['port'] = $this->getConfig('smtp/configuration_option/port',$storeId);
        $config['password'] = $this->smtpDataHelper->getPassword();

        return $config;
    }
    public function getConfig($path, $storeId = null) {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
    public function submitCareer()
    {
        $params = $this->getRequest()->getParams();
        $post = $this->getRequest()->getPostValue();
        //echo "<pre>"; print_r($post); exit;
        $pdfFile = 'pdf_file_path/email.pdf';
        $this->_template  = 7;

        $this->_inlineTranslation->suspend();
        $this->_mediaDirectory = $this->fileSystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        try{
            $config = $this->setSmtpOptionsNew();
            $this->mailResource->setSmtpOptions($this->_storeManager->getStore()->getId(), $config);

            $target = $this->_mediaDirectory->getAbsolutePath('career/email_attachment/');        
            $uploader = $this->_fileUploaderFactory->create(['fileId' => 'resume']);
            $uploader->setAllowedExtensions(['docx','pdf']);
            $uploader->setAllowRenameFiles(true);
            $result = $uploader->save($target);

            if ($result['file']) {

                
                //$emailTemplateVariables = array('firstname'=>$post['firstname'],'lastname'=>$post['lastname'],'email'=>$post['email'],'phone'=>$post['phone'],'city'=>$post['city']);
                $emailTemplateVariables['firstname'] = $post['firstname'];
                $emailTemplateVariables['lastname'] = $post['lastname'];
                $emailTemplateVariables['email'] = $post['email'];
                $emailTemplateVariables['phone'] = $post['phone'];
                $emailTemplateVariables['city'] = $post['city'];
                $emailTemplateVariables['jobtitle'] = $post['job_title'];

                $uploaderPort = $this->_fileUploaderFactory->create(['fileId' => 'portfolio']);
                $uploaderPort->setAllowedExtensions(['docx','pdf']);
                $uploaderPort->setAllowRenameFiles(true);
                $resultPort = $uploaderPort->save($target);

                $file = $result['path'] . $result['file'];
                $transportBuilderCustomer = $this->_transportBuilder;
                $transportBuilder = $this->_transportBuilder;
                //$from = array('name'=>"alsultansweets",'email'=>'noreplay@alsultansweets.ae');
                $from = $this->senderResolver->resolve(
                    'general',
                    $this->smtpDataHelper->getScopeId()
                );
                $transportBuilder->setTemplateIdentifier($this->_template)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($from)
                    ->addTo('careers@alsultansweets.ae', 'AL Sultan Career');
                    //->addTo('aimanelkhatib@gmail.com', 'AL Sultan Career')
                    //->addTo('mohammad.n@ezmartech.com');
                    //->addAttachment(file_get_contents($file),$result['name'],self::TYPE_OCTETSTREAM);

                if ($resultPort['file']) {
                    $filePort = $resultPort['path'] . $resultPort['file'];
                    $transportBuilder->addAttachment(file_get_contents($filePort),$resultPort['name'],self::TYPE_OCTETSTREAM);
                }

                $transportBuilder->getTransport()->sendMessage();
                $this->_inlineTranslation->resume();
                $receipient_template = 9;
                $transportBuilderCustomer->setTemplateIdentifier($receipient_template)
                    ->setTemplateOptions(
                        [
                            'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                            'store' => $this->_storeManager->getStore()->getId(),
                        ]
                    )
                    ->setTemplateVars($emailTemplateVariables)
                    ->setFrom($from)
                    ->addTo($post['email']);
                    //->addAttachment(file_get_contents($file),$result['name'],self::TYPE_OCTETSTREAM);

                $transportBuilderCustomer->getTransport()->sendMessage();
                $this->_inlineTranslation->resume();

                $this->messageManager->addSuccess("Thanks, we will update you soon.");
                header('Location: '."https://alsultansweets.ae/career");
                exit;
            }
            else {
                $this->messageManager->addError("Invalid file");
            }
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
    }
}