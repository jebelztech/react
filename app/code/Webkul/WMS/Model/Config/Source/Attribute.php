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

namespace Webkul\WMS\Model\Config\Source;

/**
 * ProductBarCodeImage Class
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Attribute implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Attribute
     *
     * @var \Magento\Catalog\Model\ResourceModel\Eav\Attribute
     */
    protected $catalogAttribute;

    /**
     * Constructor
     *
     * @param \Magento\Catalog\Model\ResourceModel\Eav\Attribute $catalogAttribute catalogAttribute
     */
    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Eav\Attribute $catalogAttribute
    ) {
        $this->catalogAttribute = $catalogAttribute;
    }

    /**
     * Preapring attributes option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        $attributeCollection = $this->catalogAttribute
            ->getCollection()
            ->addFieldToFilter("frontend_input", "text")
            ->addFieldToFilter("is_unique", 1);
        $attributeList = [];
        foreach ($attributeCollection as $attribute) {
            $attributeList[] = [
                "value" => $attribute->getAttributeCode(),
                "label" => $attribute->getFrontendLabel()
            ];
        }
        return $attributeList;
    }
}
