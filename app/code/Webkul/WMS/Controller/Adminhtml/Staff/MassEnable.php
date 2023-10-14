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
 * Class MassEnable staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassEnable extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * Execure function for mass enable class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $collection = $this->filter->getCollection(
            $this->collectionFactory->create()
        );
        $bannersUpdated = 0;
        $coditionArr = [];
        foreach ($collection->getAllIds() as $key => $staffId) {
            $currentStaff = $this->staffRepository->getById($staffId);
            $staffData = $currentStaff->getData();
            if (count($staffData)) {
                $condition = "`id`=".$staffId;
                array_push($coditionArr, $condition);
                $bannersUpdated++;
            }
        }
        $coditionData = implode(" OR ", $coditionArr);
        $collection->setStaffData($coditionData, ["status"=>1]);
        if ($bannersUpdated) {
            $this->messageManager->addSuccess(
                __(
                    "A total of %1 staff(s) were enabled.",
                    $bannersUpdated
                )
            );
        }
        return $resultRedirect->setPath("wms/staff/index");
    }
}
