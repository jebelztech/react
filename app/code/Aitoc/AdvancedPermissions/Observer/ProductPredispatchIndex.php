<?php
/**
 * @author Aitoc Team
 * @copyright Copyright (c) 2022 Aitoc (https://www.aitoc.com)
 * @package Aitoc_AdvancedPermissions
 */


namespace Aitoc\AdvancedPermissions\Observer;

use Aitoc\AdvancedPermissions\Helper\Data;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class ProductPredispatchIndex extends AbstractPredispatchIndex implements ObserverInterface
{
    /**
     * @param Observer $observer
     * @return $this|void
     */
    public function execute(Observer $observer)
    {
        $params = $this->request->getParams();

        if (isset($params['id']) && $params['id']) {
            return $this->redirectIfNeeded($observer, 'catalog/product/edit', ['id' => $params['id']]);
        }

        return $this;
    }
}
