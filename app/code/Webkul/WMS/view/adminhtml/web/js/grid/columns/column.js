/**
 * Webkul Software.
 * 
 * PHP version 7.0+
 *
 * @category  Webkul
 * @package   Webkul_WMS
 * @author    Webkul <support@webkul.com>
 * @copyright Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html ASL Licence
 * @link      https://store.webkul.com/license.html
 */
define([
    'underscore',
    'uiRegistry',
    'mageUtils',
    'uiElement'
    ], function (_, registry, utils, Element) {
        'use strict';
        return Element.extend({
                defaults: {
                    headerTmpl: 'ui/grid/columns/text',
                    bodyTmpl: 'ui/grid/cells/text',
                    disableAction: false,
                    controlVisibility: true,
                    sortable: true,
                    sorting: false,
                    visible: true,
                    draggable: true,
                    fieldClass: {},
                    ignoreTmpls: {
                        fieldAction: true
                    },
                    statefull: {
                        visible: true,
                        sorting: true
                    },
                    imports: {
                        exportSorting: 'sorting'
                    },
                    listens: {
                        '${ $.provider }:params.sorting.field': 'onSortChange'
                    },
                    modules: {
                        source: '${ $.provider }'
                    }
                },

                initialize: function () {
                    this._super().initFieldClass();
                    return this;
                },

                initObservable: function () {
                    this._super()
                        .track(['visible', 'sorting', 'disableAction'])
                        .observe(['dragging']);
                    return this;
                },

                initFieldClass: function () {
                    _.extend(this.fieldClass, {_dragging: this.dragging});
                    return this;
                },

                applyState: function (state, property) {
                    var namespace = this.storageConfig.root;
                    if (property) {
                        namespace += '.' + property;
                    }
                    this.storage('applyStateOf', state, namespace);
                    return this;
                },

                sort: function (enable) {
                    if (!this.sortable) {
                        return this;
                    }
                    enable !== false ? this.toggleSorting() : this.sorting = false;
                    return this;
                },

                sortDescending: function () {
                    if (this.sortable) {
                        this.sorting = 'desc';
                    }
                    return this;
                },

                sortAscending: function () {
                    if (this.sortable) {
                        this.sorting = 'asc';
                    }
                    return this;
                },

                toggleSorting: function () {
                    this.sorting === 'asc' ?
                    this.sortDescending() :
                    this.sortAscending();
                    return this;
                },

                isSorted: function () {
                    return !!this.sorting;
                },

                exportSorting: function () {
                    if (!this.sorting) {
                        return;
                    }
                    this.source('set', 'params.sorting', {
                        field: this.index,
                        direction: this.sorting
                    });
                },

                hasFieldAction: function () {
                    return !!this.fieldAction;
                },

                applyFieldAction: function (rowIndex) {
                    var action = this.fieldAction,
                    callback;
                    if (!this.hasFieldAction() || this.disableAction) {
                        return this;
                    }
                    action = utils.template(action, {
                            column: this,
                            rowIndex: rowIndex
                        }, true);
                    callback = this._getFieldCallback(action);
                    if (_.isFunction(callback)) {
                        callback();
                    }
                    return this;
                },

                getFieldHandler: function (record) {
                    if (this.hasFieldAction()) {
                        return this.applyFieldAction.bind(this, record._rowIndex);
                    }
                },

                _getFieldCallback: function (action) {
                    var args     = action.params || [],
                    callback = action.target;
                    if (action.provider && action.target) {
                        args.unshift(action.target);
                        callback = registry.async(action.provider);
                    }
                    if (!_.isFunction(callback)) {
                        return false;
                    }
                    return function () {
                        callback.apply(callback, args);
                    };
                },

                getLabel: function (record) {
                    return record[this.index];
                },

                getFieldClass: function () {
                    return this.fieldClass;
                },

                getHeader: function () {
                    return this.headerTmpl;
                },

                getBody: function () {
                    return this.bodyTmpl;
                },

                onSortChange: function (field) {
                    if (field !== this.index) {
                        this.sort(false);
                    }
                }
        });
});
