<?php

    namespace Webkul\Mobikulwallet\Controller\Result;


    class Json extends \Magento\Framework\Controller\Result\Json    {

        public function getRawData()      {
            return $this->json;
        }

    }
