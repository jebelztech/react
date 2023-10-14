<?php
declare(strict_types=1);

namespace Amasty\CheckoutGraphQl\Plugin\QuoteGraphQl\Model\Cart\SetShippingAddressesOnCart;

use Amasty\CheckoutGraphQl\Model\Utils\Address\FillEmptyData;
use Amasty\CheckoutGraphQl\Model\Utils\Address\Validator;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\QuoteGraphQl\Model\Cart\SetShippingAddressesOnCart;

class SetCustomAttributes
{
    /**
     * @var FillEmptyData
     */
    private $fillEmptyData;

    /**
     * @var Validator
     */
    private $addressValidator;

    public function __construct(FillEmptyData $fillEmptyData, Validator $addressValidator)
    {
        $this->fillEmptyData = $fillEmptyData;
        $this->addressValidator = $addressValidator;
    }

    public function beforeExecute(
        SetShippingAddressesOnCart $subject,
        ContextInterface $context,
        CartInterface $cart,
        array $shippingAddressesInput
    ): array {
        $shippingAddress = &$shippingAddressesInput[0]['address'];
        if (isset($shippingAddress['custom_attributes'])) {
            $customAttributes = $shippingAddress['custom_attributes'];
            foreach ($customAttributes as $attribute) {
                $shippingAddress[$attribute['attribute_code']] = $attribute['value'];
            }
        }

        $this->addressValidator->validate($shippingAddress);
        $shippingAddress = $this->fillEmptyData->execute($shippingAddress);

        return [$context, $cart, $shippingAddressesInput];
    }
}
