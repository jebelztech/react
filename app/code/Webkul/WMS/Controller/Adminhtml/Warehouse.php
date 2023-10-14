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
use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Warehouse Abstract For warehouses
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
abstract class Warehouse extends \Magento\Backend\App\Action
{
    /**
     * DriverInterface
     *
     * @var DriverInterface
     */
    protected $driverInterface;
    
    /**
     * Csv
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csv;

    /**
     * DateTime
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * File
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    /**
     * Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * Filesystem
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $directory;

    /**
     * Forward Factory
     *
     * @var \Webkul\WMS\Model\ToteFactory
     */
    protected $toteFactory;

    /**
     * FileFactory
     *
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $fileFactory;

    /**
     * UploaderFactory
     *
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $fileUploader;

    /**
     * DirectoryList
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $directoryList;

    /**
     * ToteCollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory
     */
    protected $toteCollection;

    /**
     * Validator
     *
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $formKeyValidator;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\Warehouse\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * Page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * JsonFactory
     *
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * CollectionFactory
     *
     * @var \Webkul\WMS\Model\ResourceModel\ProductLocation\CollectionFactory
     */
    protected $locationCollection;

    /**
     * SampleFileProvider
     *
     * @var \Magento\ImportExport\Model\Import\SampleFileProvider
     */
    protected $sampleFileProvider;

    /**
     * ProductLocationRepository
     *
     * @var \Webkul\WMS\Model\ProductLocationRepository
     */
    protected $locationRepository;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * WarehouseRepositoryInterface
     *
     * @var \Webkul\WMS\Api\WarehouseRepositoryInterface
     */
    protected $warehouseRepository;

    /**
     * Forward Factory
     *
     * @var \Magento\Backend\Model\View\Result\ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * WarehouseInterfaceFactory
     *
     * @var \Webkul\WMS\Api\Data\WarehouseInterfaceFactory
     */
    protected $warehouseDataFactory;

    /**
     * ProductLocationFactory
     *
     * @var \Webkul\WMS\Model\ProductLocationFactory
     */
    protected $productLocationFactory;

    /**
     * Initialized Dependencies
     *
     * @param \Magento\Framework\File\Csv $csv
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Webkul\WMS\Model\ToteFactory $toteFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Filesystem\Io\File $ioFile
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploader
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Webkul\WMS\Model\ProductLocationRepository $locationRepository
     * @param \Webkul\WMS\Model\ProductLocationFactory $productLocationFactory
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Webkul\WMS\Api\WarehouseRepositoryInterface $warehouseRepository
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Webkul\WMS\Api\Data\WarehouseInterfaceFactory $warehouseDataFactory
     * @param \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory $toteCollection
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Webkul\WMS\Model\ResourceModel\Warehouse\CollectionFactory $collectionFactory
     * @param \Webkul\WMS\Model\ResourceModel\ProductLocation\CollectionFactory $locationCollection
     * @param DriverInterface $driverInterface
     * @param \Magento\ImportExport\Model\Import\SampleFileProvider $sampleFileProvider
     */
    public function __construct(
        \Magento\Framework\File\Csv $csv,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Filesystem $filesystem,
        \Webkul\WMS\Model\ToteFactory $toteFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploader,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Webkul\WMS\Model\ProductLocationRepository $locationRepository,
        \Webkul\WMS\Model\ProductLocationFactory $productLocationFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Webkul\WMS\Api\WarehouseRepositoryInterface $warehouseRepository,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Webkul\WMS\Api\Data\WarehouseInterfaceFactory $warehouseDataFactory,
        \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory $toteCollection,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Webkul\WMS\Model\ResourceModel\Warehouse\CollectionFactory $collectionFactory,
        \Webkul\WMS\Model\ResourceModel\ProductLocation\CollectionFactory $locationCollection,
        \Webkul\WMS\Model\OrderTotesFactory $orderTotesF,
        \Magento\Catalog\Model\ProductFactory $productF,
        \Webkul\WMS\Helper\GetSalableQtyBySkuSource $getSalableQtyBySkuSource,
        DriverInterface $driverInterface = null,
        \Magento\ImportExport\Model\Import\SampleFileProvider $sampleFileProvider = null
    ) {
        parent::__construct($context);
        $this->csv = $csv;
        $this->driverInterface = $driverInterface ?? ObjectManager::getInstance()->get(
            \Magento\Framework\Filesystem\Driver\File::class
        );
        $this->date = $date;
        $this->ioFile = $ioFile;
        $this->filter = $filter;
        $this->fileFactory = $fileFactory;
        $this->toteFactory = $toteFactory;
        $this->fileUploader = $fileUploader;
        $this->coreRegistry = $coreRegistry;
        $this->directoryList = $directoryList;
        $this->toteCollection = $toteCollection;
        $this->formKeyValidator = $formKeyValidator;
        $this->collectionFactory = $collectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->locationCollection = $locationCollection;
        $this->locationRepository = $locationRepository;
        $this->warehouseRepository = $warehouseRepository;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->warehouseDataFactory = $warehouseDataFactory;
        $this->sampleFileProvider = $sampleFileProvider
            ?: \Magento\Framework\App\ObjectManager::getInstance()
            ->get(\Magento\ImportExport\Model\Import\SampleFileProvider::class);
        $this->productF = $productF;
        $this->productLocationFactory = $productLocationFactory;
        $this->getSalableQtyBySkuSource = $getSalableQtyBySkuSource;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->orderTotesF = $orderTotesF;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed("Webkul_WMS::warehouse");
    }
}
