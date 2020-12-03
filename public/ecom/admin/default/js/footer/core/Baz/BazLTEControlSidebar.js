/* exported BazLTEControlSidebar */
/* globals */
/* 
* @title                    : BazLTEControlSidebar
* @description              : Baz LTE Control Sidebar Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazLTEControlSidebar._function_(_options_);
* @functions                : 
* @options                  : 
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazLTEControlSidebar = function() {
    'use strict';
    var BazLTEControlSidebar = void 0;
    var Event = {
        COLLAPSED                   : "collapsed.baz.lte.controlsidebar",
        EXPANDED                    : "expanded.baz.lte.controlsidebar"
    };
    var Selector = {
        CONTROL_SIDEBAR             : '.control-sidebar',
        CONTROL_SIDEBAR_CONTENT     : '.control-sidebar-content',
        DATA_TOGGLE                 : '[data-widget="control-sidebar"]',
        CONTENT                     : '.content-wrapper',
        HEADER                      : '.main-header',
        FOOTER                      : '.main-footer'
    };
    var ClassName = {
        CONTROL_SIDEBAR_ANIMATE     : 'control-sidebar-animate',
        CONTROL_SIDEBAR_OPEN        : 'control-sidebar-open',
        CONTROL_SIDEBAR_SLIDE       : 'control-sidebar-slide-open',
        LAYOUT_FIXED                : 'layout-fixed',
        NAVBAR_FIXED                : 'layout-navbar-fixed',
        NAVBAR_SM_FIXED             : 'layout-sm-navbar-fixed',
        NAVBAR_MD_FIXED             : 'layout-md-navbar-fixed',
        NAVBAR_LG_FIXED             : 'layout-lg-navbar-fixed',
        NAVBAR_XL_FIXED             : 'layout-xl-navbar-fixed',
        FOOTER_FIXED                : 'layout-footer-fixed',
        FOOTER_SM_FIXED             : 'layout-sm-footer-fixed',
        FOOTER_MD_FIXED             : 'layout-md-footer-fixed',
        FOOTER_LG_FIXED             : 'layout-lg-footer-fixed',
        FOOTER_XL_FIXED             : 'layout-xl-footer-fixed'
    };
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }
    
    //Example Function
    function init(options) {
        fixHeight(options);
        fixScrollHeight();

        $(window).resize(function () {
            fixHeight(options);

            fixScrollHeight();
        });
        $(window).scroll(function () {
            if ($('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE)) {
                fixScrollHeight();
            }
        });
        $(document).on('click', Selector.DATA_TOGGLE, function (e) {
            e.preventDefault();
            toggle(options);
        });        
    }

    //Show Control Sidebar
    function show(options) {
        if (options.controlsidebarSlide) {
            $('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
            $('body').removeClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
                $(Selector.CONTROL_SIDEBAR).hide();
                $('html').removeClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
                $(this).dequeue();
            });
        } else {
            $('body').removeClass(ClassName.CONTROL_SIDEBAR_OPEN);
        }

        var expandedEvent = $.Event(Event.EXPANDED);
        $(Selector.DATA_TOGGLE).trigger(expandedEvent);        
    }

    //Collapse Control Sidebar
    function collapse(options) {
        if (options.controlsidebarSlide) {
            $('html').addClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
            $(Selector.CONTROL_SIDEBAR).show().delay(10).queue(function () {
                $('body').addClass(ClassName.CONTROL_SIDEBAR_SLIDE).delay(300).queue(function () {
                    $('html').removeClass(ClassName.CONTROL_SIDEBAR_ANIMATE);
                    $(this).dequeue();
                });
                $(this).dequeue();
            });
        } else {
            $('body').addClass(ClassName.CONTROL_SIDEBAR_OPEN);
        }

        var collapsedEvent = $.Event(Event.COLLAPSED);
        $(Selector.DATA_TOGGLE).trigger(collapsedEvent);
    }

    //Toggle Control Sidebar
    function toggle(options) {
        var shouldOpen = $('body').hasClass(ClassName.CONTROL_SIDEBAR_OPEN) || $('body').hasClass(ClassName.CONTROL_SIDEBAR_SLIDE);

        if (shouldOpen) {
            show(options);
        } else {
            collapse(options);
        }
    }

    function fixScrollHeight() {
        var heights = {
            scroll: $(document).height(),
            window: $(window).height(),
            header: $(Selector.HEADER).outerHeight(),
            footer: $(Selector.FOOTER).outerHeight()
        };
        var positions = {
            bottom: Math.abs(heights.window + $(window).scrollTop() - heights.scroll),
            top: $(window).scrollTop()
        };
        var navbarFixed = false;
        var footerFixed = false;

        if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
            if ($('body').hasClass(ClassName.NAVBAR_FIXED) || $('body').hasClass(ClassName.NAVBAR_SM_FIXED) || $('body').hasClass(ClassName.NAVBAR_MD_FIXED) || $('body').hasClass(ClassName.NAVBAR_LG_FIXED) || $('body').hasClass(ClassName.NAVBAR_XL_FIXED)) {
                if ($(Selector.HEADER).css("position") === "fixed") {
                    navbarFixed = true;
                }
            }

            if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName.FOOTER_MD_FIXED) || $('body').hasClass(ClassName.FOOTER_LG_FIXED) || $('body').hasClass(ClassName.FOOTER_XL_FIXED)) {
                if ($(Selector.FOOTER).css("position") === "fixed") {
                    footerFixed = true;
                }
            }

            if (positions.top === 0 && positions.bottom === 0) {
                $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
                $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
                $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.header + heights.footer));
            } else if (positions.bottom <= heights.footer) {
                if (footerFixed === false) {
                    $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer - positions.bottom);
                    $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.footer - positions.bottom));
                } else {
                    $(Selector.CONTROL_SIDEBAR).css('bottom', heights.footer);
                }
            } else if (positions.top <= heights.header) {
                if (navbarFixed === false) {
                    $(Selector.CONTROL_SIDEBAR).css('top', heights.header - positions.top);
                    $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window - (heights.header - positions.top));
                } else {
                    $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
                }
            } else {
                if (navbarFixed === false) {
                    $(Selector.CONTROL_SIDEBAR).css('top', 0);
                    $(Selector.CONTROL_SIDEBAR + ', ' + Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', heights.window);
                } else {
                    $(Selector.CONTROL_SIDEBAR).css('top', heights.header);
                }
            }
        }
    }

    function fixHeight(options) {
        var heights = {
            window: $(window).height(),
            header: $(Selector.HEADER).outerHeight(),
            footer: $(Selector.FOOTER).outerHeight()
        };

        if ($('body').hasClass(ClassName.LAYOUT_FIXED)) {
            var sidebarHeight = heights.window - heights.header;

            if ($('body').hasClass(ClassName.FOOTER_FIXED) || $('body').hasClass(ClassName.FOOTER_SM_FIXED) || $('body').hasClass(ClassName.FOOTER_MD_FIXED) || $('body').hasClass(ClassName.FOOTER_LG_FIXED) || $('body').hasClass(ClassName.FOOTER_XL_FIXED)) {
                if ($(Selector.FOOTER).css("position") === "fixed") {
                    sidebarHeight = heights.window - heights.header - heights.footer;
                }
            }

            $(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).css('height', sidebarHeight);

            if (typeof $.fn.overlayScrollbars !== 'undefined') {
                $(Selector.CONTROL_SIDEBAR + ' ' + Selector.CONTROL_SIDEBAR_CONTENT).overlayScrollbars({
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

    function BazLTEControlSidebarConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazLTEControlSidebarConstructor) {
        BazLTEControlSidebar = BazLTEControlSidebarConstructor;
        BazLTEControlSidebar.defaults = {
        };
        BazLTEControlSidebar.init = function(options) {
            init(_extends(BazLTEControlSidebar.defaults, options));
        }               
    }

    setup(BazLTEControlSidebarConstructor);

    return BazLTEControlSidebarConstructor;
}();