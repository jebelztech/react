<?php

declare(strict_types=1);

namespace Amasty\Mostviewed\Plugin\SalesRule\Model;

use Amasty\Mostviewed\Model\Cart\AddProductsByIds;
use Amasty\Mostviewed\Model\ConfigProvider;
use Amasty\Mostviewed\Model\OptionSource\DiscountType;
use Amasty\Mostviewed\Model\Pack\Cart\Discount\GetPacksForCartItem;
use Amasty\Mostviewed\Model\Pack\Discount\CalculatorInterface;
use Amasty\Mostviewed\Model\Pack\Finder\Result\SimplePack;
use Magento\Quote\Model\Quote\Item\AbstractItem;

class RulesApplier
{
    /**
     * @var AbstractItem
     */
    private $item;

    /**
     * @var array
     */
    protected $itemData;

    /**
     * @var \Magento\SalesRule\Model\Validator
     */
    private $validator;

    /**
     * @var \Amasty\Mostviewed\Api\PackRepositoryInterface
     */
    protected $packRepository;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var GetPacksForCartItem
     */
    private $getPacksForCartItem;

    /**
     * @var CalculatorInterface[]
     */
    private $calculators;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        \Amasty\Mostviewed\Api\PackRepositoryInterface $packRepository,
        \Magento\SalesRule\Model\Validator $validator,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Checkout\Model\Session $checkoutSession,
        GetPacksForCartItem $getPacksForCartItem,
        ConfigProvider $configProvider,
        array $calculators = []
    ) {
        $this->packRepository = $packRepository;
        $this->validator = $validator;
        $this->priceCurrency = $priceCurrency;
        $this->checkoutSession = $checkoutSession;
        $this->getPacksForCartItem = $getPacksForCartItem;
        $this->calculators = $calculators;
        $this->configProvider = $configProvider;
        $this->_construct();
    }

    protected function _construct(): void
    {
        $this->clearAppliedPackIds();
    }

    /**
     * @param \Magento\SalesRule\Model\RulesApplier $subject
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\ResourceModel\Rule\Collection $rules
     * @param bool $skipValidation
     * @param mixed $couponCode
     *
     * @return array
     */
    public function beforeApplyRules($subject, $item, $rules, $skipValidation, $couponCode)
    {
        $this->setItem($item);
        $this->itemData = [
            'itemPrice' => $this->validator->getItemPrice($item),
            'baseItemPrice' => $this->validator->getItemBasePrice($item),
            'itemOriginalPrice' => $this->validator->getItemOriginalPrice($item),
            'baseOriginalPrice' => $this->validator->getItemBaseOriginalPrice($item)
        ];

        return [$item, $rules, $skipValidation, $couponCode];
    }

    public function afterApplyRules(\Magento\SalesRule\Model\RulesApplier $subject, array $appliedRuleIds): array
    {
        if ($this->isNotApplicableForItem()) {
            return $appliedRuleIds;
        }

        $this->clearItemDiscount();

        $appliedPacks = $this->getPacksForCartItem->execute($this->getItem());
        foreach ($appliedPacks as $appliedPack) {
            if ($this->isPackCanBeApplied($appliedPack)) {
                $this->applyPackRule($appliedPack);
                $this->saveAppliedPackId($appliedPack->getComplexPack()->getPackId());
            }
        }

        if ($appliedPacks) {
            $bundlePackDiscountApplied = $this->updateItemDiscountWithPackDiscount();
            if ($bundlePackDiscountApplied) {
                $appliedRuleIds = [];
            }
        }

        return $appliedRuleIds;
    }

    private function isNotApplicableForItem(): bool
    {
        return !$this->configProvider->isProductsCanBeAddedSeparately()
            && $this->getItem()->getOptionByCode(AddProductsByIds::BUNDLE_PACK_OPTION_CODE) === null;
    }

    private function isPackCanBeApplied(SimplePack $simplePack): bool
    {
        $pack = $this->packRepository->getById($simplePack->getComplexPack()->getPackId(), true);

        $childIds = explode(',', $pack->getProductIds());
        $productId = (int) $this->getItem()->getProduct()->getId();

        return in_array($productId, $childIds)
            || (
                in_array($productId, $pack->getParentIds())
                && ($pack->getApplyForParent() || $pack->getDiscountType() === DiscountType::CONDITIONAL)
            );
    }

    private function applyPackRule(SimplePack $simplePack): void
    {
        $pack = $this->packRepository->getById($simplePack->getComplexPack()->getPackId(), true);
        [$amountPerPack, $baseAmountPerPack] = $this->calculators[$pack->getDiscountType()]->execute(
            $this->getItem(),
            $simplePack
        );
        $qty = $simplePack->getItemQty((int) $this->getItem()->getAmBundleItemId());

        $amountPerPack = min($amountPerPack, $qty * $this->itemData['itemPrice']);
        $baseAmountPerPack = min($baseAmountPerPack, $qty * $this->itemData['baseItemPrice']);

        $amount = $amountPerPack + $this->getItem()->getAmDiscountAmount() ?: 0;
        $baseAmount = $baseAmountPerPack + $this->getItem()->getAmBaseDiscountAmount() ?: 0;

        if ($baseAmount) {
            $this->getItem()->setAmDiscountAmount($amount);
            $this->getItem()->setAmBaseDiscountAmount($baseAmount);
            $discounts = $this->getItem()->getAmDiscounts() ?: [];
            $discounts[$simplePack->getId()] = [
                'amount' => $amountPerPack,
                'base_amount' => $baseAmountPerPack
            ];
            $this->getItem()->setAmDiscounts($discounts);
        }
    }

    public function setItem(AbstractItem $item): void
    {
        $this->item = $item;
    }

    public function getItem(): AbstractItem
    {
        return $this->item;
    }

    private function saveAppliedPackId(int $packId): void
    {
        $bundlePackIds = $this->checkoutSession->getAppliedPackIds() ?: [];
        if (!in_array($packId, $bundlePackIds)) {
            $bundlePackIds[] = $packId;
        }
        $this->checkoutSession->setAppliedPackIds($bundlePackIds);
    }

    private function clearAppliedPackIds()
    {
        $this->checkoutSession->setAppliedPackIds([]);
    }

    public function setItemData(array $itemData): void
    {
        $this->itemData = $itemData;
    }

    private function clearItemDiscount(): void
    {
        $this->getItem()->setAmDiscountAmount(0);
        $this->getItem()->setAmBaseDiscountAmount(0);
    }

    /**
     * Return true if bundle pack discount applied instead of magento cart price rules.
     *
     * @return bool
     */
    private function updateItemDiscountWithPackDiscount(): bool
    {
        $amDiscountAmount = $this->getItem()->getAmDiscountAmount();
        $amBaseDiscountAmount = $this->getItem()->getAmBaseDiscountAmount();
        if ($this->configProvider->isApplyCartRule() && $this->getItem()->getDiscountAmount() > $amDiscountAmount) {
            $bundlePackDiscountApplied = false;
        } else {
            $this->getItem()->setDiscountAmount($amDiscountAmount);
            $this->getItem()->setBaseDiscountAmount($amBaseDiscountAmount);
            $bundlePackDiscountApplied = true;
        }

        return $bundlePackDiscountApplied;
    }
}
