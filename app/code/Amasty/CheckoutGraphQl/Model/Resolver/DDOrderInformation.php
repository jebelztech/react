<?php
declare(strict_types=1);

namespace Amasty\CheckoutGraphQl\Model\Resolver;

use Amasty\CheckoutDeliveryDate\Model\DeliveryDateProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Module\Manager;
use Magento\Sales\Api\Data\OrderInterface;

class DDOrderInformation implements ResolverInterface
{
    const DD_MODULE = 'Amasty_CheckoutDeliveryDate';

    const DATE_KEY = 'date';
    const TIME_KEY = 'time';
    const COMMENT_KEY = 'comment';

    /**
     * @var OrderInterface
     */
    private $orderModel;

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(OrderInterface $orderModel, Manager $moduleManager)
    {
        $this->orderModel = $orderModel;
        $this->moduleManager = $moduleManager;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return array
     * @throws GraphQlInputException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        $emptyData = [
            self::DATE_KEY => '',
            self::TIME_KEY => '',
            self::COMMENT_KEY => ''
        ];

        if (!isset($value['order'])) {
            throw new GraphQlInputException(__('"order" value must be specified'));
        }

        if (!$this->moduleManager->isEnabled(self::DD_MODULE)) {
            return $emptyData;
        }

        try {
            $order = $this->orderModel->loadByIncrementId($value['order']['order_number']);
            $ddProvider = ObjectManager::getInstance()->get(DeliveryDateProvider::class);
            $delivery = $ddProvider->findByOrderId((int)$order->getEntityId());
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        return [
            self::DATE_KEY => $delivery->getDate() ?? '',
            self::TIME_KEY =>  $delivery->getTime() ?? '',
            self::COMMENT_KEY =>  $delivery->getComment() ?? ''
        ];
    }
}
