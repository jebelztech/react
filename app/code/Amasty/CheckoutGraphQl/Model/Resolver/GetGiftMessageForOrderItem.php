<?php

namespace Amasty\CheckoutGraphQl\Model\Resolver;

use Amasty\CheckoutGraphQl\Model\Utils\CartProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GiftMessage\Api\ItemRepositoryInterface;
use Magento\GiftMessage\Api\Data\MessageInterface;

class GetGiftMessageForOrderItem implements ResolverInterface
{
    const ITEM_IDS = 'itemIds';

    /**
     * @var CartProvider
     */
    private $cartProvider;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    public function __construct(CartProvider $cartProvider, ItemRepositoryInterface $itemRepository)
    {
        $this->cartProvider = $cartProvider;
        $this->itemRepository = $itemRepository;
    }

    /**
     * @param Field $field
     * @param ContextInterface $context
     * @param ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @return Value|MessageInterface|mixed
     * @throws GraphQlInputException
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args[CartProvider::CART_ID])) {
            throw new GraphQlInputException(__('Required parameter "%1" is missing', CartProvider::CART_ID));
        }

        if (empty($args[self::ITEM_IDS])) {
            throw new GraphQlInputException(__('Required parameter "%1" is missing', self::ITEM_IDS));
        }

        $messages = [];
        $cart = $this->cartProvider->getCartForUser($args[CartProvider::CART_ID], $context);

        foreach ($args[self::ITEM_IDS] as $itemId) {
            try {
                $messages[] = $this->itemRepository->get($cart->getId(), $itemId);
            } catch (LocalizedException $e) {
                unset($e);
                continue;
            }
        }

        return $messages;
    }
}
