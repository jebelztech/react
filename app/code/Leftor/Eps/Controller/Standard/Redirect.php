<?php

namespace Leftor\Eps\Controller\Standard;

use at\externet\eps_bank_transfer;

class Redirect extends \Leftor\Eps\Controller\Eps
{
    public function execute()
    {
        if($order = $this->getOrder()->isEmpty())
        {
            $this->getResponse()->setStatusHeader(403, '1.1', 'Forbidden');
        } 
        else 
        {
            $quote = $this->getQuote();
            $order = $this->getOrder();
            $amount = round($order->getGrandTotal(),2)*100;

			
			
            // Connection credentials
            $userID = $this->_paymentMethod->getUserId();
            $pin    = $this->_paymentMethod->getPin();
            $bic    = $this->_paymentMethod->getBic();
            $iban   = $this->_paymentMethod->getIban();
            $targetUrl = $this->_paymentMethod->getTargetUrl();

            // Return urls
            $confirmUrl = $this->getCheckoutHelper()->getUrl('eps/standard/response?id='.$order->getRealOrderId());
            $successUrl = $this->getCheckoutHelper()->getUrl('eps/standard/success');
            $failUrl = $this->getCheckoutHelper()->getUrl('eps/standard/fail');

            $transferMsgDetails = new eps_bank_transfer\TransferMsgDetails(
                $confirmUrl,
                $successUrl,
                $failUrl
            );

            $transferInitiatorDetails = new eps_bank_transfer\TransferInitiatorDetails(
                $userID,
                $pin,
                $bic,
                $order->getCustomerName(),
                $iban,
                $order->getRealOrderId(),
                intval($amount),
                $transferMsgDetails
            );

            $transferInitiatorDetails->RemittanceIdentifier = $order->getRealOrderId();
            $transferInitiatorDetails->SetExpirationMinutes(60);     // Sets ExpirationTimeout. Value must be between 5 and 60

            //Include information about one or more articles
            $items = $order->getItems();
            foreach ($items as $item) {
                $article = new eps_bank_transfer\WebshopArticle(
                    $item->getName(),
                    strstr($item->getQtyOrdered(), '.', true),
                    intval(round($item->getPrice(),2)*100)
                );
                $transferInitiatorDetails->WebshopArticles[] = $article;
            }

            // Send TransferInitiatorDetails to Scheme Operator
            $soCommunicator = new eps_bank_transfer\SoCommunicator();
			
			
            $plain = $soCommunicator->SendTransferInitiatorDetails($transferInitiatorDetails, $targetUrl);

            $xml = new \SimpleXMLElement($plain);
            $soAnswer = $xml->children(eps_bank_transfer\XMLNS_epsp);
            $errorDetails = $soAnswer->BankResponseDetails->ErrorDetails;

            if (('' . $errorDetails->ErrorCode) != '000')
            {
                $errorCode = '' . $errorDetails->ErrorCode;
                $errorMsg = '' . $errorDetails->ErrorMsg;
                print "Error Code: ".$errorCode." ErrorMsg: ".$errorMsg;
            }
            else
            {
				$redirectUrl = trim($soAnswer->BankResponseDetails->ClientRedirectUrl->__toString());
                $this->getResponse()->setRedirect($redirectUrl);
            }
        }
    }
}