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

namespace Webkul\WMS\Controller\Adminhtml;

/**
 * Tote Abstract Class for Tote manipulation
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
abstract class Tote extends \Magento\Backend\App\Action
{
    /**
     * Page Factory
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context        $context           context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Check for is allowed
     *
     * @return boolean
     */
    protected function isAllowed()
    {
        return $this->_authorization->isAllowed("Webkul_WMS::tote");
    }
}
