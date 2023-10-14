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

namespace Webkul\WMS\Ui\Component\Listing\Columns;

use Magento\Framework\Filesystem\DriverInterface;
use Magento\Framework\App\ObjectManager;

/**
 * OrderBarCodeImage Class warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class OrderBarCodeImage extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * File
     *
     * @var DriverInterface
     */
    protected $driverInterface;

    /**
     * File
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $io;

    /**
     * DirectoryList
     *
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    protected $baseDir;

    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Barcode
     *
     * @var \Webkul\WMS\Helper\Barcode
     */
    protected $helperBarcode;

    /**
     * Constructor
     * @param \Magento\Framework\Filesystem\Io\File                        $io                 io
     * @param \Webkul\WMS\Helper\Barcode                                   $helperBarcode      helperBarcode
     * @param \Magento\Framework\Filesystem\DirectoryList                  $dir                dir
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager       storeManager
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory uiComponentFactory
     * @param array                                                        $data               data
     * @param array                                                        $components         components
     */
    public function __construct(
        \Magento\Framework\Filesystem\Io\File $io,
        \Webkul\WMS\Helper\Barcode $helperBarcode,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        DriverInterface $driverInterface = null,
        array $data = [],
        array $components = []
    ) {
        $this->io = $io;
        $this->driverInterface = $driverInterface ?? ObjectManager::getInstance()->get(
            \Magento\Framework\Filesystem\Driver\File::class
        );
        $this->storeManager = $storeManager;
        $this->helperBarcode = $helperBarcode;
        $this->baseDir = $dir->getPath("media");
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Preapring data source for image
     *
     * @param array $dataSource dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            $target = $this->storeManager->getStore()->getBaseUrl(
                \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            );
            $fieldName = $this->getData("name");
            foreach ($dataSource["data"]["items"] as $key => $item) {
                $attribute = $item["increment_id"];
                $basePath = $this->baseDir."/"."wms"."/"."order"."/".$item["entity_id"]."/";
                $fileName = str_replace(" ", "_", $attribute).".png";
                if (!$this->driverInterface->isFile($basePath.$fileName)) {
                    $this->io->mkdir($basePath, 0777);
                    $path = $basePath.$fileName;
                    $this->helperBarcode->generatebarcode(
                        $path,
                        $attribute,
                        100,
                        "horizontal",
                        "code128",
                        false,
                        1
                    );
                }
                $srcUrl = $target."wms"."/"."order"."/".$item["entity_id"]."/".$fileName;
                $dataSource["data"]["items"][$key][$fieldName."_src"] = $srcUrl;
                $dataSource["data"]["items"][$key][$fieldName."_alt"] = $attribute;
            }
        }
        return $dataSource;
    }
}
