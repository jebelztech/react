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

namespace Webkul\WMS\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class FormData for order
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class FormData extends \Magento\Sales\Controller\Adminhtml\Order
{
    /**
     * LayoutFactory
     *
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * Constructor class
     *
     * @param Action\Context                                   $context             context
     * @param \Magento\Framework\Registry                      $coreRegistry        coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory         fileFactory
     * @param \Magento\Framework\Translate\InlineInterface     $translateInline     translateInline
     * @param \Magento\Framework\View\Result\PageFactory       $resultPageFactory   resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory   resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory     $resultLayoutFactory resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory  $resultRawFactory    resultRawFactory
     * @param OrderManagementInterface                         $orderManagement     orderManagement
     * @param OrderRepositoryInterface                         $orderRepository     orderRepository
     * @param LoggerInterface                                  $logger              logger
     * @param \Magento\Framework\View\LayoutFactory            $layoutFactory       layoutFactory
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
        $this->layoutFactory = $layoutFactory;
        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $orderManagement,
            $orderRepository,
            $logger
        );
    }

    /**
     * Generate order assign info for ajax request
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $this->_initOrder();
        $layout = $this->layoutFactory->create();
        $html = $layout->createBlock(\Webkul\WMS\Block\Adminhtml\Order\View\Tab\Assign::class)
            ->toHtml();
        $this->_translateInline->processResponseBody($html);
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($html);
        return $resultRaw;
    }
}
