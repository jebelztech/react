<?php
declare(strict_types=1);

namespace Amasty\CheckoutGraphQl\Model\Resolver;

use Amasty\CheckoutDeliveryDate\Model\DeliveryDateProvider;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Framework\Module\Manager;

class DDCartInformation implements ResolverInterface
{
    const DD_MODULE = 'Amasty_CheckoutDeliveryDate';

    const DATE_KEY = 'date';
    const TIME_KEY = 'time';
    const COMMENT_KEY = 'comment';

    /**
     * @var Manager
     */
    private $moduleManager;

    public function __construct(Manager $moduleManager)
    {
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

        if (!isset($value['model'])) {
            throw new GraphQlInputException(__('"model" value must be specified'));
        }

        if (!$this->moduleManager->isEnabled(self::DD_MODULE)) {
            return $emptyData;
        }

        $cart = $value['model'];

        try {
            $ddProvider = ObjectManager::getInstance()->get(DeliveryDateProvider::class);
            $delivery = $ddProvider->findByQuoteId((int)$cart->getId());
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
