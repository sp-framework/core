/* exported BazTunnels */
/* globals BazNotifications BazMessenger BazAnnouncements ab */
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
    var reconnectPusherTunnel = null;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init() {
        dataCollection = window.dataCollection;
        dataCollection.env.wsTunnels = { };
        if (dataCollection.env.httpScheme === 'http') {
            dataCollection.env.wsTunnels.protocol = 'ws';
        } else if (dataCollection.env.httpScheme === 'https') {
            dataCollection.env.wsTunnels.protocol = 'wss';
        }

        dataCollection.env.wsTunnels.pusher = { };
        dataCollection.env.wsTunnels.messenger = { };

        initPusherTunnel();
        // initMessengerOTR();
    }

    // Init Messenger tunnel as needed. Messages can be transmitted purely on WSS avoiding message to be added to DB.
    function initMessengerOTR() {
        dataCollection.env.wsTunnels.messenger = { };
        dataCollection.env.wsTunnels.messenger = new WebSocket(dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/messenger/');

        dataCollection.env.wsTunnels.messenger.onopen = null;
        dataCollection.env.wsTunnels.messenger.onopen = function() {
            if (reconnectMessengerTunnel) {
                clearInterval(reconnectMessengerTunnel);
                reconnectMessengerTunnel = null;
                BazMessenger.serviceOnline();
            } else {
                BazMessenger.init();
            }
        };

        dataCollection.env.wsTunnels.messenger.onclose = null;
        dataCollection.env.wsTunnels.messenger.onclose = function() {
            if (!reconnectMessengerTunnel) {
                BazMessenger.serviceOffline();
                reconnectMessengerTunnel = setInterval(() => {
                    initMessengerOTR();
                }, 10000);
            }
        };

        dataCollection.env.wsTunnels.messenger.onerror = null;
        dataCollection.env.wsTunnels.messenger.onerror = function() {
            dataCollection.env.wsTunnels.messenger.close();
        };

        dataCollection.env.wsTunnels.messenger.onmessage = null;
        dataCollection.env.wsTunnels.messenger.onmessage = function() {
        };
    }

    function initPusherTunnel() {
        // window.ab.debug(true, true);
        dataCollection.env.wsTunnels.pusher = { };
        dataCollection.env.wsTunnels.pusher =
            new ab.Session(dataCollection.env.wsTunnels.protocol + '://' + dataCollection.env.httpHost + '/pusher/',
                function() {
                    //eslint-disable-next-line
                    console.info('WebSocket connection open');
                    dataCollection.env.wsTunnels.pusher.subscribe('systemNotifications', function(topic, data) {
                        BazNotifications.onMessage(data);
                    });
                    dataCollection.env.wsTunnels.pusher.subscribe('messengerNotifications', function(topic, data) {
                        BazMessenger.onMessage(data);
                    });
                    dataCollection.env.wsTunnels.pusher.subscribe('announcements', function(topic, data) {
                        BazAnnouncements.onMessage(data);
                    });
                    if (reconnectPusherTunnel) {
                        clearInterval(reconnectPusherTunnel);
                        reconnectPusherTunnel = null;
                        BazMessenger.serviceOnline();
                        BazNotifications.serviceOnline();
                        BazAnnouncements.serviceOnline();
                    } else {
                        BazMessenger.init();
                        BazNotifications.init();
                        BazAnnouncements.init();
                    }
                },
                function() {
                    //eslint-disable-next-line
                    console.log(reconnectPusherTunnel);
                    if (!reconnectPusherTunnel || reconnectPusherTunnel == null) {
                        BazMessenger.serviceOffline();
                        BazNotifications.serviceOffline();
                        BazAnnouncements.serviceOffline();
                        reconnectPusherTunnel = setInterval(() => {
                            initPusherTunnel();
                        }, 10000);
                    }
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
        BazTunnels.initMessengerOTR = function(options) {
            initMessengerOTR(_extends(BazTunnels.defaults, options));
        }
    }

    setup(bazTunnelsConstructor);

    return bazTunnelsConstructor;
}();