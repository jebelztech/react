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

namespace Webkul\WMS\Controller\Adminhtml\Tote;

/**
 * Class MassPrint totes
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassPrint extends \Webkul\WMS\Controller\Adminhtml\Tote
{
    /**
     * Execute Method
     *
     * @return pagefactory
     */
    public function execute()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu("Webkul_WMS::warehouse");
        $resultPage->addBreadcrumb(__("WMS"), __("Barcode"));
        $resultPage->getConfig()->getTitle()->prepend(__("Tote Barcodes"));
        return $resultPage;
    }
}
