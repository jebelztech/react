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

namespace Webkul\WMS\Controller\Adminhtml\Staff;

/**
 * Class Mass delete to delete staff in massaction
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassDelete extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * Execute function for MassDelete class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $staffsDeleted = 0;
        foreach ($collection->getAllIds() as $staffId) {
            if (!empty($staffId)) {
                try {
                    $this->staffRepository->deleteById($staffId);
                    $staffsDeleted++;
                } catch (\Exception $exception) {
                    $this->messageManager->addError($exception->getMessage());
                }
            }
        }
        if ($staffsDeleted) {
            $this->messageManager->addSuccess(
                __(
                    "A total of %1 staff(s) were deleted.",
                    $staffsDeleted
                )
            );
        }
        return $resultRedirect->setPath("wms/staff/index");
    }
}
