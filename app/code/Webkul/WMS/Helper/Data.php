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

namespace Webkul\WMS\Helper;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Helper Data Class
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_SINGLE_STAFF_LOGIN = "wms/configuration/enablesinglelogin";
    /**
     * File
     *
     * @var \Magento\Framework\Filesystem\Driver\File
     */
    protected $file;

    /**
     * Encrypted
     *
     * @var \Magento\Config\Model\Config\Backend\Encrypted
     */
    protected $encrypted;

    /**
     * Encryptor
     *
     * @var \Magento\Framework\Encryption\Encryptor
     */
    protected $encryptor;

    /**
     * Image
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * DirectoryList
     *
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * SessionManagerInterface
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * Json helper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * DriverInterface
     *
     * @var DriverInterface
     */
    protected $driverInterface;

    /**
     * ProductFactory
     *
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;

    /**
     * Initialized dependencies
     *
     * @param DriverInterface $driverInterface
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Image\Factory $imageFactory
     * @param \Magento\Framework\Filesystem\Driver\File $file
     * @param \Magento\Framework\Encryption\Encryptor $encryptor
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Config\Model\Config\Backend\Encrypted $encrypted
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Image\Factory $imageFactory,
        \Magento\Framework\Filesystem\Driver\File $file,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Config\Model\Config\Backend\Encrypted $encrypted,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        DriverInterface $driverInterface = null
    ) {
        $this->file = $file;
        $this->jsonHelper = $jsonHelper;
        $this->encrypted = $encrypted;
        $this->encryptor = $encryptor;
        $this->imageFactory = $imageFactory;
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->productFactory = $productFactory;
        $this->sessionManager = $sessionManager;
        $this->driverInterface = $driverInterface ?? ObjectManager::getInstance()->get(
            \Magento\Framework\Filesystem\Driver\File::class
        );
        parent::__construct($context);
    }

    /**
     * Function to authorize the auth key
     *
     * @param string $authKey authkey
     *
     * @return array auth data
     */
    public function isAuthorized($authKey)
    {
        $authData = [];
        $authData["code"] = 2;
        $authData["token"] = "";
        $apiKey = $this->getPassword();
        $apiUsername = $this->getConfigData("wms/configuration/apiusername");
        $sessionToken = $this->sessionManager->getSessionId();
        $H1 = hash("sha256", $apiUsername.":".$apiKey);
        $H2 = hash("sha256", $H1.":".$sessionToken);
        if ($authKey == $H2) {
            $authData["code"] = 1;
        } else {
            $authData["token"] = $sessionToken;
        }
        return $authData;
    }

    /**
     * Get Password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->encrypted->processValue(
            $this->getConfigData("wms/configuration/apikey")
        );
    }

    /**
     * Logs data to file
     *
     * @param array  $data      data
     * @param string $key       key
     * @param array  $wholeData wholeData
     *
     * @return null
     */
    public function log($data, $key, $wholeData)
    {
        $flag = $wholeData[$key] ?? 0;
        $this->printLog($data, $flag);
    }

    /**
     * PrintLog to file
     *
     * @param array   $data     data
     * @param integer $flag     flag
     * @param string  $filename to change log file
     *
     * @return null
     */
    public function printLog($data, $flag = 1, $filename = "wms.log")
    {
        if ($flag == 1) {
            $logger = new \Zend_Log();
            $path   = $this->directoryList->getPath("var");
            if (!$this->file->isExists($path."/log/")) {
                $this->file->createDirectory($path."/log/");
            }
            $logger->addWriter(new \Zend_Log_Writer_Stream($path."/log/".$filename));
            if (is_array($data) || is_object($data)) {
                $data = $this->jsonHelper->jsonEncode($data);
            }
            $logger->info($data);
        }
    }

    /**
     * PrintLog to file
     *
     * @param string         $path  data
     * @param scopeinterface $scope flag
     *
     * @return string
     */
    public function getConfigData(
        $path,
        $scope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE
    ) {
        return $this->scopeConfig->getValue($path, $scope);
    }

    /**
     * Function to validate mFactor value
     *
     * @param float $mFactor original value of mFactor
     *
     * @return float a valid value for mFactor
     */
    public function calcMFactor($mFactor)
    {
        if ($mFactor == 0) {
            $mFactor = 1;
        }
        return ceil($mFactor) > 2 ? 2 : ceil($mFactor);
    }

    /**
     * Function to get Url of directory
     *
     * @param string $dir directory
     *
     * @return string
     */
    public function getUrl($dir)
    {
        return $this->storeManager->getStore()->getBaseUrl($dir);
    }

    /**
     * Function to get resize image and create cache for that image
     *
     * @param string  $basePath    base path
     * @param string  $newPath     destination path
     * @param integer $width       width of the image type of image
     * @param integer $height      height of the image type of image
     * @param bool    $forCustomer is for customer or not
     *
     * @return string
     */
    public function resizeNCache(
        $basePath,
        $newPath,
        $width,
        $height,
        $forCustomer = false
    ) {
        if (!$this->driverInterface->isFile($newPath) || $forCustomer) {
            $imageObj = $this->imageFactory->create($basePath);
            $imageObj->keepAspectRatio(false);
            $imageObj->backgroundColor([255, 255, 255]);
            $imageObj->keepFrame(false);
            $imageObj->resize($width, $height);
            $imageObj->save($newPath);
        }
    }

    /**
     * Function to load product
     *
     * @param integer $id product id
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function loadAssociatedProduct($id)
    {
        return $this->productFactory->create()->load($id);
    }

    /**
     * @return bool
     */
    public function isEnabledSingleStaffLogin()
    {
        return $this->getConfigData(self::XML_PATH_SINGLE_STAFF_LOGIN);
    }
}
