/* exported BazLTEPushMenu */
/* globals */
/* 
* @title                    : BazLTEPushMenu
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazLTEPushMenu._function_(_options_);
* @functions                : 
* @options                  : 
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazLTEPushMenu = function() {
    'use strict';
    var BazLTEPushMenu = void 0;
    var Event = {
        COLLAPSED                   : "collapsed.baz.lte.pushmenu",
        SHOWN                       : "shown.baz.lte.pushmenu"
    };
    var Selector = {
        TOGGLE_BUTTON               : '[data-widget="pushmenu"]',
        SIDEBAR_MINI                : '.sidebar-mini',
        SIDEBAR_COLLAPSED           : '.sidebar-collapse',
        BODY                        : 'body',
        OVERLAY                     : '#sidebar-overlay',
        WRAPPER                     : '.wrapper'
    };
    var ClassName = {
        SIDEBAR_OPEN                : 'sidebar-open',
        COLLAPSED                   : 'sidebar-collapse',
        OPEN                        : 'sidebar-open'
    };

    function show(options) {
        if (options.autoCollapseSize) {
            if ($(window).width() <= options.autoCollapseSize) {
                $(Selector.BODY).addClass(ClassName.OPEN);
            }
        }

        $(Selector.BODY).removeClass(ClassName.COLLAPSED);

        if (options.enableRemember) {
            localStorage.setItem("remember.baz.lte.pushmenu", ClassName.OPEN);
        }

        var shownEvent = $.Event(Event.SHOWN);
        $(Selector.TOGGLE_BUTTON).trigger(shownEvent);
    }

    function collapse(options) {
        if (options.autoCollapseSize) {
            if ($(window).width() <= options.autoCollapseSize) {
                //eslint-disable-next-line
                console.log('iran');
                $(Selector.BODY).removeClass(ClassName.OPEN);
            }
        }

        $(Selector.BODY).addClass(ClassName.COLLAPSED);

        if (options.enableRemember) {
            localStorage.setItem("remember.baz.lte.pushmenu", ClassName.COLLAPSED);
        }

        var collapsedEvent = $.Event(Event.COLLAPSED);
        $(Selector.TOGGLE_BUTTON).trigger(collapsedEvent);
    }

    function toggle(options) {
        if (!$(Selector.BODY).hasClass(ClassName.COLLAPSED)) {
            collapse(options);
        } else {
            show(options);
        }
    }

    function autoCollapse(resize = false, options) {
        if (options.autoCollapseSize) {
            if ($(window).width() <= options.autoCollapseSize) {
                if (!$(Selector.BODY).hasClass(ClassName.OPEN)) {
                    collapse(options);
                }
            } else if (resize == true) {
                if (!$(Selector.BODY).hasClass(ClassName.OPEN)) {
                    show(options);
                } else {
                    $(Selector.BODY).removeClass(ClassName.OPEN);
                }
            }
        }
    }

    function remember(options) {
        if (options.enableRemember) {
            var toggleState = localStorage.getItem("remember.baz.lte.pushmenu");

            if (toggleState == ClassName.COLLAPSED) {
                if (options.noTransitionAfterReload) {
                    $("body").addClass('hold-transition').addClass(ClassName.COLLAPSED).delay(50).queue(function () {
                        $(this).removeClass('hold-transition');
                        $(this).dequeue();
                    });
                } else {
                    $("body").addClass(ClassName.COLLAPSED);
                }
            } else {
                if (options.noTransitionAfterReload) {
                    $("body").addClass('hold-transition').removeClass(ClassName.COLLAPSED).delay(50).queue(function () {
                        $(this).removeClass('hold-transition');
                        $(this).dequeue();
                    });
                } else {
                    $("body").removeClass(ClassName.COLLAPSED);
                }
            }
        }
    }

    function init(options) {
        if (!$(Selector.OVERLAY).length) {
            addOverlay(options);
        }
        remember(options);
        autoCollapse(false, options);
        $(window).resize(function () {
            autoCollapse(true, options);
        });
        $(document).on('click', Selector.TOGGLE_BUTTON, function (event) {
            event.preventDefault();
            var button = event.currentTarget;

            if ($(button).data('widget') !== 'pushmenu') {
                button = $(button).closest(Selector.TOGGLE_BUTTON);
            }

            toggle(options);
        });
    }

    function addOverlay(options) {
        var overlay = $('<div />', {
            id: 'sidebar-overlay'
        });
        overlay.on('click', function () {
            collapse(options);
        });
        $(Selector.WRAPPER).append(overlay);
    }

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }


    function bazLTEPushMenuConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazLTEPushMenuConstructor) {
        BazLTEPushMenu = BazLTEPushMenuConstructor;
        BazLTEPushMenu.defaults = {
            autoCollapseSize: 992,
            enableRemember: true,
            noTransitionAfterReload: true
        };
        BazLTEPushMenu.init = function(options) {
            init(_extends(BazLTEPushMenu.defaults, options));
        }      
    }

    setup(bazLTEPushMenuConstructor);

    return bazLTEPushMenuConstructor;
}();