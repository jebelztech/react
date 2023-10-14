<?php
/**
 * Webkul Software.
 *
 *
 * @category  Webkul
 * @package   Webkul_DeliveryBoy
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
namespace Webkul\DeliveryBoy\Helper;

class DeliveryAutomation extends \Magento\Framework\App\Helper\AbstractHelper
{
    private $addressConfig;
    private $jsonHelper;
    private $fileDriver;
    private $logger;
    private $orderLocationF;
    private $deliveryboyHelper;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Session\SessionManagerInterface $sessionManager
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        \Psr\Log\LoggerInterface $logger,
        \Webkul\DeliveryBoy\Model\OrderLocationFactory $orderLocationF,
        \Webkul\DeliveryBoy\Helper\Data $deliveryboyHelper
    ) {
        parent::__construct($context);

        $this->addressConfig = $addressConfig;
        $this->jsonHelper = $jsonHelper;
        $this->fileDriver = $fileDriver;
        $this->logger = $logger;
        $this->orderLocationF = $orderLocationF;
        $this->deliveryboyHelper = $deliveryboyHelper;
    }

    /**
     * @return bool
     */
    public function isSortDeliveryBoyNearestDistanceEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            ModuleGlobalConstants::XML_PATH_SORT_DELIVERYBOY_BY_NEAREST_DISTANCE
        );
    }

    /**
     * @return bool
     */
    public function isAutoAssignNearestDeliveryBoyEnabled()
    {
        return (bool)$this->scopeConfig->getValue(
            ModuleGlobalConstants::XML_PATH_AUTO_ASSIGN_NEAREST_DELIVERYBOY
        );
    }

    public function getDistanceBetTwoPoints($from, $to, $radiusUnit = 'km')
    {
        $earthRadius = 6371; // km
        $dLat = ((float)$from['latitude'] - (float)$to['latitude']) * M_PI / 180;
        $dLon = ((float)$from['longitude'] - (float)$to['longitude']) * M_PI / 180;
        $lat1 = (float)$to['latitude'] * M_PI / 180;
        $lat2 = (float)$from['latitude'] * M_PI / 180;
     
        $a = sin($dLat/2) * sin($dLat/2) + sin($dLon/2) * sin($dLon/2) * cos($lat1) * cos($lat2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $d = $earthRadius * $c;
        if ($radiusUnit == 'mile') {
            $m = $d * 0.621371; //for milles
            return $m;
        }
        return $d;
    }

    public function sortDeliveryBoyDataWithDistances($orderId, $deliveryboyCollection)
    {
        $orderLocation = $this->orderLocationF->create()
            ->getCollection()->addFieldToFilter('order_id', $orderId)
            ->getFirstItem();
        
        $deliveryLocation = [
            'latitude' => $orderLocation->getLatitude(),
            'longitude' => $orderLocation->getLongitude(),
        ];
        
        $sortedDelBoy = [];
        foreach ($deliveryboyCollection as $deliveryboy) {
            $delBoyCoords = [
                'latitude' => $deliveryboy->getLatitude(),
                'longitude' => $deliveryboy->getLongitude(),
            ];
            $distance = $this->getDistanceBetTwoPoints($delBoyCoords, $deliveryLocation);
            $sortedDelBoy[] = [
                'distance' => $distance,
                'deliveryboy' => $deliveryboy,
            ];
        }
        usort($sortedDelBoy, function ($a, $b) {
            if ($a['distance'] < $b['distance']) {
                return -1;
            }
            if ($a['distance'] > $b['distance']) {
                return 1;
            }
            if ($a['distance'] == $b['distance']) {
                return 0;
            }
        });
        return $sortedDelBoy;
    }

    public function getAddressCoordinates($address)
    {
        try {
            $renderer = $this->addressConfig->getFormatByCode('html')->getRenderer();
            $addrStr = strip_tags($renderer->renderArray($address));
            $prepAddr = str_replace(' ', '+', $addrStr);
            $googleMapApiKey = $this->deliveryboyHelper->getGoogleMapKey();
            $geocode = $this->fileDriver->fileGetContents(
                'https://maps.google.com/maps/api/geocode/json?key=' . $googleMapApiKey .
                '&address=' . $prepAddr . '&sensor=false'
            );
            $output = $this->jsonHelper->jsonDecode($geocode);
            if (isset($output->results[0])) {
                $resultData['latitude'] = $output->results[0]->geometry->location->lat;
                $resultData['longitude'] = $output->results[0]->geometry->location->lng;
            } else {
                $resultData = null;
                $resultData['latitude'] = "28.6568196";
                $resultData['longitude'] = "77.4182632";
                $output = $this->jsonHelper->jsonEncode($output);
                $this->logger->debug($output);
            }
        } catch (\Throwable $e) {
            $this->logger->debug(__CLASS__);
            $this->logger->debug($e->getMessage());
            $resultData = null;
        }

        return $resultData;
    }

    public function getDistanceUnit()
    {
        return 'km';
    }

    public function formatDeliveryboyName($deliveryboyName, $distance, $distanceUnit = null)
    {
        $formattedDistance = $this->formatDistance($distance, $distanceUnit);
        $formattedName = $deliveryboyName . ' (' . $formattedDistance . ')';
        return $formattedName;
    }

    public function formatDistance($distance, $distanceUnit = null)
    {
        $distanceUnit = $distanceUnit ?: $this->getDistanceUnit();
        $distance = $this->formatDistanceWithoutUnit($distance);
        $distance = $distance . ' ' . $distanceUnit;
        return $distance;
    }

    public function formatDistanceWithoutUnit($distance)
    {
        return number_format($distance, 2, '.', '');
    }
}
