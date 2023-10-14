/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

(function (root, factory) {
    'use strict';

    if (typeof define === 'function' && define.amd) {
        define([
            'jquery',
            'jquery/ui',
            'jquery/jstree/jquery.jstree',
            'mage/backend/suggest',
            'mage/backend/tree-suggest'
        ], factory);
    } else {
        factory(root.jQuery);
    }
}(this, function ($) {
    'use strict';
    
    $.widget('mage.aitapTreeSuggest', $.mage.treeSuggest, {
        
        _renderDropdown: function (e, items, context) {
            if ('root_ids' in this.options && 'show_id' in this.options) {
                for (var key in items) {
                    if (this.options.root_ids.indexOf(items[key].id) > -1 &&
                        this.options.show_id != items[key].id
                    ) {
                        items.splice(key, 1);
                    }
                }
            }
            this._superApply([e, items, context]);
        }
        
    });
   
    return $.mage.aitapTreeSuggest;
}));
