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

namespace Webkul\WMS\Block\Adminhtml\Warehouse\Edit\Tab;

/**
 * Class Assignment for location
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Assignment extends \Magento\Backend\Block\Template
{
    /**
     * RequestContentInterface
     *
     * @var \Magento\Framework\App\RequestContentInterface
     */
    public $request;

    /**
     * RequestContentInterface
     *
     * @var \Webkul\WMS\Block\Adminhtml\Warehouse\Edit\Tab\ProductGrid
     */
    protected $blockGrid;

    /**
     * Jsonhelper
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * Template file
     *
     * @var string
     */
    protected $_template = "warehouse/product.phtml";

    /**
     * Constructor
     *
     * @param \Magento\Framework\Json\Helper\Data            $jsonHelper jsonHelper
     * @param \Magento\Backend\Block\Template\Context        $context    context
     * @param \Magento\Framework\App\RequestContentInterface $request    request
     * @param array                                          $data       data
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\RequestContentInterface $request,
        array $data = []
    ) {
        $this->request = $request;
        $this->jsonHelper = $jsonHelper;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                \Webkul\WMS\Block\Adminhtml\Warehouse\Edit\Tab\ProductGrid::class,
                "wms.warehouse.productgrid"
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }
}
