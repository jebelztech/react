<?php
declare(strict_types=1);

namespace Amasty\CheckoutGraphQl\Plugin\QuoteGraphQl\Model\Cart\SetBillingAddressOnCart;

use Amasty\CheckoutGraphQl\Model\Utils\Address\FillEmptyData;
use Amasty\CheckoutGraphQl\Model\Utils\Address\Validator;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\QuoteGraphQl\Model\Cart\SetBillingAddressOnCart;

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
        SetBillingAddressOnCart $subject,
        ContextInterface $context,
        CartInterface $cart,
        array $billingAddressInput
    ): array {
        $billingAddress = &$billingAddressInput['address'];
        if (isset($billingAddress['custom_attributes'])) {
            $customAttributes = $billingAddress['custom_attributes'];
            foreach ($customAttributes as $attribute) {
                $billingAddress[$attribute['attribute_code']] = $attribute['value'];
            }
        }

        $this->addressValidator->validate($billingAddress);
        $billingAddress = $this->fillEmptyData->execute($billingAddress);

        return [$context, $cart, $billingAddressInput];
    }
}
