<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Smtp
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Etail\Career\Controller\Index;

use Exception;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Email\Model\Template\SenderResolver;
use Magento\Framework\App\Area;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\Store;
use Mageplaza\Smtp\Helper\Data as SmtpData;
use Mageplaza\Smtp\Mail\Rse\Mail;
use Psr\Log\LoggerInterface;

/**
 * Class Test
 * @package Mageplaza\Smtp\Controller\Adminhtml\Smtp
 */
class Email extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageplaza_Smtp::smtp';

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var SmtpData
     */
    protected $smtpDataHelper;

    /**
     * @var Mail
     */
    protected $mailResource;

    /**
     * @var TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * @var SenderResolver
     */
    protected $senderResolver;
    protected $scopeConfig;

    /**
     * Test constructor.
     *
     * @param Context $context
     * @param LoggerInterface $logger
     * @param SmtpData $smtpDataHelper
     * @param Mail $mailResource
     * @param TransportBuilder $transportBuilder
     * @param SenderResolver $senderResolver
     */
    public function __construct(
        Context $context,
        LoggerInterface $logger,
        SmtpData $smtpDataHelper,
        Mail $mailResource,
        TransportBuilder $transportBuilder,
        SenderResolver $senderResolver,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->logger            = $logger;
        $this->smtpDataHelper    = $smtpDataHelper;
        $this->mailResource      = $mailResource;
        $this->_transportBuilder = $transportBuilder;
        $this->senderResolver    = $senderResolver;
        $this->scopeConfig      = $scopeConfig; 

        parent::__construct($context);
    }

    /**
     * @return ResponseInterface|ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        $result = ['status' => false];

        $params = $this->getRequest()->getParams();
        if ($params && $params['to']) {
            /*$config = [
                'type'       => 'smtp',
                'host'       => $params['host'],
                'auth'       => $params['authentication'],
                'username'   => $params['username'],
                'ignore_log' => true,
                'force_sent' => true
            ];*/

            $storeId = 1;//$this->_storeManager->getStore()->getId();
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

                
            $protocol = $this->getConfig('smtp/configuration_option/protocol',$storeId);
            $ssl = $this->getConfig('smtp/configuration_option/ssl',$storeId);
            $port = $this->getConfig('smtp/configuration_option/port',$storeId);
            $password = $this->smtpDataHelper->getPassword();
            $return_path = $this->getConfig('smtp/configuration_option/returnpath',$storeId);

            $config = [
                'type'       => 'smtp',
                'host'       => $host,
                'auth'       => $authentication,
                'username'   => $username,
                'ignore_log' => true,
                'force_sent' => true
            ];

            $config['ssl'] = $protocol;

            $config['port'] = $port;
            
            $config['password'] = $password;

            //$config['return_path'] = "";

            $this->mailResource->setSmtpOptions($storeId, $config);

            $from = $this->senderResolver->resolve(
                'general',
                $this->smtpDataHelper->getScopeId()
            );
            //$from = array('name'=>"alsultansweets",'email'=>'noreplay@alsultansweets.ae');

            $this->_transportBuilder
                ->setTemplateIdentifier('mpsmtp_test_email_template')
                ->setTemplateOptions(['area' => Area::AREA_FRONTEND, 'store' => $storeId]) 
                ->setTemplateVars([])
                ->setFrom($from)
                ->addTo($params['to']);

            try {
                $this->_transportBuilder->getTransport()->sendMessage();

                $result = [
                    'status'  => true,
                    'config' => $config,
                    'content' => __('Sent successfully! Please check your email box.')
                ];
            } catch (Exception $e) {
                $result['content'] = $e->getMessage();
                $this->logger->critical($e);
            }
        } else {
            $result['content'] = __('Test Error');
        }
        $result['config'] = $config;
        return $this->getResponse()->representJson(SmtpData::jsonEncode($result));
    }
    public function getConfig($path, $storeId = null) {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
