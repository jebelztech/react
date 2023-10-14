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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Upload Class staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Upload extends \Webkul\WMS\Controller\Adminhtml\Staff
{
    /**
     * Execute function for class Upload
     *
     * @return json
     */
    public function execute()
    {
        $result = [];
        if ($this->getRequest()->isPost()) {
            try {
                $files  = $this->getRequest()->getFiles();
                $imageDirPath = $this->mediaDirectory->getAbsolutePath(
                    "wms/profileimages"
                );
                if (!$this->directoryManager->isExists($imageDirPath)) {
                    $this->driverInterface->createDirectory($imageDirPath, 0777);
                }
                $fileName = $files["wms_staff"]["filename"]["name"];
                $ext = substr($fileName, strrpos($fileName, ".") + 1);
                $newFileName = "File-".time().".".$ext;
                if (!in_array($ext, ["jpg", "jpeg", "gif", "png", "JPG", "JPEG", "GIF", "PNG"])) {
                    $result = ["error" => __("Disallowed file type."), "errorcode" => ""];
                    return $this->resultFactory->create(
                        ResultFactory::TYPE_JSON
                    )->setData($result);
                }
                $baseTmpPath = "wms/profileimages/";
                $target = $this->mediaDirectory->getAbsolutePath($baseTmpPath);
                $uploader = $this->fileUploaderFactory->create(["fileId" => "wms_staff[filename]"]);
                $uploader->setAllowedExtensions(["jpg", "jpeg", "gif", "png", "JPG", "JPEG", "GIF", "PNG"]);
                $uploader->setAllowRenameFiles(true);
                $result   = $uploader->save($target, $newFileName);
                if (!$result) {
                    $result = [
                        "error" => __("File can not be saved to the destination folder."),
                        "errorcode" => ""
                    ];
                }
                if (isset($result["file"])) {
                    try {
                        $store = $this->storeManager->getStore();
                        $filepath = $this->getFilePath(
                            $baseTmpPath,
                            $result["file"]
                        );
                        $url = $store->getBaseUrl(
                            \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
                        ).$filepath;
                        $result["tmp_name"] = str_replace("\\", "/", $result["tmp_name"]);
                        $result["path"] = str_replace("\\", "/", $result["path"]);
                        $result["url"] = $url;
                        $result["name"] = $result["file"];
                    } catch (\Exception $e) {
                        $result = ["error"=>$e->getMessage(), "errorcode"=>$e->getCode()];
                    }
                }
                $result["cookie"] = [
                    "name" => $this->_getSession()->getName(),
                    "value" => $this->_getSession()->getSessionId(),
                    "path" => $this->_getSession()->getCookiePath(),
                    "domain" => $this->_getSession()->getCookieDomain(),
                    "lifetime" => $this->_getSession()->getCookieLifetime()
                ];
            } catch (\Exception $e) {
                $result = ["error"=>$e->getMessage(), "errorcode"=>$e->getCode()];
            }
        }
        return $this->resultFactory
            ->create(ResultFactory::TYPE_JSON)
            ->setData($result);
    }

    /**
     * Gets file path
     *
     * @param string $path      path
     * @param string $imageName imageName
     *
     * @return string
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, "/")."/".ltrim($imageName, "/");
    }
}
