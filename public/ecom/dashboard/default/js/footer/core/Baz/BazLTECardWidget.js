/* globals exports define BazLTELayout */
/*!
* AdminLTE v3.0.0-rc.6 (https://adminlte.io)
* Copyright 2014-2019 Colorlib <http://colorlib.com>
* Licensed under MIT (https://github.com/ColorlibHQ/AdminLTE/blob/master/LICENSE)
*/
(function (global, factory) {
    'use strict';
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {
    'use strict';
    /**
    * --------------------------------------------
    * AdminLTE CardWidget.js
    * License MIT
    * --------------------------------------------
    */
    var CardWidget = function ($) {

        var NAME                    = 'CardWidget';
        var DATA_KEY                = 'lte.cardwidget';
        var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        var Event = {
            EXPANDED                : "expanded" + EVENT_KEY,
            COLLAPSED               : "collapsed" + EVENT_KEY,
            MAXIMIZED               : "maximized" + EVENT_KEY,
            MINIMIZED               : "minimized" + EVENT_KEY,
            REMOVED                 : "removed" + EVENT_KEY,
            LOADED                  : "loaded" + EVENT_KEY,
            OVERLAY_ADDED           : "overlay.added" + EVENT_KEY,
            OVERLAY_REMOVED         : "overlay.removed" + EVENT_KEY
        };
        var ClassName = {
            CARD                    : 'card',
            COLLAPSED               : 'collapsed-card',
            WAS_COLLAPSED           : 'was-collapsed',
            MAXIMIZED               : 'maximized-card'
        };
        var Selector = {
            DATA_REMOVE             : '[data-card-widget="remove"]',
            DATA_COLLAPSE           : '[data-card-widget="collapse"]',
            DATA_MAXIMIZE           : '[data-card-widget="maximize"]',
            DATA_REFRESH            : '[data-card-widget="refresh"]',
            CARD                    : "." + ClassName.CARD,
            CARD_HEADER             : '.card-header',
            CARD_BODY               : '.card-body',
            CARD_FOOTER             : '.card-footer',
            COLLAPSED               : "." + ClassName.COLLAPSED
        };
        var Default = {
            collapseTrigger         : Selector.DATA_COLLAPSE,
            removeTrigger           : Selector.DATA_REMOVE,
            maximizeTrigger         : Selector.DATA_MAXIMIZE,
            refreshTrigger          : Selector.DATA_REFRESH,
            collapseIcon            : 'fa-minus',
            expandIcon              : 'fa-plus',
            maximizeIcon            : 'fa-expand',
            minimizeIcon            : 'fa-compress',
            source                  : '',
            sourceSelector          : '',
            params                  : {},
            method                  : 'get',
            dataType                : 'html',
            content                 : '.card-body',
            loadInContent           : true,
            overlayTemplate         : '<div class="overlay"><i class="fas fa-2x fa-sync-alt fa-spin"></i></div>',
            onLoadStart             : function onLoadStart() {},
            onLoadDone              : function onLoadDone(response) {
                                            return response;
                                        }
        };

        var CardWidget = function () {
            function CardWidget(element, settings) {
                this._element = element;
                this._parent = element.parents(Selector.CARD).first();

                if (element.hasClass(ClassName.CARD)) {
                    this._parent = element;
                }
                this._settings = $.extend({}, Default, settings);
            }

            var _proto = CardWidget.prototype;

            _proto.collapse = function collapse() {
                this._parent.addClass(ClassName.COLLAPSED);
                this._parent.children(Selector.CARD_BODY + ", " + Selector.CARD_FOOTER).slideUp();

                this._parent.find(this._settings.collapseTrigger + ' .' + this._settings.collapseIcon).addClass(this._settings.expandIcon).removeClass(this._settings.collapseIcon);

                var collapsed = $.Event(Event.COLLAPSED);

                this._element.trigger(collapsed, this._parent);
            };

            _proto.expand = function expand() {
                this._parent.removeClass(ClassName.COLLAPSED);

                this._parent.children(Selector.CARD_BODY + ", " + Selector.CARD_FOOTER).slideDown();
                this._parent.find(this._settings.collapseTrigger + ' .' + this._settings.expandIcon).addClass(this._settings.collapseIcon).removeClass(this._settings.expandIcon);

                var expanded = $.Event(Event.EXPANDED);

                this._element.trigger(expanded, this._parent);
            };

            _proto.remove = function remove() {
                this._parent.slideUp();

                var removed = $.Event(Event.REMOVED);

                this._element.trigger(removed, this._parent);
            };

            _proto.toggle = function toggle() {
                if (this._parent.hasClass(ClassName.COLLAPSED)) {
                    this.expand();
                    return;
                }

                this.collapse();
            };

            _proto.maximize = function maximize() {
                this._parent.find(this._settings.maximizeTrigger + ' .' + this._settings.maximizeIcon).addClass(this._settings.minimizeIcon).removeClass(this._settings.maximizeIcon);

                this._parent.css({
                    'height': this._parent.height(),
                    'width': this._parent.width(),
                    'transition': 'all .15s'
                }).delay(150).queue(function () {
                    $(this).addClass(ClassName.MAXIMIZED);
                    $('html').addClass(ClassName.MAXIMIZED);

                    if ($(this).hasClass(ClassName.COLLAPSED)) {
                        $(this).addClass(ClassName.WAS_COLLAPSED);
                    }

                    $(this).dequeue();
                });

                this._parent.find('.card-body').addClass('overflow-auto');

                var maximized = $.Event(Event.MAXIMIZED);

                this._element.trigger(maximized, this._parent);
            };

            _proto.minimize = function minimize() {
                this._parent.find(this._settings.maximizeTrigger + ' .' + this._settings.minimizeIcon).addClass(this._settings.maximizeIcon).removeClass(this._settings.minimizeIcon);

                this._parent.css('cssText', 'height:' + this._parent[0].style.height + ' !important;' + 'width:' + this._parent[0].style.width + ' !important; transition: all .15s;').delay(10).queue(function () {
                    $(this).removeClass(ClassName.MAXIMIZED);
                    $('html').removeClass(ClassName.MAXIMIZED);
                    $(this).css({
                        'height': 'inherit',
                        'width': 'inherit'
                    });

                    if ($(this).hasClass(ClassName.WAS_COLLAPSED)) {
                        $(this).removeClass(ClassName.WAS_COLLAPSED);
                    }

                    $(this).dequeue();
                });

                this._parent.find('.card-body').removeClass('overflow-auto');

                var MINIMIZED = $.Event(Event.MINIMIZED);

                this._element.trigger(MINIMIZED, this._parent);
            };

            _proto.toggleMaximize = function toggleMaximize() {
                if (this._parent.hasClass(ClassName.MAXIMIZED)) {
                    this.minimize();
                    return;
                }

                this.maximize();
            };

            _proto.load = function load() {
                this._addOverlay();

                this._settings.onLoadStart.call($(this));

                var that = this;

                $.ajax({
                    url         : this._settings.source,
                    data        : this._settings.params,
                    method      : this._settings.method,
                    dataType    : this._settings.responseType,
                    success     : function(response) {
                                    if (that._settings.loadInContent) {
                                        if (that._settings.sourceSelector != '') {
                                            response = $(response).find(that._settings.sourceSelector).html();
                                        }
                                        that._element.find(that._settings.content).empty().html(response);
                                    }
                    },
                    error       : function(response) {
                                    if (that._settings.loadInContent) {
                                        if (that._settings.sourceSelector != '') {
                                            response = $(response).find(that._settings.sourceSelector).html();
                                        }
                                        that._element.find(that._settings.content).html(response);
                                    }
                    }
                }).done(function(response) {
                    that._settings.onLoadDone.call($(that), response);
                    that._removeOverlay();
                    var loadedEvent = $.Event(Event.LOADED);
                    $(that._element).trigger(loadedEvent);
                });
            };

            _proto._addOverlay = function _addOverlay() {
                this._parent.append(this._overlay);

                var overlayAddedEvent = $.Event(Event.OVERLAY_ADDED);
                $(this._element).trigger(overlayAddedEvent);
            };

            _proto._removeOverlay = function _removeOverlay() {
                this._parent.find(this._overlay).remove();

                var overlayRemovedEvent = $.Event(Event.OVERLAY_REMOVED);
                $(this._element).trigger(overlayRemovedEvent);
            };

            _proto._init = function _init(config) {
                if (config === 'toggle') {
                    this.toggle();
                } else if (config === 'toggleMaximize') {
                    this.toggleMaximize();
                } else if (config === 'remove') {
                    this.remove();
                } else if (config === 'load') {
                    if ($(this._element).find('[data-card-widget="refresh"]').data('params')) {
                        this._settings.params = JSON.parse(JSON.parse($(this._element).find('[data-card-widget="refresh"]').data('params')));
                    }
                    if ($(this._element).find('[data-card-widget="refresh"]').data('datatype')) {
                        this._settings.dataType = $(this._element).find('[data-card-widget="refresh"]').data('datatype');
                    }
                    if ($(this._element).find('[data-card-widget="refresh"]').data('method')) {
                        this._settings.method = $(this._element).find('[data-card-widget="refresh"]').data('method');
                    }
                    if ($(this._element).find('[data-card-widget="refresh"]').data('sourceselector')) {
                        this._settings.sourceSelector = $(this._element).find('[data-card-widget="refresh"]').data('sourceselector');
                    }
                    this._overlay = $(this._settings.overlayTemplate);
                    if ($(this._element).find('[data-card-widget="refresh"]').length > 0) {
                        if ($(this._element).find('[data-card-widget="refresh"]').data('source')) {
                            this._settings.source = $(this._element).find('[data-card-widget="refresh"]').data('source');
                            this.load();
                        } else {
                            throw new Error('Source url was not defined. Please specify a url in your CardWidget source option.');
                        }
                    }
                }
            };

            CardWidget._jQueryInterface = function _jQueryInterface(config) {
                var data = $(this).data(DATA_KEY);

                if (!data) {
                    data = new CardWidget($(this), data);
                    $(this).data(DATA_KEY, typeof config === 'string' ? data : config);
                    data._init(config);
                } else {
                    data._init(config);
                }
            };

        return CardWidget;

        }();

        $(document).on('click', Selector.DATA_COLLAPSE, function (event) {
            if (event) {
                event.preventDefault();
            }
            CardWidget._jQueryInterface.call($(this).parents('.card'), 'toggle');
        });
        $(document).on('click', Selector.DATA_REMOVE, function (event) {
            if (event) {
                event.preventDefault();
            }
            CardWidget._jQueryInterface.call($(this).parents('.card'), 'remove');
        });
        $(document).on('click', Selector.DATA_MAXIMIZE, function (event) {
            if (event) {
                event.preventDefault();
            }
            CardWidget._jQueryInterface.call($(this).parents('.card'), 'toggleMaximize');
        });
        $(document).on('click', Selector.DATA_REFRESH, function (event) {
            if (event) {
                event.preventDefault();
            }
            CardWidget._jQueryInterface.call($(this).parents('.card'), 'load');
        });
        $(document).on('libsLoadComplete bazContentLoaderAjaxComplete', function() {
            $('body').find(Selector.DATA_REFRESH).each(function() {
                CardWidget._jQueryInterface.call($(this).parents('.card'), 'load');
            });
        });
        $(document).on('maximized.lte.cardwidget', function() {
            BazLTELayout.fixHeight({fixHeightTask : 'noFixHeight'});
        });
        $(document).on('collapsed.lte.cardwidget expanded.lte.cardwidget minimized.lte.cardwidget', function() {
            BazLTELayout.fixHeight({fixHeightTask : 'fixHeight'});
        });

        $.fn[NAME] = CardWidget._jQueryInterface;
        $.fn[NAME].Constructor = CardWidget;

        $.fn[NAME].noConflict = function () {
            $.fn[NAME] = JQUERY_NO_CONFLICT;
            return CardWidget._jQueryInterface;
        };

        return CardWidget;
}(jQuery);

exports.CardWidget = CardWidget;

Object.defineProperty(exports, '__esModule', { value: true });

}));