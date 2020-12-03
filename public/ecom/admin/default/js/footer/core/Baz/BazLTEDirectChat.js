/* exported BazLTEDirectChat */
/* globals */
/* 
* @title                    : BazLTEDirectChat
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazLTEDirectChat._function_(_options_);
* @functions                : 
* @options                  : 
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazLTEDirectChat = function() {
    'use strict';
    var BazLTEDirectChat = void 0;
    var Event = {
        TOGGLED                 : "toggled.baz.lte.directchat"
    };
    var Selector = {
        DATA_TOGGLE             : '[data-widget="chat-pane-toggle"]',
        DIRECT_CHAT             : '.direct-chat'
    };
    var ClassName = {
        DIRECT_CHAT_OPEN        : 'direct-chat-contacts-open'
    };

    function init() {    
        $(document).on('click', Selector.DATA_TOGGLE, function (event) {
            if (event) event.preventDefault();

            toggle();
        });
    }

    function toggle() {
        $(this._element).parents(Selector.DIRECT_CHAT).first().toggleClass(ClassName.DIRECT_CHAT_OPEN);
        var toggledEvent = $.Event(Event.TOGGLED);
        $(this._element).trigger(toggledEvent);
    }

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }


    function bazLTEDirectChatConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazLTEDirectChatConstructor) {
        BazLTEDirectChat = BazLTEDirectChatConstructor;
        BazLTEDirectChat.defaults = {
        };
        BazLTEDirectChat.init = function(options) {
            init(_extends(BazLTEDirectChat.defaults, options));
        }               
    }

    setup(bazLTEDirectChatConstructor);

    return bazLTEDirectChatConstructor;
}();