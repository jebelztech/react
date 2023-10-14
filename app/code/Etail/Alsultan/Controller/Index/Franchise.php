<?php

namespace Etail\Alsultan\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Controller\Result\RedirectFactory;

class Franchise extends Action
{
    protected $dir;
    private $resultPageFactory;
    protected $transportBuilder;
    protected $storeManager;
    protected $inlineTranslation;
    protected $_scopeConfig;
    protected $request;
    protected $messageManager;
    protected $resultRedirectFactory;
    private $redirectFactory;
    public function __construct(
        Context $context,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        StateInterface $state,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\RequestInterface $request,
        RedirectFactory $resultRedirectFactory,
        PageFactory $resultPageFactory
    ) {
        $this->request = $request;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->redirectFactory = $resultRedirectFactory;
    }
    public function execute()
    {
        $adminEmail = $this->_scopeConfig ->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $adminName = $this->_scopeConfig ->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $params = $this->request->getParams();
        if(empty($params['firstname']) || empty($params['lastname']) || empty($params['email']) || empty($params['telephone']) || empty($params['address']) || empty($params['city']) || empty($params['country']) || empty($params['date']) || empty($params['territoryOwnership']) || empty($params['typefranchise'])) {
            /*$resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('franchise-with-us?msg=error'); 
            return $resultRedirect;*/
            return $this->_redirect($this->storeManager->getStore()->getBaseUrl().'franchise-with-us?msg=error');
        }

        // Sending email to customer //
        $templateId = '2'; // template id
        $fromEmail = $adminEmail;  // sender Email id
        $fromName = $adminName;
        $toEmail = $params['email']; // receiver email id
        $middlename = (!empty($params['middlename']))? ' '.$params['middlename'].' ' :'';
        $templateVars = [
                'name' => $params['firstname'].$middlename." ".$params['lastname'],
                'email' => $params['email'],
                'telephone' => $params['telephone'],
                'address' => $params['address'],
                'city' => $params['city'],
                'country' => $params['country'],
                'date' => $params['date'],
                'territoryOwnership' => $params['territoryOwnership'],
                'typefranchise' => $params['typefranchise']
            ];
        $this->sendEmail($templateId,$fromEmail,$fromName,$toEmail,$templateVars);


        $adminEmail = $this->_scopeConfig ->getValue('trans_email/ident_custom2/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $adminName = $this->_scopeConfig ->getValue('trans_email/ident_custom2/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        // Sending email to admin //
        $templateId = '3'; // template id
        $fromEmail = $params['email'];  // sender Email id
        $middlename = (!empty($params['middlename']))? ' '.$params['middlename'].' ' :'';
        $fromName = $params['firstname'].$middlename." ".$params['lastname'];
        $toEmail = $adminEmail; // receiver email id
        $templateVars = [
                'name' => $params['firstname'].$middlename." ".$params['lastname'],
                'email' => $params['email'],
                'telephone' => $params['telephone'],
                'address' => $params['address'],
                'city' => $params['city'],
                'country' => $params['country'],
                'date' => $params['date'],
                'territoryOwnership' => $params['territoryOwnership'],
                'typefranchise' => $params['typefranchise']
            ];
        $this->sendEmail($templateId,$fromEmail,$fromName,$toEmail,$templateVars);
        
        /*$resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('franchise-with-us');
        return $resultRedirect;*/
        return $this->_redirect($this->storeManager->getStore()->getBaseUrl().'franchise-with-us?msg=success');
    }
    public function sendEmail($templateId,$fromEmail,$fromName,$toEmail,$templateVars)
    {
        try {
            $storeId = $this->storeManager->getStore()->getId();
            $from = ['email' => $fromEmail, 'name' => $fromName];
            $this->inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];
            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
            //$this->messageManager->addSuccess(__("Success"));
            
        } catch (\Exception $e) {
            //$this->messageManager->addError(__("Error"))
            //echo $e->getMessage(); exit;
            //$this->_logger->info($e->getMessage());
        }
    }
}