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

namespace Webkul\WMS\Controller\Api;

use Magento\Framework\App\Request\InvalidRequestException;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;

/**
 * Login Class for staff login
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Login extends \Webkul\WMS\Controller\ApiController
{
    /**
     * Encryptor
     *
     * @var \Magento\Framework\Encryption\Encryptor
     */
    public $encryptor;

    /**
     * Media directory
     *
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $baseDir;

    /**
     * Device screen density factor
     *
     * @var float
     */
    protected $mFactor;

    /**
     * Customer Username
     *
     * @var string
     */
    protected $username;

    /**
     * Customer Password
     *
     * @var string
     */
    protected $password;

    /**
     * Icon width
     *
     * @var float
     */
    protected $iconWidth;

    /**
     * Icon hight
     *
     * @var float
     */
    protected $iconHeight;

    /**
     * DriverInterface
     *
     * @var DriverInterface
     */

    protected $driverInterface;

    /**
     * Icon width
     *
     * @var float
     */

    protected $deviceToken;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory
     */
    protected $staffCollection;

    /**
     * StaffRepository
     *
     * @var \Webkul\WMS\Model\StaffRepository
     */
    protected $staffRepository;

    /**
     * Constructor
     * @param \Webkul\WMS\Helper\Data                                 $helper          helper
     * @param \Magento\Framework\App\Action\Context                   $context         context
     * @param \Magento\Framework\Json\Helper\Data                     $jsonHelper      jsonHelper
     * @param \Magento\Framework\Filesystem\DirectoryList             $dir             dir
     * @param \Magento\Framework\Encryption\Encryptor                 $encryptor       encryptor
     * @param \Webkul\WMS\Model\StaffRepository                       $staffRepository staffRepository
     * @param \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection staffCollection
     * @param DriverInterface $driverInterface
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Framework\Encryption\Encryptor $encryptor,
        \Webkul\WMS\Model\StaffRepository $staffRepository,
        \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $staffCollection,
        DriverInterface $driverInterface = null
    ) {
        $this->driverInterface = $driverInterface ?? ObjectManager::getInstance()->get(
            \Magento\Framework\Filesystem\Driver\File::class
        );
        $this->encryptor = $encryptor;
        $this->baseDir = $dir->getPath("media");
        $this->staffCollection = $staffCollection;
        $this->staffRepository = $staffRepository;
        parent::__construct(
            $helper,
            $context,
            $jsonHelper
        );
    }

    /**
     * Matched Password with hash
     *
     * @param string $password
     * @param string $hash
     * @return void
     */
    private function _isValidPassword($password, $hash)
    {
        return $this->encryptor->isValidHash($password, $hash);
    }

    /**
     * Execute Function
     *
     * @return json
     */
    public function execute()
    {
        try {
            $validity = $this->verifyRequest();
            if ($validity) {
                return $validity;
            }
            $this->iconHeight = $this->iconWidth = 288 * $this->mFactor;
            if (!\Zend_Validate::is($this->username, "EmailAddress")) {
                throw new InvalidRequestException(__("Invalid Username."));
            }
            if ($this->password == "") {
                throw new InvalidRequestException(__("Invalid Password."));
            }
            $collection = $this->staffCollection->create()->addFieldToFilter(
                "status",
                1
            )->addFieldToFilter("email", $this->username);
            $staff = $collection->getFirstItem();
            if ($this->helper->isEnabledSingleStaffLogin() && $staff->getIsLoggedIn()) {
                throw new LocalizedException(__("Already logged in on some other device."));
            }
            $passwordHash = $staff->getPassword() ?? "";
            $isValid = $this->_isValidPassword($this->password, $passwordHash);
            if ($staff->getId() && $isValid) {
                $profileImage = $staff->getFilename();
                $staff = $this->staffRepository->getById($staff->getId());
                $this->returnArray["staffName"] = $staff->getName();
                $staffToken = $this->encryptor->hash(time(), 0);
                $staff->setDeviceToken($this->deviceToken)
                    ->setOs($this->os)
                    ->setStaffToken($staffToken)
                    ->setIsLoggedIn(1);
                $this->staffRepository->save($staff);
                $this->returnArray["staffToken"] = $staffToken;
                $newUrl = "";
                $basePath = $this->baseDir
                    .DS.$profileImage;
                if ($this->driverInterface->isFile($basePath)) {
                    $newPath = $this->baseDir
                        .DS."wms"
                        .DS."resized"
                        .DS.$this->iconWidth."x".$this->iconHeight
                        .DS.$profileImage;
                    $this->helper->resizeNCache(
                        $basePath,
                        $newPath,
                        $this->iconWidth,
                        $this->iconHeight
                    );
                    $newUrl = $this->helper->getUrl("media")."wms"
                        .DS."resized"
                        .DS.$this->iconWidth."x".$this->iconHeight
                        .DS.$profileImage;
                }
                $this->returnArray["avatar"] = $newUrl."?v=".time();
                $this->returnArray["success"] = true;
            } else {
                $this->returnArray["message"] = __("Invalid username or password.");
            }
            return $this->getJsonResponse($this->returnArray);
        } catch (\Exception $e) {
            $this->returnArray["message"] = __($e->getMessage());
            $this->helper->printLog($this->returnArray);
            return $this->getJsonResponse($this->returnArray);
        }
    }

    /**
     * Function verify Request to authenticate the request
     * Authenticates the request and logs the result for invalid requests
     *
     * @return Json
     */
    protected function verifyRequest()
    {
        if ($this->getRequest()->getMethod() == "POST" && $this->wholeData) {
            $this->os = $this->wholeData["os"] ?? "";
            $this->mFactor = $this->wholeData["mFactor"] ?? 1;
            $this->username = $this->wholeData["username"] ?? "";
            $this->password = $this->wholeData["password"] ?? "";
            $this->deviceToken = $this->wholeData["deviceToken"] ?? "";
            $this->mFactor = $this->helper->calcMFactor($this->mFactor);
        } else {
            $this->returnArray["message"] = __("Invalid Request");
            return $this->getJsonResponse($this->returnArray);
        }
    }
}
