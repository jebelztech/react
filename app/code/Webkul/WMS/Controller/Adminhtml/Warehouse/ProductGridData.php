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

namespace Webkul\WMS\Controller\Adminhtml\Warehouse;

/**
 * Class Assignment product grid
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class ProductGridData extends \Magento\Backend\App\Action
{
    /**
     * LayoutFactory
     *
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context      $context             context
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory resultLayoutFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * ProductGridData execute
     *
     * @return string
     */
    public function execute()
    {
        $resultLayout = $this->resultLayoutFactory->create();
        $this->getResponse()->setBody(
            $resultLayout
                ->getLayout()
                ->createBlock(
                    \Webkul\WMS\Block\Adminhtml\Warehouse\Edit\Tab\ProductGrid::class
                )
                ->toHtml()
        );
    }
}
