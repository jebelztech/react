/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

define(
    [
        'jquery',
        'underscore',
    ],
    function ($,_) {
        'use strict';

        var mixin = _.extend({
            defaults       : {
                globalEnabled: window.GlobalEnabled,
                levelScope   : window.LevelScope,
                attributesArr : window.attributesArr
            },
            setInitialValue: function () {
                this._super();
                var self = this;

                if (typeof this.globalEnabled !== 'undefined') {
                    if (!this.globalEnabled && ( this.scopeLabel == '[GLOBAL]' || this.parentScope == 'data.product.stock_data')) {
                        this.disabled(true);
                        if (this.code == 'gift_message_available') {
                            this.visible(false);
                        }
                    }
                    if (!this.globalEnabled && this.index == 'use_config_gift_message_available') {
                        this.visible(false);
                    }
                    if (this.levelScope == 1 && this.scopeLabel == '[WEBSITE]') {
                        this.disabled(true);
                    }
                }

                if (typeof this.attributesArr !== 'undefined') {
                    $.each(this.attributesArr, function(key, value) {
                        if (value.code == self.code) {
                            if (value.allow == 0) {
                                self.disabled(true);
                            } else if (value.allow == 2) {
                                if ($('label[for="' + self.uid + '"]')) {
                                    $('label[for="' + self.uid + '"]').hide();
                                }

                                self.visible(false);
                            }
                        }
                    });
                }


                return this;
            },
        });
        return function (target) {
            return target.extend(mixin);
        };
    }
);
