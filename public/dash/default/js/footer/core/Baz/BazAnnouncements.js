/* exported BazAnnouncements */
/* globals */
/*
* @title                    : BazAnnouncements
* @description              : Baz Announcements Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazAnnouncements._function_(_options_);
* @functions                : BazAnnouncements
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazAnnouncements = function() {
    var BazAnnouncements = void 0;
    var dataCollection;
    var initialized = false;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        dataCollection = window.dataCollection;
        dataCollection.env.messenger = { };

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        if (!initialized) {
            init(false);
        }
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }

        initPullAnnouncements();
    }

    function initPullAnnouncements() {
        // $(document).ready(function() {
            // getAnnouncementsCount();
        // });

        // BazHelpers.interval(
        //     async(iteration, stop) => {

        //         if (stoppedPullAnnouncements) {
        //             stop();
        //         }

        //         BazTunnels.getAnnouncementsCount();
        //     },
        //     10000
        // );
    }

    function shakeBell() {
        $('#notifications-button').addClass('animated tada');

        setTimeout(function() {
            $('#notifications-button').removeClass('animated tada');
        }, 10000);
    }

    function onMessage(data) {
        //eslint-disable-next-line
        console.log(data);

        shakeBell();//This will be popup
    }

    function bazAnnouncementsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazAnnouncementsConstructor) {
        BazAnnouncements = BazAnnouncementsConstructor;
        BazAnnouncements.defaults = { };
        BazAnnouncements.init = function(options) {
            init(_extends(BazAnnouncements.defaults, options));
        }
        BazAnnouncements.serviceOnline = function(options) {
            serviceOnline(_extends(BazAnnouncements.defaults, options));
        }
        BazAnnouncements.serviceOffline = function(options) {
            serviceOffline(_extends(BazAnnouncements.defaults, options));
        }
        BazAnnouncements.onMessage = function(options) {
            onMessage(_extends(BazAnnouncements.defaults, options));
        }
    }

    setup(bazAnnouncementsConstructor);

    return bazAnnouncementsConstructor;
}();