/* exported BazLTETodoList */
/* globals */
/* 
* @title                    : BazLTETodoList
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazLTETodoList._function_(_options_);
* @functions                : 
* @options                  : 
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazLTETodoList = function() {
    'use strict';
    var BazLTETodoList = void 0;

    var Selector = {
        DATA_TOGGLE                 : '[data-widget="todo-list"]'
    };
    var ClassName = {
        TODO_LIST_DONE              : 'done'
    };

    function init(options) {
        $(Selector.DATA_TOGGLE).find('input:checkbox:checked').parents('li').toggleClass(ClassName.TODO_LIST_DONE);
        $(Selector.DATA_TOGGLE).on('change', 'input:checkbox', function (event) {
          toggle($(event.target), options);
        });
    }
    function toggle(item, options) {
        item.parents('li').toggleClass(ClassName.TODO_LIST_DONE);

        if (!$(item).prop('checked')) {
            unCheck($(item), options);
            return;
        }

        check(item);
    }

    function check(item, options) {
        options.onCheck.call(item);
    }

    function unCheck(item, options) {
        options.onUnCheck.call(item);
    }

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    function bazLTEToDoListConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazLTETodoListConstructor) {
        BazLTETodoList = BazLTETodoListConstructor;
        BazLTETodoList.defaults = {
            onCheck: function onCheck(item) {
                return item;
            },
            onUnCheck: function onUnCheck(item) {
                return item;
            }           
        };
        BazLTETodoList.init = function(options) {
            init(_extends(BazLTETodoList.defaults, options));
        }               
    }

    setup(bazLTEToDoListConstructor);

    return bazLTEToDoListConstructor;
}();