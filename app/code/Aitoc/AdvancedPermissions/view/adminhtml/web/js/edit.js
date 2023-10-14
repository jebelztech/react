/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
define(['jquery', 'jquery/ui'], function ($) {
    'use strict';
    return function (optionConfig) {
        $(function () {
            if (optionConfig.classes.length > 0) {
                $.each(optionConfig.classes, function (i,val) {
                    $(val).prop(optionConfig.param[i], optionConfig.param[i]);
                });
            }
        });
    }
});