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

namespace Webkul\WMS\Model\Staff;

use \Magento\Framework\App\ObjectManager;
use \Webkul\WMS\Model\ResourceModel\Staff\CollectionFactory;

/**
 * Class DataProvider warehouse
 *
 * @author   Webkul <support@webkul.com>
 * @license  https://store.webkul.com/license.html ASL Licence
 * @link     https://store.webkul.com/license.html
 */
class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * SessionManagerInterface
     *
     * @var \Magento\Framework\Session\SessionManagerInterface
     */
    protected $session;

    /**
     * CollectionFactory
     *
     * @var CollectionFactory
     */
    protected $collection;

    /**
     * Array of data
     *
     * @var array
     */
    protected $loadedData;

    /**
     * Constructor
     *
     * @param string            $name              name
     * @param string            $primaryFieldName  primaryFieldName
     * @param string            $requestFieldName  requestFieldName
     * @param CollectionFactory $collectionFactory collectionFactory
     * @param array             $meta              meta
     * @param array             $data              data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->collection->addFieldToSelect("*");
    }

    /**
     * GetSession
     *
     * @return SessionManagerInterface
     */
    protected function getSession()
    {
        if ($this->session === null) {
            $this->session = ObjectManager::getInstance()->get(
                \Magento\Framework\Session\SessionManagerInterface::class
            );
        }
        return $this->session;
    }

    /**
     * GetData
     *
     * @return mixed
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $items = $this->collection->getItems();
        foreach ($items as $staff) {
            $result["staff"] = $staff->getData();
            $this->loadedData[$staff->getId()] = $result;
        }
        $data = $this->getSession()->getStaffFormData();
        $staffId = $data["wms_staff"]["id"] ?? null;
        if (!empty($data)) {
            $this->loadedData[$staffId] = $data;
            $this->getSession()->unsStaffFormData();
        }
        if (isset($this->loadedData[$staffId]["wms_staff"]["password"])) {
            unset($this->loadedData[$staffId]["wms_staff"]["password"]);
        }
        return $this->loadedData;
    }
}
