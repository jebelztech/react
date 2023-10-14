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

namespace Webkul\WMS\Controller\Adminhtml;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as DirectoryManager;
use Magento\Framework\Encryption\EncryptorInterface as Encryptor;
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Downloadable\Helper\File as FileHelper;

/**
 * Staff Abstract Class
 *
 * @category Webkul
 * @package  Webkul_WMS
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
abstract class Staff extends \Magento\Backend\App\Action
{
    /**
     * FileHelper
     *
     * @var FileHelper
     */
    protected $fileHelper;

    /**
     * DriverInterface
     *
     * @var DriverInterface
     */
    protected $driverInterface;

    /**
     * Data
     *
     * @var \Webkul\WMS\Helper\Data
     */
    protected $helper;

    /**
     * Page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * Forward Factory
     *
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * StaffRepositoryInterface
     *
     * @var \Webkul\WMS\Api\StaffRepositoryInterface
     */
    protected $staffRepository;

    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * StaffInterfaceFactory
     *
     * @var \Webkul\WMS\Api\Data\StaffInterfaceFactory
     */
    protected $staffDataFactory;

    /**
     * JsonFactory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * TransportBuilder
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * StateInterface
     *
     * @var \Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $mediaDirectory;

    /**
     * UploaderFactory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploaderFactory;

    /**
     * @var DirectoryManager
     */
    protected $directoryManager;

    /**
     * Initialized Dependencies
     *
     * @param Encryptor $encryptor
     * @param DriverInterface $driverInterface
     * @param DirectoryManager $directoryManager
     * @param FileHelper $fileHelper
     * @param \Webkul\WMS\Helper\Data $helper
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Webkul\WMS\Api\StaffRepositoryInterface $staffRepository
     * @param \Webkul\WMS\Api\Data\StaffInterfaceFactory $staffDataFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $collectionFactory
     */
    public function __construct(
        Encryptor $encryptor,
        FileHelper $fileHelper,
        DirectoryManager $directoryManager,
        \Webkul\WMS\Helper\Data $helper,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Webkul\WMS\Api\StaffRepositoryInterface $staffRepository,
        \Webkul\WMS\Api\Data\StaffInterfaceFactory $staffDataFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory $collectionFactory,
        DriverInterface $driverInterface = null
    ) {
        parent::__construct($context);
        $this->date = $date;
        $this->helper = $helper;
        $this->filter = $filter;
        $this->fileHelper = $fileHelper;
        $this->encryptor = $encryptor;
        $this->storeManager = $storeManager;
        $this->coreRegistry = $coreRegistry;
        $this->driverInterface = $driverInterface ?? ObjectManager::getInstance()->get(
            \Magento\Framework\Filesystem\Driver\File::class
        );
        $this->staffRepository = $staffRepository;
        $this->directoryManager = $directoryManager;
        $this->staffDataFactory = $staffDataFactory;
        $this->formKeyValidator = $formKeyValidator;
        $this->transportBuilder = $transportBuilder;
        $this->collectionFactory = $collectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->inlineTranslation = $inlineTranslation;
        $this->fileUploaderFactory = $fileUploaderFactory;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Create a hash for the given password
     *
     * @param string $password
     * @return bool
     */
    protected function createPasswordHash($password)
    {
        return $this->encryptor->getHash($password, true);
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed("Webkul_WMS::staff");
    }
}
