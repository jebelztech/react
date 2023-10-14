<?php
/**
 * Webkul Software.
 *
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */

namespace Webkul\WMS\Controller;

if (!defined("DS")) {
    define("DS", DIRECTORY_SEPARATOR);
}
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Request\InvalidRequestException;

/**
 * ApiController Class
 *
 * @category Webkul
 * @package  Webkul_WMS
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
abstract class ApiController extends \Magento\Framework\App\Action\Action implements
    \Magento\Framework\App\CsrfAwareActionInterface
{

    /**
     * WMS helper
     *
     * @var \Webkul\WMS\Helper\Data
     */
    protected $helper;

    /**
     * Array
     *
     * @var array
     */
    protected $wholeData;

    /**
     * Json helper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Api Response
     *
     * @var array
     */
    protected $returnArray = [];

    /**
     * Constructer method.
     *
     * @param \Webkul\WMS\Helper\Data               $helper     helper
     * @param \Magento\Framework\App\Action\Context $context    context
     * @param \Magento\Framework\Json\Helper\Data   $jsonHelper jsonHelper
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper
    ) {
        $this->helper = $helper;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context);
        $this->returnArray["success"] = false;
        $this->returnArray["message"] = "";
    }

    /**
     * Get config value by key.
     *
     * @param RequestInterface $request request
     *
     * @return $request
     */
    public function dispatch(
        RequestInterface $request
    ) {
        $this->headers = $this->getRequest()->getHeaders();
        $this->wholeData = $this->getRequest()->getParams();
        $this->helper->log(__CLASS__, "logClass", $this->wholeData);
        $this->helper->log($this->headers, "logHeaders", $this->wholeData);
        $this->helper->log($this->wholeData, "logParams", $this->wholeData);
        $returnArray = [];
        $returnArray["success"] = false;
        $returnArray["message"] = "";
        $authKey = $request->getHeader("authKey");
        $authData = $this->helper->isAuthorized($authKey);
        // if ($authData["code"] != 1) {
        //     return $this->getJsonResponse($returnArray, 401, $authData["token"]);
        // }
        return parent::dispatch($request);
    }

    /**
     * Return json response.
     *
     * @param array  $responseContent response content
     * @param string $responseCode    response code
     * @param string $token           token
     *
     * @return ResultFactory $resultJson
     */
    protected function getJsonResponse(
        $responseContent = [],
        $responseCode = "",
        $token = ""
    ) {
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        if ($responseCode == 304) {
            $resultJson->setHttpResponseCode(304);
            return $resultJson;
        } elseif ($responseCode == 401) {
            $resultJson->setHttpResponseCode(
                \Magento\Framework\Webapi\Exception::HTTP_UNAUTHORIZED
            );
        }
        if ($token != "") {
            $resultJson->setHeader("token", $token, true);
        }
        $resultJson->setData($responseContent);
        $this->helper->log($responseContent, "logResponse", $this->wholeData);
        return $resultJson;
    }

    /**
     * @inheritDoc
     */
    public function createCsrfValidationException(
        RequestInterface $request
    ): ?InvalidRequestException {
            return null;
    }

    /**
     * @inheritDoc
     */
    public function validateForCsrf(RequestInterface $request): ?bool
    {
        return true;
    }
}
