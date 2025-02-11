/* exported BazTunnels */
/* globals BazNotifications BazMessenger BazAnnouncements BazProgress ab BazHelpers */
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
    var dataCollection, timerId;
    dataCollection = window.dataCollection;
    dataCollection.env.wsTunnels = { };
    if (dataCollection.env.httpScheme === 'http') {
        dataCollection.env.wsTunnels.protocol = 'ws';
    } else if (dataCollection.env.httpScheme === 'https') {
        dataCollection.env.wsTunnels.protocol = 'wss';
    }
    dataCollection.env.wsTunnels.messenger = { };
    dataCollection.env.wsTunnels.pusher = { };

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init() {
        // initMessengerOTR();
        initPusherTunnel();
    }

    // Init Messenger tunnel as needed. Messages can be transmitted purely on WSS avoiding message to be added to DB.
    function initMessengerOTR() {
        var url;

        if (dataCollection.env.websocketAppRoute) {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/messenger/app/' + dataCollection.env.websocketAppRoute;
        } else {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/messenger/';
        }

        dataCollection.env.wsTunnels.messenger = new WebSocket(url);

        dataCollection.env.wsTunnels.messenger.onopen = null;
        dataCollection.env.wsTunnels.messenger.onopen = function() {
            timerId = BazHelpers.getTimerId('initMessengerOTR');
            if (timerId) {
                BazHelpers.setTimeoutTimers.stop(timerId, null, 'initMessengerOTR');
            }
            BazMessenger.initOTR();
        };

        dataCollection.env.wsTunnels.messenger.onclose = null;
        dataCollection.env.wsTunnels.messenger.onclose = function() {
            BazMessenger.otrServiceOffline();
            BazHelpers.setTimeoutTimers.add(function() {
                BazNotifications.serviceOffline();
                initMessengerOTR();
            }, 10000, null, 'initMessengerOTR');
        };

        dataCollection.env.wsTunnels.messenger.onerror = null;
        dataCollection.env.wsTunnels.messenger.onerror = function() {
            dataCollection.env.wsTunnels.messenger.close();
        };

        dataCollection.env.wsTunnels.messenger.onmessage = null;
        dataCollection.env.wsTunnels.messenger.onmessage = function(e) {
            //eslint-disable-next-line
            console.log(e.data);
        };
    }

    function initPusherTunnel(options) {
        var tunnelsToInit = ['systemNotifications','messengerNotifications','systemAnnouncements','progress'];
        if (options && options.tunnelsToInit) {
            tunnelsToInit = options.tunnelsToInit;
        }

        var url;
        if (dataCollection.env.websocketAppRoute) {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/pusher/app/' + dataCollection.env.websocketAppRoute;
        } else {
            url = dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/pusher/';
        }

        dataCollection.env.wsTunnels.pusher =
            new ab.Session(url,
                function() {
                    //eslint-disable-next-line
                    console.info('WebSocket connection open');

                    if (tunnelsToInit.includes('systemNotifications')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('systemNotifications', function(topic, data) {
                            BazNotifications.onMessage('systemNotifications', data);
                        });
                    }

                    if (tunnelsToInit.includes('messengerNotifications')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('messengerNotifications', function(topic, data) {
                            BazMessenger.onMessage(data);
                        });
                    }

                    if (tunnelsToInit.includes('systemAnnouncements')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('systemAnnouncements', function(topic, data) {
                            BazAnnouncements.onMessage('systemAnnouncements', data);
                        });
                    }

                    if (tunnelsToInit.includes('progress')) {
                        dataCollection.env.wsTunnels.pusher.subscribe('progress', function(topic, data) {
                            BazProgress.onMessage(data);
                        });
                    }

                    timerId = BazHelpers.getTimerId('initPusherTunnel');
                    if (timerId) {
                        BazHelpers.setTimeoutTimers.stop(timerId, null, 'initPusherTunnel');
                        if (tunnelsToInit.includes('systemNotifications')) {
                            BazNotifications.serviceOnline();
                        }
                        if (tunnelsToInit.includes('messengerNotifications')) {
                            BazMessenger.serviceOnline();
                        }
                        if (tunnelsToInit.includes('systemAnnouncements')) {
                            BazAnnouncements.serviceOnline();
                        }
                        if (tunnelsToInit.includes('progress')) {
                            BazProgress.serviceOnline();
                        }
                    } else {
                        if (tunnelsToInit.includes('systemNotifications')) {
                            BazNotifications.init();
                        }
                        if (tunnelsToInit.includes('messengerNotifications')) {
                            BazMessenger.init();
                        }
                        if (tunnelsToInit.includes('systemAnnouncements')) {
                            BazAnnouncements.init();
                        }
                        if (tunnelsToInit.includes('progress')) {
                            BazProgress.init();
                        }
                    }
                },
                function() {
                    if (tunnelsToInit.includes('systemNotifications')) {
                        BazNotifications.serviceOffline();
                    }
                    if (tunnelsToInit.includes('messengerNotifications')) {
                        BazMessenger.serviceOffline();
                    }
                    if (tunnelsToInit.includes('systemAnnouncements')) {
                        BazAnnouncements.serviceOffline();
                    }
                    if (tunnelsToInit.includes('progress')) {
                        BazProgress.serviceOffline();
                    }
                    //eslint-disable-next-line
                    console.warn('WebSocket connection closed');

                    BazHelpers.setTimeoutTimers.add(function() {
                        initPusherTunnel(options);
                    }, 30000, null, 'initPusherTunnel');
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
        BazTunnels.init = function() {
            init();
        }
        BazTunnels.initMessengerOTR = function(options) {
            initMessengerOTR(_extends(BazTunnels.defaults, options));
        }
        BazTunnels.initPusherTunnel = function(options) {
            initPusherTunnel(_extends(BazTunnels.defaults, options));
        }
    }

    setup(bazTunnelsConstructor);

    return bazTunnelsConstructor;
}();