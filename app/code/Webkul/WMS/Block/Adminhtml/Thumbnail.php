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

namespace Webkul\WMS\Block\Adminhtml;

use \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;

/**
 * Class Thumbnail image
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Thumbnail extends AbstractRenderer
{
    /**
     * ImageFactory
     *
     * @var \Magento\Catalog\Helper\ImageFactory
     */
    protected $imageHelper;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Helper\ImageFactory $imageHelper imageHelper
     */
    public function __construct(
        \Magento\Catalog\Helper\ImageFactory $imageHelper
    ) {
        $this->imageHelper = $imageHelper;
    }

    /**
     * Function render
     *
     * @param \Magento\Framework\DataObject $row row
     *
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $imageUrl = $this->imageHelper
            ->create()
            ->init($row, "product_thumbnail_image")
            ->getUrl();
        $html  = '<img src="'.$imageUrl.'"/>';
        return $html;
    }
}
