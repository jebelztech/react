<?php

namespace Leftor\Eps\Controller\Standard;

class Fail extends \Leftor\Eps\Controller\Eps
{

    public function execute()
    {
        return $this->resultPageFactory->create();
    }
}