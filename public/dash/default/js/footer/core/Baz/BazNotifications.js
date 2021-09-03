/* exported BazNotifications */
/* globals BazHelpers */
/*
* @title                    : BazNotifications
* @description              : Baz Notifications Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazNotifications._function_(_options_);
* @functions                : BazNotifications
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazNotifications = function() {
    var BazNotifications = void 0;
    var dataCollection;
    var initialized = false;
    var pullNotifications = false;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        dataCollection = window.dataCollection;

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        if (!initialized) {
            init(false);
        } else {
            getNotificationsCount();
        }

        //eslint-disable-next-line
        console.log('Notification service online');
        initPullNotifications(false);
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }

        //eslint-disable-next-line
        console.log('Notification service offline');
        initPullNotifications(true);
    }

    //Notifications
    function getNotificationsCount() {
        var url = dataCollection.env.rootPath + dataCollection.env.appRoute + '/system/notifications/fetchNewNotificationsCount';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }

            if (response.responseCode == 0 && response.responseData) {
                if (!Number.isInteger(response.responseData.count)) {
                    parseInt(response.responseData.count);
                }

                if (response.responseData.count === window.dataCollection.env.notifications.count) {
                    return;
                } else if (response.responseData.count < window.dataCollection.env.notifications.count) {
                    window.dataCollection.env.notifications.count = response.responseData.count;
                    updateCounter(response);
                    return;
                }

                window.dataCollection.env.notifications.count = response.responseData.count;
                updateCounter(response);

                if (response.responseData.count > 0 && !response.responseData.mute) {
                    window.dataCollection.env.sounds.notificationSound.play();
                }
            }

        }, 'json');
    }

    function updateCounter(response) {
        var notificationCount = 0;

        if (response.responseData.count.error > 0) {
            notificationCount = response.responseData.count.error;
            $('#notifications-button-counter').removeClass('badge-info badge-warning').addClass('badge-danger');
        } else if (response.responseData.count.warning > 0) {
            $('#notifications-button-counter').removeClass('badge-info badge-danger').addClass('badge-warning');
            notificationCount = response.responseData.count.warning;
        } else {
            $('#notifications-button-counter').removeClass('badge-warning badge-danger').addClass('badge-info');
            notificationCount = response.responseData.count.total;
        }

        if (notificationCount === 0) {
            $('#notifications-button-counter').html('');
        } else if (notificationCount < 10) {
            $('#notifications-button-counter').css({'right': '10px'});
            $('#notifications-button-counter').html(notificationCount);
            shakeNotificationsButton();
        } else if (notificationCount < 99) {
            $('#notifications-button-counter').css({'right': '5px'});
            $('#notifications-button-counter').html(notificationCount);
            shakeNotificationsButton();
        } else if (notificationCount > 99) {
            $('#notifications-button-counter').css({'right': 0});
            $('#notifications-button-counter').html('99+');
            shakeNotificationsButton();
        }
    }

    function initPullNotifications(offline) {
        // $(document).ready(function() {
            // getNotificationsCount();
        // });
        if (offline) {
            BazHelpers.interval(
                async(iteration, stop) => {
                    //eslint-disable-next-line
                    console.log(BazNotifications.getPullNotifications());
                    if (!BazNotifications.getPullNotifications()) {
                        stop();
                    } else {
                        BazNotifications.getNotificationsCount();
                    }
                },
                100000
            );

        }

        pullNotifications = offline;
    }

    function getPullNotifications() {
        return pullNotifications;
    }

    function shakeNotificationsButton() {
        $('#notifications-button').addClass('animated tada');

        setTimeout(function() {
            $('#notifications-button').removeClass('animated tada');
        }, 10000);
    }

    function onMessage() {
        getNotificationsCount();

        if ($('#baz-content section').length > 0) {
            var section = $('#baz-content section')[0].id.match(/notifications-listing/g);
            if (section && section.length === 1) {
                var component = $('#baz-content .component')[0].id;
                window["dataCollection"][component][component + '-listing']['BazContentSectionWithListing']._filterRunAjax();
            }
        }
    }

    function bazNotificationsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazNotificationsConstructor) {
        BazNotifications = BazNotificationsConstructor;
        BazNotifications.defaults = { };
        BazNotifications.init = function(options) {
            init(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.serviceOnline = function(options) {
            serviceOnline(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.serviceOffline = function(options) {
            serviceOffline(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.initPullNotifications = function(options) {
            initPullNotifications(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.onMessage = function(options) {
            onMessage(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.getNotificationsCount = function(options) {
            getNotificationsCount(_extends(BazNotifications.defaults, options));
        }
        BazNotifications.getPullNotifications = function(options) {
            return getPullNotifications(_extends(BazNotifications.defaults, options));
        }
    }

    setup(bazNotificationsConstructor);

    return bazNotificationsConstructor;
}();