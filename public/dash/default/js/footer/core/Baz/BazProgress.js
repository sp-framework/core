/* exported BazProgress */
/* globals BazHelpers */
/*
* @title                    : BazProgress
* @description              : Baz Progress Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazProgress._function_(_options_);
* @functions                : BazProgress
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazProgress = function() {
    var BazProgress = void 0;
    var initialized = false;
    var progressCounter = 0;
    var online = false;
    var element;
    var dataCollection = window.dataCollection;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        if (!initialized) {
            init(false);
        }

        online = true;
        //eslint-disable-next-line
        console.log('Progress service online');
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }

        online = false;
        //eslint-disable-next-line
        console.log('Progress service offline');
    }

    function buildProgressBar(el) {
        element = el;
        $(element).html(
            '<div class="progress active progress-xs">' +
                '<div class="progress-bar progress-xs bg-info progress-bar-animated progress-bar-striped ' + $(element)[0].id + '-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%"></div>' +
            '</div>' +
            '<div class="row text-center text-sm text-primary m-1">' +
                '<div class="col">' +
                    '<span class="sr-only progress-span"></span>' +
                    '<span class="' + $(element)[0].id + '-progress-span"></span>' +
                '</div>' +
            '</div>'
        );
    }

    function getProgress() {
        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();

        $.post(
            dataCollection.env.httpScheme + '://' + dataCollection.env.httpHost + '/' + dataCollection.env.appRoute + '/system/progress/getProgress',
            postData,
            function(response)
        {
            processResponse(response);
        }, 'json');
    }

    function processResponse(response) {
        var timerId;
        if (response && response.responseCode === 0) {
            if (response.responseData) {
                var responseData;

                if (typeof response.responseData === 'string' || response.responseData instanceof String) {
                    responseData = JSON.parse(response.responseData);
                } else {
                    responseData = response.responseData;
                }

                if (responseData['preCheckComplete'] == false) {
                    resetProgressCounter();

                    return false;
                }

                if (responseData['total'] !== 'undefined' && responseData['completed'] !== 'undefined') {
                    if (responseData['total'] !== responseData['completed']) {
                        if (responseData['runners']['running'] !== false) {
                            $(element).attr('hidden', false);
                            $('.' + $(element)[0].id + '-progress-span')
                                .html(responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)');

                            $('.' + $(element)[0].id + '-bar').css('width', responseData['percentComplete'] + '%');
                            $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', responseData['percentComplete']);
                        }
                        if (online === false) {
                            timerId = BazHelpers.getTimerId('progressCounter');
                            if (timerId) {
                                BazHelpers.setTimeoutTimers.stop(timerId, null, 'progressCounter');
                            }
                            BazHelpers.setTimeoutTimers.add(function() {
                                getProgress();
                            }, 500);
                        }
                    } else if (responseData['total'] === responseData['completed']) {
                        if (online === false) {
                            BazHelpers.setTimeoutTimers.stopAll();
                        }
                        $('.' + $(element)[0].id + '-bar').removeClass('progress-bar-animated');
                        $('.' + $(element)[0].id + '-bar').css('width', '100%');
                        $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', 100);
                        $('.' + $(element)[0].id + '-progress-span').html('Done (100%)');
                        $('.' + $(element)[0].id + '-bar').removeClass(function (index, className) {
                            return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
                        }).addClass('bg-success');
                    } else {
                        resetProgressCounter();
                    }
                } else {
                    resetProgressCounter();
                }
            } else {
                resetProgressCounter();
            }
        } else {
            resetProgressCounter();
        }
    }

    function resetProgressCounter() {
        if (progressCounter !== 60) {
            progressCounter ++;

            if (online === false) {
                BazHelpers.setTimeoutTimers.stopAll();
                BazHelpers.setTimeoutTimers.add(function() {
                    getProgress();
                }, 1000, null, 'progressCounter');
            }
        }
    }

    function onMessage(data) {
        //eslint-disable-next-line
        console.log(data);
        processResponse(data);
    }

    function bazProgressConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazProgressConstructor) {
        BazProgress = BazProgressConstructor;
        BazProgress.defaults = { };
        BazProgress.init = function(options) {
            init(_extends(BazProgress.defaults, options));
        }
        BazProgress.serviceOnline = function(options) {
            serviceOnline(_extends(BazProgress.defaults, options));
        }
        BazProgress.serviceOffline = function(options) {
            serviceOffline(_extends(BazProgress.defaults, options));
        }
        BazProgress.onMessage = function(data) {
            onMessage(data);
        }
        BazProgress.getProgress = function() {
            if (online === false) {
                getProgress();
            }
        }
        BazProgress.buildProgressBar = function(el) {
            buildProgressBar(el);
        }
    }

    setup(bazProgressConstructor);

    return bazProgressConstructor;
}();