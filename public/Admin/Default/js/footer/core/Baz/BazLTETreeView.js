/* exported BazLTETreeView */
/* globals */
/*
* @title                    : BazLTETreeView
* @description              : Baz LTE Tree View Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazLTETreeView._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazLTETreeView = function() {
    'use strict';
    var BazLTETreeView = void 0;
    var Event = {
        SELECTED                    : "selected.baz.lte.treeview",
        EXPANDED                    : "expanded.baz.lte.treeview",
        COLLAPSED                   : "collapsed.baz.lte.treeview",
        LOAD_DATA_API               : "load.baz.lte.treeview"
    };
    var Selector = {
        LI                          : '.nav-item',
        LINK                        : '.nav-link',
        TREEVIEW_MENU               : '.nav-treeview',
        OPEN                        : '.menu-open',
        DATA_WIDGET                 : '[data-widget="treeview"]'
    };
    var ClassName = {
        LI                          : 'nav-item',
        LINK                        : 'nav-link',
        TREEVIEW_MENU               : 'nav-treeview',
        OPEN                        : 'menu-open'
    };

    function init(options) {
        $(document).on('click', options.trigger, function (event) {
            toggle(event, options);
        });
    }

    function expand(treeviewMenu, parentLi, options) {
        var expandedEvent = $.Event(Event.EXPANDED);

        if (options.accordion) {
            var openMenuLi = parentLi.siblings(Selector.OPEN).first();
            var openTreeview = openMenuLi.find(Selector.TREEVIEW_MENU).first();
            collapse(openTreeview, openMenuLi, options);
        }

        treeviewMenu.stop().slideDown(options.animationSpeed, function () {
            parentLi.addClass(ClassName.OPEN);
            $(Selector.DATA_WIDGET).trigger(expandedEvent);
        });
    }

    function collapse(treeviewMenu, parentLi, options) {
        var collapsedEvent = $.Event(Event.COLLAPSED);
        treeviewMenu.stop().slideUp(options.animationSpeed, function () {
            parentLi.removeClass(ClassName.OPEN);
            $(Selector.DATA_WIDGET).trigger(collapsedEvent);
            treeviewMenu.find(Selector.OPEN + " > " + Selector.TREEVIEW_MENU).slideUp();
            treeviewMenu.find(Selector.OPEN).removeClass(ClassName.OPEN);
        });
    }

    function toggle(event, options) {
        var $relativeTarget = $(event.currentTarget);
        var $parent = $relativeTarget.parent();
        var treeviewMenu = $parent.find('> ' + Selector.TREEVIEW_MENU);

        if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
            if (!$parent.is(Selector.LI)) {
                treeviewMenu = $parent.parent().find('> ' + Selector.TREEVIEW_MENU);
            }

            if (!treeviewMenu.is(Selector.TREEVIEW_MENU)) {
                return;
            }
        }

        event.preventDefault();
        var parentLi = $relativeTarget.parents(Selector.LI).first();
        var isOpen = parentLi.hasClass(ClassName.OPEN);

        if (isOpen) {
            collapse($(treeviewMenu), parentLi, options);
        } else {
            expand($(treeviewMenu), parentLi, options);
        }
    }

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }


    function bazLTETreeExampleConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazLTETreeViewConstructor) {
        BazLTETreeView = BazLTETreeViewConstructor;
        BazLTETreeView.defaults = {
            trigger: Selector.DATA_WIDGET + " " + Selector.LINK,
            animationSpeed: 300,
            accordion: true
        };
        BazLTETreeView.init = function(options) {
            init(_extends(BazLTETreeView.defaults, options));
        }
    }

    setup(bazLTETreeExampleConstructor);

    return bazLTETreeExampleConstructor;
}();