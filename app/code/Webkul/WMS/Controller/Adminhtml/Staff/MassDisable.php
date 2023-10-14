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
 * Class MassDisable staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassDisable extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * Execure function for mass disable class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $staffsUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $staffId) {
            $currentStaff = $this->staffRepository->getById($staffId);
            $staffData = $currentStaff->getData();
            if (count($staffData)) {
                $condition = "`id`=".$staffId;
                array_push($coditionArr, $condition);
                $staffsUpdated++;
            }
        }
        $coditionData = implode(" OR ", $coditionArr);
        $collection->setStaffData($coditionData, ["status" => 0]);
        if ($staffsUpdated) {
            $this->messageManager->addSuccess(
                __(
                    "A total of %1 staff(s) were disabled.",
                    $staffsUpdated
                )
            );
        }
        return $resultRedirect->setPath("wms/staff/index");
    }
}
