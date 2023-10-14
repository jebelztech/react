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
 * Class MassAssignment of product
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class MassAssignment extends \Magento\Backend\Block\Template
{
    /**
     * FormKey
     *
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $formKey;

    /**
     * Template file
     *
     * @var string
     */
    protected $_template = "warehouse/upload.phtml";

    /**
     * Constructor
     *
     * @param \Magento\Framework\Data\Form\FormKey    $formKey formKey
     * @param \Magento\Backend\Block\Template\Context $context context
     * @param array                                   $data    data
     */
    public function __construct(
        \Magento\Framework\Data\Form\FormKey $formKey,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->formKey = $formKey;
        parent::__construct($context, $data);
    }

    /**
     * Get getSampleFileUrl
     *
     * @return string
     */
    public function getSampleFileUrl()
    {
        return $this->getUrl(
            "wms/warehouse/downloadsample",
            [
                "_current" => true
            ]
        );
    }

    /**
     * Get getCheckCsvUrl
     *
     * @return string
     */
    public function getCheckCsvUrl()
    {
        return $this->getUrl(
            "wms/warehouse/checkcsv",
            [
                "_current" => true
            ]
        );
    }

    /**
     * Get getImportCsvUrl
     *
     * @return string
     */
    public function getImportCsvUrl()
    {
        return $this->getUrl(
            "wms/warehouse/importcsv",
            [
                "_current" => true
            ]
        );
    }

    /**
     * Get form key
     *
     * @return string
     */
    public function getFormKey()
    {
         return $this->formKey->getFormKey();
    }
}
