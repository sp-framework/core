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
    var element, manualShow;
    var callableFunc = null;
    var dataCollection = window.dataCollection;
    var url
    var postData = { };
    var progressOptions;
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

    function buildProgressBar(el, mS = false) {
        element = el;
        manualShow = mS;

        $(element).html(
            '<div class="progress active progress-xs">' +
                '<div class="progress-bar progress-xs bg-info progress-bar-animated progress-bar-striped ' + $(element)[0].id + '-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%"></div>' +
            '</div>' +
            '<div class="row text-center text-sm text-primary m-1">' +
                '<div class="col">' +
                    '<span class="sr-only ' + $(element)[0].id + '-progress-span"></span>' +
                    '<span class="' + $(element)[0].id + '-progress-span"></span>' +
                '</div>' +
            '</div>'
        );
    }

    function getProgress(options) {
        progressOptions = options;

        if (callableFunc && callableFunc['beforeStart']) {
            if (callableFunc['beforeStart']() === false) {
                resetProgressCounter();
                return;
            }
        }

        if (options && options.url) {
            url = options.url;
        } else {
            url =
                dataCollection.env.httpScheme +
                '://' + dataCollection.env.httpHost + '/' +
                dataCollection.env.appRoute + '/system/progress/getProgress';
        }

        if (options && options.postData) {
            postData = $.extend(postData, options.postData);
        } else {
            postData[$('#security-token').attr('name')] = $('#security-token').val();
        }

        $.post(url, postData, function(response) {
            processResponse(response);
        }, 'json');
    }

    function processResponse(response) {
        if (response && response.length === 0) {
            return;
        }

        if (callableFunc && callableFunc['beforeProcess']) {
            if (callableFunc['beforeProcess'](response) === false) {
                resetProgressCounter();
                return;
            }
        }

        var timerId;
        if (response && response.responseCode === 0) {
            if (response.responseData) {
                var responseData;

                if (typeof response.responseData === 'string' || response.responseData instanceof String) {
                    responseData = JSON.parse(response.responseData);
                } else {
                    responseData = response.responseData;
                }

                if (responseData['preCheckComplete'] == false ||
                    (responseData['callResult'] && responseData['callResult'] === 'reset')
                ) {
                    resetProgressCounter();

                    return false;
                }

                if (responseData['total'] !== 'undefined' && responseData['completed'] !== 'undefined') {
                    if (responseData['total'] !== responseData['completed']) {
                        if (responseData['runners'] && responseData['runners']['running'] !== false) {
                            if (manualShow) {
                                $(element).attr('hidden', true);
                            } else {
                                $(element).attr('hidden', false);
                            }
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
                            if (BazHelpers.getTimerId('getProgress') === false) {
                                BazHelpers.setTimeoutTimers.add(function() {
                                    getProgress(progressOptions);
                                }, 500, null, 'getProgress');
                            }
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

                        if (callableFunc && callableFunc['onComplete']) {
                            callableFunc['onComplete'](response);
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
        } else {
            resetProgressCounter();
        }

        if (callableFunc && callableFunc['afterProcess']) {
            if (callableFunc['afterProcess'](response) === false) {
                resetProgressCounter();
                return;
            }
        }
    }

    function resetProgressCounter() {
        if (progressCounter !== 60) {
            progressCounter ++;

            if (online === false) {
                BazHelpers.setTimeoutTimers.stopAll();
                BazHelpers.setTimeoutTimers.add(function() {
                    getProgress(progressOptions);
                }, 1000, null, 'progressCounter');
            }
        }

        $('#' + $(element)[0].id).attr('hidden', true);
        $('.' + $(element)[0].id + '-bar').css('width', '0%');
        $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', 0);
    }

    function onMessage(data) {
        //eslint-disable-next-line
        console.log(data);
        processResponse(data);
    }

    function setCallable(callable) {
        callableFunc = callable;
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
        BazProgress.getProgress = function(options, force = false) {
            if (online === false || force === true) {
                getProgress(options);
            }
        }
        BazProgress.buildProgressBar = function(el, mS = false) {
            buildProgressBar(el, mS);
        }
        BazProgress.setCallable = function(callable) {
            setCallable(callable);
        }
        BazProgress.isOnline = function() {
            return online;
        }
    }

    setup(bazProgressConstructor);

    return bazProgressConstructor;
}();