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

namespace Webkul\WMS\Block\Adminhtml\Edit;

/**
 * Class GenericButton staff
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class GenericButton
{
    /*
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /*
     * UrlBuilder
     *
     * @var $urlBuilder
     */
    protected $urlBuilder;

    /**
     * Constructor
     *
     * @param \Magento\Framework\Registry           $registry registry
     * @param \Magento\Backend\Block\Widget\Context $context  context
     *
     * @return void
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Block\Widget\Context $context
    ) {
        $this->registry = $registry;
        $this->urlBuilder = $context->getUrlBuilder();
    }

    /**
     * Function to get banner Image Id
     *
     * @return int
     */
    public function getWarehouseId()
    {
        return $this->registry->registry("id");
    }

    /**
     * Function to get banner Image Id
     *
     * @return int
     */
    public function getStaffId()
    {
        return $this->registry->registry("id");
    }

    /**
     * Function to get Url
     *
     * @param string $route  route
     * @param array  $params params
     *
     * @return string
     */
    public function getUrl($route = "", $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
