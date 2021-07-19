/* exported BazTunnels */
/* globals BazMessenger ab */
/*
* @title                    : BazTunnels
* @description              : Baz Tunnels Lib for wstunnels
* @developer                : guru@bazaari.com.au
* @usage                    : BazTunnels._function_(_options_);
* @functions                : BazTunnelsInit
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazTunnels = function() {
    var BazTunnels = void 0;
    var dataCollection;

    var reconnectMessengerTunnel = null;

    // var stoppedPullNotifications = false;
    // var stopPullNotifications = () => {stoppedPullNotifications = true};

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init() {
        dataCollection = window.dataCollection;
        dataCollection.env.wsTunnels = { };

        initWsTunnels();
    }

    // function initPullNotifications() {
    //     // $(document).ready(function() {
    //         // getNotificationsCount();
    //     // });

    //     BazHelpers.interval(
    //         async(iteration, stop) => {

    //             if (stoppedPullNotifications) {
    //                 stop();
    //             }

    //             BazTunnels.getNotificationsCount();
    //         },
    //         10000
    //     );
    // }

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
                    updateCounter();
                    return;
                }

                window.dataCollection.env.notifications.count = response.responseData.count;
                updateCounter();

                if (response.responseData.count > 0 && !response.responseData.mute) {
                    window.dataCollection.env.sounds.notificationSound.play();
                }
            }

            function updateCounter() {
                if (response.responseData.count === 0) {
                    $('#notifications-button-counter').html('');
                } else if (response.responseData.count < 10) {
                    $('#notifications-button-counter').css({'right': '10px'});
                    $('#notifications-button-counter').html(response.responseData.count);
                    shakeBell();
                } else if (response.responseData.count < 99) {
                    $('#notifications-button-counter').css({'right': '5px'});
                    $('#notifications-button-counter').html(response.responseData.count);
                    shakeBell();
                } else if (response.responseData.count > 99) {
                    $('#notifications-button-counter').css({'right': 0});
                    $('#notifications-button-counter').html('99+');
                    shakeBell();
                }
            }

            function shakeBell() {
                $('#notifications-button').addClass('animated tada');

                setTimeout(function() {
                    $('#notifications-button').removeClass('animated tada');
                }, 10000);
            }
        }, 'json');
    }

    function initWsTunnels() {
        initMessengerTunnel();
        initWANMPTunnel();
    }

    function initMessengerTunnel() {
        dataCollection.env.wsTunnels.messenger = { };
        dataCollection.env.wsTunnels.messenger = new WebSocket('ws://' + dataCollection.env.httpHost + '/messenger/');
        initMessengerListers();
    }

    function initMessengerListers() {
        dataCollection.env.wsTunnels.messenger.onopen = null;
        dataCollection.env.wsTunnels.messenger.onopen = function(e) {
            if (reconnectMessengerTunnel) {
                clearInterval(reconnectMessengerTunnel);
                reconnectMessengerTunnel = null;
                BazMessenger.serviceOnline();
            } else {
                BazMessenger.init();
            }

            // if (!stoppedPullNotifications) {
            //     stopPullNotifications();
            // }

            //eslint-disable-next-line
            console.log(e);
        };

        dataCollection.env.wsTunnels.messenger.onclose = null;
        dataCollection.env.wsTunnels.messenger.onclose = function(e) {
                //eslint-disable-next-line
            console.log(e);
            if (!reconnectMessengerTunnel) {
                BazMessenger.serviceOffline();
                reconnectMessengerTunnel = setInterval(() => {
                    initMessengerTunnel();
                }, 10000);
            }
        };

        dataCollection.env.wsTunnels.messenger.onerror = null;
        dataCollection.env.wsTunnels.messenger.onerror = function(e) {
            //eslint-disable-next-line
            console.log(e);
            dataCollection.env.wsTunnels.messenger.close();
        };
    }

    function initWANMPTunnel() {
        window.ab.debug(true, true);
        dataCollection.env.wsTunnels.notifications = { };
        dataCollection.env.wsTunnels.notifications =
            new ab.Session('ws://' + dataCollection.env.httpHost + '/notifications/',
                function() {
                    dataCollection.env.wsTunnels.notifications.subscribe('systemNotifications', function(topic, data) {
                        //eslint-disable-next-line
                        console.log(topic);
                        //eslint-disable-next-line
                        console.log(data);
                    });
                    dataCollection.env.wsTunnels.notifications.subscribe('messengerNotifications', function(topic, data) {
                        BazMessenger.onMessage(data);
                    });
                },
                function() {
                    //eslint-disable-next-line
                    console.warn('WebSocket connection closed');
                },
                {
                    'skipSubprotocolCheck': true
                }
            );
    }

    function bazTunnelsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazTunnelsConstructor) {
        BazTunnels = BazTunnelsConstructor;
        BazTunnels.defaults = {
            loadHeaderAt : null,
            loadFooterAt : null
        };
        BazTunnels.init = function(options) {
            init(_extends(BazTunnels.defaults, options));
        }
        BazTunnels.getNotificationsCount = function(options) {
            getNotificationsCount(_extends(BazTunnels.defaults, options));
        }
    }

    setup(bazTunnelsConstructor);

    return bazTunnelsConstructor;
}();