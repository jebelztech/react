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

use \Magento\Inventory\Model\ResourceModel\Source\Collection;

/**
 * Class Source msi
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class Source implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * Registry
     *
     * @var Collection
     */
    protected $sourceCollection;

    /**
     * Constructor
     *
     * @param Collection $sourceCollection sourceCollection
     */
    public function __construct(
        Collection $sourceCollection
    ) {
        $this->sourceCollection = $sourceCollection;
    }

    /**
     * Function toOptionArray
     *
     * @return Array
     */
    public function toOptionArray()
    {
        $returnData[] = [
            "label" => "Please Select",
            "value" => ""
        ];
        foreach ($this->sourceCollection as $each) {
            $returnData[] = [
                "value"=>$each->getSourceCode(),
                "label"=>$each->getName()
            ];
        }
        return $returnData;
    }
}
