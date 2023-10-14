<?php

namespace Leftor\Eps\Block;

use Magento\Framework\View\Element\Template;

class EpsFail extends Template
{
    protected $_request;

    public function __construct(
        Template\Context $context,
        \Magento\Framework\App\Request\Http $request,
        array $data = []
    )
    {
        $this->_request = $request;
        parent::__construct($context, $data);
    }

    public function getErrorMsg($errorCode)
    {
        $errors = array(
            'ERROR1' => __('The payment confirmation could not be transmitted to the merchant. Please contact the merchant.'),
            'ERROR2' => __('A technical error occurred. Your order could not be executed.'),
            'ERROR3' => __('You have canceled the payment of your order.')
        );
        return $errors[$errorCode];
    }

    public function getError()
    {
        $errorCode = $this->_request->getParam('epserrorcode');

        return $this->getErrorMsg($errorCode);
    }
}