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

/**
 * Image Class warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Image extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * StoreManagerInterface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Constructer method.
     *
     * @param \Magento\Store\Model\StoreManagerInterface                   $storeManager       storeManager
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context            context
     * @param \Magento\Framework\View\Element\UiComponentFactory           $uiComponentFactory uiComponentFactory
     * @param array                                                        $components         components
     * @param array                                                        $data               data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct(
            $context,
            $uiComponentFactory,
            $components,
            $data
        );
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
            foreach ($dataSource["data"]["items"] as &$item) {
                $image = $item["filename"];
                $item[$fieldName."_html"] = "<img src='".$target.$image."'/>";
                $item[$fieldName."_src"]  = $target.$image;
            }
        }
        return $dataSource;
    }
}
