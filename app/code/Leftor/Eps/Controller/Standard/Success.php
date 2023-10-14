<?php

namespace Leftor\Eps\Controller\Standard;


class Success extends \Leftor\Eps\Controller\Eps
{
    public function execute()
    {
        $redirectUrl = $this->getCheckoutHelper()->getUrl('checkout/onepage/success');
        $this->getResponse()->setRedirect($redirectUrl);
    }
}