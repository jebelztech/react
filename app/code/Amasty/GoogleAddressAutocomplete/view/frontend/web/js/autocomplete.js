/* global google */
/**
 * Google Autocomplete initializer
 */
define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiRegistry',
    'ko',
    'underscore'
], function ($, registry, ko, _) {
    'use strict';

    return {
        isReady: ko.observable(false),
        options: {},

        /**
         * @param {Object} autocomplete - Google Autocomplete object
         * @returns {void}
         */
        geolocate: function (autocomplete) {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var geolocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        },
                        circle = new google.maps.Circle({
                            center: geolocation,
                            radius: position.coords.accuracy
                        });

                    autocomplete.setBounds(circle.getBounds());
                });
            }
        },

        /**
         * @param {UIComponent} component
         * @returns {null|void}
         */
        registerField: function (component) {
            var self = this;

            if (this.isReady()) {
                return this.init(component);
            }

            this.isReady.subscribe(function (isReady) {
                if (isReady) {
                    return self.init(component);
                }

                return null;
            });

            return null;
        },

        /**
         * @param {UIComponent} component
         * @returns {void}
         */
        init: function (component) {
            var self = this;

            registry.get(component, function (rootComponent) {
                registry.get(component + '.street.0', function (inputComponent) {
                    $.async({
                        selector: '#' + inputComponent.uid
                    }, function (input) {
                        var autocomplete = new google.maps.places.Autocomplete(
                            input,
                            { types: [ 'geocode' ] }
                        );

                        autocomplete.setFields(['address_components', 'name']);

                        // eslint-disable-next-line max-nested-callbacks
                        autocomplete.addListener('place_changed', function () {
                            self.fillInAddress(autocomplete, rootComponent);
                        });

                        self.geolocate(autocomplete);
                    });
                });
            });
        },

        /**
         * @param {Object} autocomplete - Google Autocomplete object
         * @param {UIComponent} rootComponent
         * @returns {void}
         */
        fillInAddress: function (autocomplete, rootComponent) {
            var self = this,
                place = autocomplete.getPlace(),
                streetComponent,
                street,
                isRegionApplied = false,
                postcode = false,
                postcodeSuffix = false,
                stateSelect,
                country,
                value,
                addressType,
                stateInput;

            if (!place.address_components) {
                return;
            }

            streetComponent = rootComponent.getChild('street').getChild(0);
            street = place.name.replace(',', '');

            if (street && (streetComponent.value() === street)) {
                streetComponent.value.valueHasMutated();
            } else {
                streetComponent.value(street);
            }

            if (rootComponent.hasChild('postcode')) {
                rootComponent.getChild('postcode').value('');
            }

            if (rootComponent.hasChild('region_id_input')) {
                rootComponent.getChild('region_id_input').value('');
            }

            if (rootComponent.hasChild('city')) {
                rootComponent.getChild('city').value('');
            }

            _.each(place.address_components, function (addressComponent) {
                addressType = addressComponent.types[0];

                switch (addressType) {
                    case 'country':
                        if (rootComponent.hasChild('country_id')) {
                            rootComponent.getChild('country_id').value(addressComponent.short_name);
                        }

                        break;
                    case 'locality':
                    case 'postal_town':
                        if (rootComponent.hasChild('city')) {
                            rootComponent.getChild('city').value(addressComponent.long_name);
                        }

                        break;
                    case 'postal_code':
                        if (rootComponent.hasChild('postcode')) {
                            postcode = addressComponent.long_name;

                            if (postcodeSuffix) {
                                postcode = postcode + '-' + postcodeSuffix;
                            }

                            rootComponent.getChild('postcode').value(postcode);
                        }

                        break;
                    case 'postal_code_suffix':
                        postcodeSuffix = addressComponent.long_name;

                        break;
                    case 'administrative_area_level_1':
                        if (isRegionApplied) {
                            break;
                        }

                        stateSelect = rootComponent.getChild('region_id');

                        if (stateSelect && stateSelect.visible()) {
                            value = addressComponent.short_name;
                            country = window.checkoutConfig.defaultCountryId;

                            if (rootComponent.hasChild('country_id')) {
                                country = rootComponent.getChild('country_id').value();
                            }

                            if (country in self.options.regions && value in self.options.regions[country]) {
                                stateSelect.value(self.options.regions[country][value]);
                            }
                        } else if (rootComponent.hasChild('region_id_input')) {
                            rootComponent.getChild('region_id_input').value(addressComponent.long_name);
                        }

                        isRegionApplied = true;

                        break;
                    case 'administrative_area_level_2':
                        if (isRegionApplied) {
                            stateInput = rootComponent.getChild('region_id_input');

                            if (stateInput && stateInput.visible() && stateInput.value()) {
                                stateInput.value(stateInput.value() + ', ' + addressComponent.long_name);
                            }
                        } else {
                            stateSelect = rootComponent.getChild('region_id');

                            if (stateSelect && stateSelect.visible()) {
                                value = addressComponent.short_name;
                                country = window.checkoutConfig.defaultCountryId;

                                if (rootComponent.hasChild('country_id')) {
                                    country = rootComponent.getChild('country_id').value();
                                }

                                if (country in self.options.regions && value in self.options.regions[country]) {
                                    stateSelect.value(self.options.regions[country][value]);
                                }
                            }
                        }

                        break;
                    default: break;
                }
            });
        }
    };
});
