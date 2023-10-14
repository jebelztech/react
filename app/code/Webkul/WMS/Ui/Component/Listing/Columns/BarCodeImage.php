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

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\DriverInterface;

/**
 * Class BarCodeImage warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class BarCodeImage extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * DriverInterface
     *
     * @var DriverInterface
     */
    private $driverInterface;

    /**
     * DirectoryList
     *
     * @var \Magento\Framework\Filesystem\DirectoryList
     */
    private $_baseDir;

    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $_storeManager;

    /**
     * Barcode
     *
     * @var \Webkul\WMS\Helper\Barcode
     */
    private $_helperBarcode;

    /**
     * Constructor
     *
     * @param \Webkul\WMS\Helper\Barcode                                   $helperBarcode      helperBarcode
     * @param \Magento\Framework\Filesystem\DirectoryList                  $dir                dir
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager       storeManager
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory uiComponentFactory
     * @param array                                                        $data               data
     * @param array                                                        $components         components
     */
    public function __construct(
        \Webkul\WMS\Helper\Barcode $helperBarcode,
        \Magento\Framework\Filesystem\DirectoryList $dir,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        DriverInterface $driverInterface = null,
        array $data = [],
        array $components = []
    ) {
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
        $this->driverInterface = $driverInterface ?? ObjectManager::getInstance()->get(
            \Magento\Framework\Filesystem\Driver\File::class
        );
        $this->_storeManager = $storeManager;
        $this->_helperBarcode = $helperBarcode;
        $this->_baseDir = $dir->getPath("media");
    }

    /**
     * Prepares DataSource
     *
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            $obManager = \Magento\Framework\App\ObjectManager::getInstance();
            $target = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
            $fieldName = $this->getData("name");
            $ioFile = $obManager->get(\Magento\Framework\Filesystem\Io\File::class);
            foreach ($dataSource["data"]["items"] as $key => $item) {
                $attribute = $item["hash"];
                $basePath = $this->_baseDir."/"."wms"."/"."tote"."/".$item["id"]."/";
                $fileName = str_replace(" ", "_", $attribute).".png";
                if (!$this->driverInterface->isFile($basePath.$fileName)) {
                    $ioFile->mkdir($basePath, 0777);
                    $path = $basePath.$fileName;
                    $this->_helperBarcode->generatebarcode(
                        $path,
                        $attribute,
                        100,
                        "horizontal",
                        "code128",
                        false,
                        1
                    );
                }
                $srcUrl = $target."wms"."/"."tote"."/".$item["id"]."/".$fileName;
                $dataSource["data"]["items"][$key][$fieldName."_src"] = $srcUrl;
                $dataSource["data"]["items"][$key][$fieldName."_alt"] = $attribute;
            }
        }
        return $dataSource;
    }
}
