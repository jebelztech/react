<?php
declare(strict_types=1);

namespace Amasty\GoogleAddressAutocomplete\Model;

use Amasty\Base\Model\ConfigProviderAbstract;

class ConfigProvider extends ConfigProviderAbstract
{
    const PATH_PREFIX = 'amasty_address_autocomplete/';

    const IS_ENABLED = 'general/google_address_suggestion';
    const API_KEY = 'general/google_api_key';

    protected $pathPrefix = self::PATH_PREFIX;

    /**
     * @return bool
     */
    public function isAddressSuggestionEnabled(): bool
    {
        return $this->isSetFlag(self::IS_ENABLED);
    }

    /**
     * @return string|null
     */
    public function getGoogleMapsKey(): ?string
    {
        return $this->getValue(self::API_KEY);
    }
}
