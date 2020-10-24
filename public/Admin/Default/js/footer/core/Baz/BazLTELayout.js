/* exported BazLTELayout */
/* globals screenfull MobileDetect */
/* 
* @title                    : BazLTELayout
* @description              : Baz LTE Layout Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazLTELayout._function_(_options_);
* @functions                : 
* @options                  : 
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazLTELayout = function() {
    'use strict';
    var BazLTELayout = void 0;
    var checkIsMobile, md;
    var heights = { };
    var Selector = {
        HEADER                      : '.main-header',
        MAIN_SIDEBAR                : '.main-sidebar',
        SIDEBAR                     : '.main-sidebar .sidebar',
        TOGGLE_BUTTON               : '[data-widget="pushmenu"]',
        DATA_WIDGET                 : '[data-widget="treeview"]',
        CONTENT                     : '.content-wrapper',
        BRAND                       : '.brand-link',
        CONTENT_HEADER              : '.content-header',
        WRAPPER                     : '.wrapper',
        CONTROL_SIDEBAR             : '.control-sidebar',
        LAYOUT_FIXED                : '.layout-fixed',
        FOOTER                      : '.main-footer'
    };
    var ClassName = {
        HOLD                        : 'hold-transition',
        SIDEBAR                     : 'main-sidebar',
        CONTENT_FIXED               : 'content-fixed',
        SIDEBAR_FOCUSED             : 'sidebar-focused',
        LAYOUT_FIXED                : 'layout-fixed',
        NAVBAR_FIXED                : 'layout-navbar-fixed',
        FOOTER_FIXED                : 'layout-footer-fixed'
    };

    // Check if device is mobile
    function isMobile() {
        md = new MobileDetect(window.navigator.userAgent);
        if (md.mobile()) {
            return true;
        }
    }

    // Calculate Present Heights
    function calculateHeight() {
        var layoutHeader, contentHeader, sectionsAlert, layoutFooter, cardHeight, totalCardHeader, 
            noOfCards, noOfCollapsedCards, totalOpenCards, totalCardFooter;
        layoutHeader = $('.main-header').outerHeight();
        contentHeader = $('.content-header').outerHeight() + 16;
        layoutFooter = ($('.main-footer').outerHeight() + 1);
        if ($('.alert').length > 0) {
            sectionsAlert = $('.content .alert').outerHeight();
            heights.content = $(window).height() - (layoutHeader + contentHeader + layoutFooter + sectionsAlert + 10);
        } else {
            heights.content = $(window).height() - (layoutHeader + contentHeader + layoutFooter);
        }
        // heights.sectionsHeader = $('#' + componentId + '-header').height() + 10;
        noOfCards = 0;
        noOfCollapsedCards = 0;
        totalCardHeader = 0;
        totalCardFooter = 0;

        $('.card').each(function() {
            if (!$(this).parents().hasClass('card-body')) {
                noOfCards = noOfCards + 1;
                if ($(this).children().hasClass('card-header')) {
                    totalCardHeader = totalCardHeader + $(this).children('.card-header').outerHeight();
                }
                if ($(this).children().hasClass('widget-user-header')) {
                    totalCardHeader = totalCardHeader + $(this).children('.widget-user-header').outerHeight();
                }
                if ($(this).children().hasClass('card-footer')) {
                    totalCardFooter = totalCardFooter + $(this).children('.card-footer').outerHeight();
                }
                if ($(this).hasClass('collapsed-card')) {
                    noOfCollapsedCards = noOfCollapsedCards + 1;
                }
            }
        });
        totalOpenCards = noOfCards - noOfCollapsedCards;
        heights.cards = totalCardHeader + totalCardFooter;

        if (totalOpenCards > 0) {
            cardHeight = (heights.content - heights.cards) / totalOpenCards;
        } else {
            cardHeight = heights.content - heights.cards;
        }

        heights = { };
        return cardHeight;
    }

    //Fix height to disable scrolling
    //NOTE: in _cards.scss we have .card { @extend .mb-2; instead of mb-3
    function fixHeight(options) {
        if ($(window).height() > 600) {
            if (options.fixHeightTask === "fixHeight") {
                $('body, html').css({
                    'height'    : 'auto',
                    'overflow'  : 'hidden'
                });
                var cardHeight = calculateHeight();
                $('.card-body').each(function() {
                    if (!$(this).parents().hasClass('card-body')) {
                        if (!$(this).parent('.collapsed-card').length > 0 ) {
                            $(this).css({
                                "max-height" : cardHeight,
                                "min-height" : cardHeight
                            });
                            $(this).addClass('overflow-auto');
                        }
                    }
                });
                var contentWrapper = document.querySelectorAll('.content-wrapper');
                contentWrapper[0].style.setProperty('margin-bottom', 0, 'important');
            } else if (options.fixHeightTask === "noFixHeight") {
                $('.card-body').css({
                        "max-height": '',
                        "min-height": ''
                });
            }
        } else {
            $('body, html').css({
                'height'    : 'auto',
                'overflow'  : 'scroll'
            });
            $('.card-body').css({
                    "max-height": '',
                    "min-height": ''
            });
        }
        $('body').trigger('heightFixed');
    }

    function init(options) {
        checkIsMobile = isMobile();
        if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
            if (!checkIsMobile) {
                options.fixHeightTask = 'fixHeight';
                fixHeight(options);
                // Change pushmenu redo fix height
                $(Selector.TOGGLE_BUTTON).on('collapsed.baz.lte.pushmenu shown.baz.lte.pushmenu', function () {
                    fixHeight(options);
                });
                //On Menu Expand/Collapse
                $(Selector.DATA_WIDGET).on('collapsed.baz.lte.treeview expanded.baz.lte.treeview', function () {
                    fixHeight(options);
                });                     
                // Change window Size redo fix height
                $(window).resize(function() {
                    fixHeight(options);
                });
                //OnAjaxComplete fix height of card-body
                $(document).on('bazContentLoaderAjaxComplete', function() {
                    fixHeight(options);                    
                });
                // Fullscreen redo fix height
                $(function () {
                    screenfull.onchange(function () {
                        if ($('.collapsed-card').length !== 0) {
                            fixHeight(options);
                        }
                    });
                });
                // Sidebar scrollbar
                if (typeof $.fn.overlayScrollbars !== 'undefined') {
                    $(Selector.SIDEBAR).overlayScrollbars({
                        className: options.scrollbarTheme,
                        sizeAutoCapable: true,
                        scrollbars: {
                            autoHide: options.scrollbarAutoHide,
                            clickScrolling: true
                        }
                    });
                }
            }
        }

        // $(Selector.SIDEBAR + ' a').on('focusin', function () {
        //     //eslint-disable-next-line
        //     console.log('ifocus');
        //     $(Selector.MAIN_SIDEBAR).addClass(ClassName.SIDEBAR_FOCUSED);
        // });      

        // $(Selector.SIDEBAR + ' a').on('focusout', function () {
        //     //eslint-disable-next-line
        //     console.log('no no ifocus');
        //     $(Selector.MAIN_SIDEBAR).removeClass(ClassName.SIDEBAR_FOCUSED);
        // });        
    }

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    function bazLTELayoutConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazLTELayoutConstructor) {
        BazLTELayout = BazLTELayoutConstructor;
        BazLTELayout.defaults = {
            fixHeightTask       : null,
            scrollbarTheme      : 'os-theme-light',
            scrollbarAutoHide   : 'l'
        };
        BazLTELayout.init = function(options) {
            init(_extends(BazLTELayout.defaults, options));
        };
        BazLTELayout.fixHeight = function(options) {
            fixHeight(_extends(BazLTELayout.defaults, options));
        }
    }

    setup(bazLTELayoutConstructor);

    return bazLTELayoutConstructor;
}();