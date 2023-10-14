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

namespace Webkul\WMS\Block;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Class PrintAction
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Barcode extends \Magento\Backend\Block\Template
{
    /**
     * Helper Data
     *
     * @var \Webkul\WMS\Helper\Data
     */
    public $helper;

    /**
     * File
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    public $ioFile;

    /**
     * Filter
     *
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    public $filter;

    /**
     * Http
     *
     * @var \Magento\Framework\App\Request\Http
     */
    public $request;

    /**
     * DriverInterface
     *
     * @var DriverInterface
     */
    public $driverInterface;

    /**
     * ToteFactory
     *
     * @var \Webkul\WMS\Model\ToteFactory
     */
    public $toteFactory;

    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;

    /**
     * DirectoryList
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    public $directoryList;

    /**
     * Barcode
     *
     * @var \Webkul\WMS\Helper\Barcode
     */
    public $helperBarcode;

    /**
     * Tote Collection
     *
     * @var \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory
     */
    public $toteCollection;

    /**
     * Order CollectionFactory
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    public $orderCollection;

    /**
     * Product Collection
     *
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    public $collectionFactory;

    /**
     * Constructor
     *
     * @param DriverInterface                                                $driverInterface
     * @param \Webkul\WMS\Helper\Data                                        $helper
     * @param \Webkul\WMS\Helper\Barcode                                     $helperBarcode
     * @param \Webkul\WMS\Model\ToteFactory                                  $toteFactory
     * @param \Magento\Framework\App\Request\Http                            $request
     * @param \Magento\Framework\Filesystem\Io\File                          $ioFile
     * @param \Magento\Ui\Component\MassAction\Filter                        $filter
     * @param \Magento\Backend\Block\Template\Context                        $context
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager
     * @param \Magento\Framework\App\Filesystem\DirectoryList                $directoryList
     * @param \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory         $toteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory     $orderCollection
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     */
    public function __construct(
        \Webkul\WMS\Helper\Data $helper,
        \Webkul\WMS\Helper\Barcode $helperBarcode,
        \Webkul\WMS\Model\ToteFactory $toteFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Webkul\WMS\Model\ResourceModel\Tote\CollectionFactory $toteCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        DriverInterface $driverInterface = null
    ) {
        $this->driverInterface = $driverInterface ?? ObjectManager::getInstance()->get(
            \Magento\Framework\Filesystem\Driver\File::class
        );
        $this->helper = $helper;
        $this->ioFile = $ioFile;
        $this->filter = $filter;
        $this->request = $request;
        $this->toteFactory = $toteFactory;
        $this->storeManager = $storeManager;
        $this->directoryList = $directoryList;
        $this->helperBarcode = $helperBarcode;
        $this->toteCollection = $toteCollection;
        $this->orderCollection = $orderCollection;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }
}
