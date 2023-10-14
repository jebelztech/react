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

use Magento\Framework\UrlInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class ViewAction warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ViewOrder extends Column
{
    /**
     * UrlInterface
     *
     * @var UrlInterface $urlBuilder
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param ContextInterface   $context            context
     * @param UiComponentFactory $uiComponentFactory uiComponentFactory
     * @param UrlInterface       $urlBuilder         urlBuilder
     * @param array              $components         components
     * @param array              $data               data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Data source
     *
     * @param array $dataSource datasource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource["data"]["items"])) {
            foreach ($dataSource["data"]["items"] as &$item) {
                if (isset($item["order_id"])) {
                    $viewUrlPath = $this->getData("config/viewUrlPath") ?: "#";
                    $urlEntityParamName = $this->getData("config/urlEntityParamName") ?: "order_id";
                    $item[$this->getData("name")] = [
                        "view" => [
                            "href"  => $this->urlBuilder->getUrl(
                                $viewUrlPath,
                                [$urlEntityParamName=>$item["order_id"]]
                            ),
                            "label" => __("View")
                        ]
                    ];
                }
            }
        }
        return $dataSource;
    }
}
