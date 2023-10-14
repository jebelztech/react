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
 * Class Delete staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Delete extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * Execute function for delete Class
     *
     * @return page
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $formKeyIsValid = $this->formKeyValidator->validate($this->getRequest());
        $isPost = $this->getRequest()->isPost();
        if (!$formKeyIsValid || !$isPost) {
            $this->messageManager->addError(__("Staff could not be deleted."));
            return $resultRedirect->setPath("wms/staff/index");
        }
        $staffId = $this->initCurrentStaff();
        if (!empty($staffId)) {
            try {
                $this->staffRepository->deleteById($staffId);
                $this->messageManager->addSuccess(__("Staff has been deleted."));
            } catch (\Exception $exception) {
                $this->messageManager->addError($exception->getMessage());
            }
        }
        return $resultRedirect->setPath("wms/staff/index");
    }

    /**
     * Function to intialise current Staff
     *
     * @return int
     */
    protected function initCurrentStaff()
    {
        $staffId = (int)$this->getRequest()->getParam("id");
        if ($staffId) {
            $this->coreRegistry->register("id", $staffId);
        }
        return $staffId;
    }
}
