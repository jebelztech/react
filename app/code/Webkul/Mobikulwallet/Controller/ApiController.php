<?php
    /**
    * Webkul Software.
    *
    * @category  Webkul
    * @package   Webkul_Mobikulwallet
    * @author    Webkul
    * @copyright Copyright (c) 2010-2017 Webkul Software Private Limited (https://webkul.com)
    * @license   https://store.webkul.com/license.html
    */

    namespace Webkul\Mobikulwallet\Controller;
    // define("DS", DIRECTORY_SEPARATOR);
    use Webkul\MobikulCore\Helper\Data;
    use Magento\Store\Model\App\Emulation;
    use Magento\Framework\App\Action\Context;
    use Magento\Framework\Controller\ResultFactory;

    abstract class ApiController extends \Magento\Framework\App\Action\Action     {

        protected $_helper;
        protected $_emulate;

        public function __construct(
            Data $helper,
            Context $context,
            Emulation $emulate
        ) {
            $this->_helper  = $helper;
            $this->_emulate = $emulate;
            parent::__construct($context);
        }

        protected function getJsonResponse($responseContent = []){
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $resultJson->setData($responseContent);
            return $resultJson;
        }

    }