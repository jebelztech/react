<?php

namespace Leftor\Eps\Controller\Standard;

use at\externet\eps_bank_transfer;

class Response extends \Leftor\Eps\Controller\Eps
{

    public function execute()
    {
        $soCommunicator = new eps_bank_transfer\SoCommunicator();
        $soCommunicator->HandleConfirmationUrl(
            [$this, 'paymentConfirmationCallback'],
            null,
            'php://input',
            'php://output'
        );
    }

    public function paymentConfirmationCallback($plainXml, $bankConfirmationDetails)
    {
        $orderId = $bankConfirmationDetails->GetRemittanceIdentifier();

        if ($bankConfirmationDetails->GetStatusCode() == 'OK')
        {
            $response = $this->getRequest()->getParams();
            $comment = __("The order was paid");
            $this->updateOrder($orderId,'success',$comment);
            $this->makeInvoice($orderId,$response);
        }
        elseif($bankConfirmationDetails->GetStatusCode() == 'NOK')
        {
            $comment = __("The order was canceled");
            $this->updateOrder($orderId,'fail',$comment);
        }
        return true;
    }
}
