/* exported BazProgress */
/* globals BazHelpers paginatedPNotify Swal dataCollection */
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
    var element, manualShowHide, hasChild, hasSubProcess, hasCancelButton, hasDetails;
    var callableFunc = null;
    var url
    var postData = { };
    var progressOptions;
    var downloadTotal = 0;
    var downloadedBytes = 0;
    var uploadTotal = 0;
    var uploadedBytes = 0;
    var stepsTotal = 0;
    var stepsCurrent = 0;
    var isUpload = false;
    var isDownload = false;
    var isSteps = false;
    var pid = 0;
    var progressFile = null;
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

    function buildProgressBar(el, mSH = false, hC = false, hSP = false, hCB = true, hD = true) {
        element = el;
        manualShowHide = mSH;
        hasChild = hC;
        hasSubProcess = hSP;
        hasCancelButton = hCB;
        hasDetails = hD;

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

        if (hasChild) {
            $(element).append(
                '<div class="progress progress-child active progress-xxs" hidden>' +
                    '<div class="progress-bar progress-xxs bg-info progress-bar-animated progress-bar-striped ' + $(element)[0].id + '-child-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%"></div>' +
                '</div>' +
                '<div class="row child-progress-span text-center text-sm text-primary m-1">' +
                    '<div class="col">' +
                        '<span class="sr-only ' + $(element)[0].id + '-child-progress-span"></span>' +
                        '<span class="' + $(element)[0].id + '-child-progress-span"></span>' +
                    '</div>' +
                '</div>'
            );
        }

        if (hasSubProcess) {
            $(element).append(
                '<div class="progress progress-remote active progress-xxs" hidden>' +
                    '<div class="progress-bar progress-xxs bg-primary progress-bar-animated progress-bar-striped ' + $(element)[0].id + '-remote-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0" style="width: 0%"></div>' +
                '</div>' +
                '<div class="row remote-progress-span text-center text-sm text-primary m-1">' +
                    '<div class="col">' +
                        '<span class="sr-only ' + $(element)[0].id + '-remote-progress-span"></span>' +
                        '<span class="' + $(element)[0].id + '-remote-progress-span"></span>' +
                    '</div>' +
                '</div>'
            );
        }

        var buttons = '<div class="row">';

        if (hasCancelButton) {
            buttons +=
                '<div class="col">' +
                    '<div class="text-center mt-3" id="' + $(element)[0].id + '-cancel" hidden>' +
                        '<button class="text-uppercase btn btn-sm btn-secondary mr-1" id="' + $(element)[0].id + '-cancel-button" role="button">' +
                            'Cancel' +
                        '</button>' +
                    '</div>' +
                '</div>';
        }

        if (hasDetails) {
            buttons +=
                '<div class="col">' +
                    '<div class="text-center mt-3" id="' + $(element)[0].id + '-details">' +
                        '<button class="text-uppercase btn btn-sm btn-primary mr-1" id="' + $(element)[0].id + '-details-button" role="button">' +
                            'Show Details' +
                        '</button>' +
                    '</div>' +
                '</div>';
        }

        buttons += '</div>';

        $(element).append(buttons);

        if (hasCancelButton) {
            $('#' + $(element)[0].id + '-cancel-button').off();
            $('#' + $(element)[0].id + '-cancel-button').click(function() {
                if (pid > 0) {
                    Swal.fire({
                        title                       : '<span class="text-danger"> Cancel current process?</span>',
                        icon                        : 'question',
                        background                  : 'rgba(0,0,0,.8)',
                        backdrop                    : 'rgba(0,0,0,.6)',
                        buttonsStyling              : false,
                        confirmButtonText           : 'Yes',
                        customClass                 : {
                            'confirmButton'             : 'btn btn-danger btn-sm text-uppercase',
                            'cancelButton'              : 'ml-2 btn btn-secondary btn-sm text-uppercase',
                        },
                        showCancelButton            : true,
                        keydownListenerCapture      : true,
                        allowOutsideClick           : true,
                        allowEscapeKey              : true,
                        didOpen                     : function() {
                            dataCollection.env.sounds.swalSound.play();
                        }
                    }).then((result) => {
                        if (result.value) {
                            var cancelUrl =
                                dataCollection.env.httpScheme + '://' +
                                dataCollection.env.httpHost + '/' +
                                dataCollection.env.appRoute + '/system/progress/cancelProgress';

                            var postData = { };
                            postData[$('#security-token').attr('name')] = $('#security-token').val();

                            if (progressFile) {
                                postData['file_name'] = progressFile;
                            }

                            $.post(cancelUrl, postData, function(response) {
                                if (response.responseCode == 0) {
                                    paginatedPNotify('success', {
                                        'title' : response.responseMessage
                                    });

                                    $('body').trigger('bazProgressCancelled');

                                    resetProgressCounter();
                                } else {
                                    paginatedPNotify('error', {
                                        'title' : response.responseMessage
                                    });
                                }
                            }, 'json');
                        }
                    });
                }
            });
        }

        if (hasDetails) {
            $('#' + $(element)[0].id + '-details-button').off();
            $('#' + $(element)[0].id + '-details-button').click(function() {
                var text = $('#' + $(element)[0].id + '-details-button').text().toLowerCase();

                if (text === 'show details') {
                    $('#' + $(element)[0].id + '-details-button').text('Hide details');
                    $('#details-div').attr('hidden', false);
                } else if (text === 'hide details') {
                    $('#' + $(element)[0].id + '-details-button').text('Show details');
                    $('#details-div').attr('hidden', true);
                }
            });

            $(element).append(
                '<div class="row" id="details-div" hidden>' +
                    '<div class="col">' +
                        '<div class="form-group">' +
                            '<label class="text-uppercase text-xs" for="username">Progress Details</label>' +
                            '<div id="details-data" class="height-control-400 overflow-scroll"></div>' +
                        '</div>' +
                    '</div>' +
                '</div>'
            );
        }
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

                if (responseData['pid']) {
                    $('#' + $(element)[0].id + '-cancel').attr('hidden', false);
                    pid = responseData['pid'];

                    $('#details-data').html('PID running : Progress is running with process ID: ' + pid + '<br>');
                }

                if (responseData['progressFile']) {
                    progressFile = responseData['progressFile'];
                }

                if (responseData['preCheckComplete'] == false ||
                    (responseData['callResult'] && responseData['callResult'] === 'reset') ||
                    (responseData['callResult'] && responseData['callResult'] === 'pid_running')
                ) {
                    if (responseData['errors']) {
                        $('#details-div').attr('hidden', false);
                        var html = responseData['errors'];
                        var regex = /{.*}/g;
                        var found = html.match(regex);

                        if (found) {
                            //eslint-disable-next-line
                            var data = found[0].replaceAll('\"', '"');
                            //eslint-disable-next-line
                            data = data.replaceAll('\\', '\\\\\\\\');
                            //eslint-disable-next-line
                            data = data.replaceAll('\\\\\\u0022', '"');
                            var obj = JSON.parse(data);

                            if (obj) {
                                $('#details-data').html(
                                    '<div class="text-danger">' +
                                        BazHelpers.createHtmlList({'obj': obj}));
                                    '</div>'
                            }
                        } else {
                            $('#details-data').html(html);
                        }

                        $('#details-data').scrollTop($('#details-data')[0].scrollHeight);
                    }

                    if (responseData['callResult'] === 'pid_running') {
                        $('#' + $(element)[0].id).attr('hidden', false);
                        $('#' + $(element)[0].id + '-cancel').attr('hidden', false);
                        $('#' + $(element)[0].id + '-cancel-button').text('Cancel Process: ' + responseData['pid']);
                    }

                    if (responseData['callResult'] && responseData['callResult'] === 'reset') {
                        if (!responseData['errors']) {
                            resetProgressCounter();
                        }
                    }

                    return false;
                }

                if (responseData['details']) {
                    var pidHtml = $('#details-data').html();
                    $('#details-data').html(pidHtml + BazHelpers.createHtmlList({'obj': responseData['details']}));
                }

                if (responseData['total'] !== 'undefined' && responseData['completed'] !== 'undefined') {
                    if (responseData['total'] !== responseData['completed']) {
                        if (responseData['runners'] && responseData['runners']['running'] !== false) {
                            if (manualShowHide) {
                                $(element).attr('hidden', true);
                            } else {
                                $(element).attr('hidden', false);
                            }

                            if (responseData['runners']['child']) {
                                $('#' + $(element)[0].id + ' .progress-child').attr('hidden', false);
                                $('.child-progress-span').attr('hidden', false);

                                if (responseData['runners']['running'] &&
                                    (responseData['runners']['running']['remoteWeb'] &&
                                     responseData['runners']['running']['remoteWebCounters']) ||
                                    (responseData['runners']['running']['steps'] &&
                                     responseData['runners']['running']['stepsCounters'])
                                ) {
                                    $('#' + $(element)[0].id + ' .progress-remote').attr('hidden', false);
                                    $('.remote-progress-span').attr('hidden', false);

                                    $('.' + $(element)[0].id + '-remote-progress-span')
                                        .html(responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)');

                                    $('.' + $(element)[0].id + '-remote-bar').css('width', responseData['percentComplete'] + '%');
                                    $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', responseData['percentComplete']);

                                    if ($('.' + $(element)[0].id + '-progress-span').html() === '') {
                                        $('.' + $(element)[0].id + '-progress-span')
                                            .html(responseData['runners']['running']['text'] + ' (' + responseData['totalPercentComplete'] + '%)');

                                        $('.' + $(element)[0].id + '-bar').css('width', responseData['totalPercentComplete'] + '%');
                                        $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', responseData['totalPercentComplete']);
                                    }
                                } else {
                                    $('.progress-remote, .remote-progress-span').attr('hidden', true);

                                    $('.' + $(element)[0].id + '-child-progress-span')
                                        .html(responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)');

                                    $('.' + $(element)[0].id + '-child-bar').css('width', responseData['percentComplete'] + '%');
                                    $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', responseData['percentComplete']);
                                }
                            } else {
                                $('#' + $(element)[0].id + ' .progress-child').attr('hidden', true);
                                $('.child-progress-span').attr('hidden', true);
                                $('.' + $(element)[0].id + '-child-bar').css('width', '0%');
                                $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', 0);
                                switchProgressBarColor('.' + $(element)[0].id + '-child-bar', 'info');

                                if (responseData['runners']['running'] &&
                                    (responseData['runners']['running']['remoteWeb'] &&
                                     responseData['runners']['running']['remoteWebCounters']) ||
                                    (responseData['runners']['running']['steps'] &&
                                     responseData['runners']['running']['stepsCounters'])
                                ) {
                                    if (responseData['runners']['running']['remoteWebCounters'] ||
                                        responseData['runners']['running']['stepsCounters']
                                    ) {
                                        $('#' + $(element)[0].id + ' .progress-remote').attr('hidden', false);
                                        $('.remote-progress-span').attr('hidden', false);

                                        var text = getText(responseData);

                                        $('.' + $(element)[0].id + '-remote-progress-span').html(text);

                                        $('.' + $(element)[0].id + '-remote-bar').css('width', responseData['percentComplete'] + '%');
                                        $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', responseData['percentComplete']);

                                        if ($('.' + $(element)[0].id + '-progress-span').html() === '') {
                                            $('.' + $(element)[0].id + '-progress-span')
                                                .html(responseData['runners']['running']['text'] + ' (' + responseData['totalPercentComplete'] + '%)');

                                            $('.' + $(element)[0].id + '-bar').css('width', responseData['totalPercentComplete'] + '%');
                                            $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', responseData['totalPercentComplete']);
                                        }
                                    } else {
                                        $('.' + $(element)[0].id + '-remote-bar').css('width', '0%');
                                        $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', 0);
                                        switchProgressBarColor('.' + $(element)[0].id + '-remote-bar', 'info');
                                    }
                                } else {
                                    $('.progress-remote, .remote-progress-span').attr('hidden', true);

                                    $('.' + $(element)[0].id + '-progress-span')
                                        .html(responseData['runners']['running']['text'] + ' (' + responseData['totalPercentComplete'] + '%)');

                                    $('.' + $(element)[0].id + '-bar').css('width', responseData['totalPercentComplete'] + '%');
                                    $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', responseData['totalPercentComplete']);
                                }
                            }
                        } else {
                            $('.progress-remote, .remote-progress-span').attr('hidden', true);
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

                        if (responseData['runners']['child']) {
                            $('.' + $(element)[0].id + '-child-bar').removeClass('progress-bar-animated');
                            $('.' + $(element)[0].id + '-child-bar').css('width', '100%');
                            $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', 100);
                            $('.' + $(element)[0].id + '-child-progress-span').html('Done (100%)');
                            switchProgressBarColor('.' + $(element)[0].id + '-child-bar', 'success');
                        } else {
                            $('.' + $(element)[0].id + '-bar').removeClass('progress-bar-animated');
                            $('.' + $(element)[0].id + '-bar').css('width', '100%');
                            $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', 100);
                            $('.' + $(element)[0].id + '-progress-span').html('Done (100%)');
                            switchProgressBarColor('.' + $(element)[0].id + '-bar', 'success');
                            $('#' + $(element)[0].id + ' .progress-child').attr('hidden', true);
                            $('.child-progress-span').attr('hidden', true);

                            if (manualShowHide) {
                                $(element).attr('hidden', true);
                            } else {
                                $(element).attr('hidden', false);
                            }

                            if (callableFunc && callableFunc['onComplete']) {
                                callableFunc['onComplete'](response);
                            }

                            downloadTotal = 0;
                            downloadedBytes = 0;
                            uploadTotal = 0;
                            uploadedBytes = 0;
                            stepsTotal = 0;
                            stepsCurrent = 0;
                            isUpload = false;
                            isDownload = false;
                            isSteps = false;
                            pid = 0;
                            progressFile = null;
                            $('.' + $(element)[0].id + '-child-bar').css('width', '0%');
                            $('.' + $(element)[0].id + '-child-bar').attr('aria-valuenow', 0);
                            switchProgressBarColor('.' + $(element)[0].id + '-child-bar', 'info');
                            $('.' + $(element)[0].id + '-remote-bar').css('width', '0%');
                            $('.' + $(element)[0].id + '-remote-bar').attr('aria-valuenow', 0);
                            switchProgressBarColor('.' + $(element)[0].id + '-remote-bar', 'info');
                            $('.progress-remote, .remote-progress-span').attr('hidden', true);
                            $('#' + $(element)[0].id + '-cancel').attr('hidden', true);
                            $('body').trigger('bazProgressComplete');
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

    function switchProgressBarColor(bar = null, color = 'info') {
        if (!bar) {
            bar = '.' + $(element)[0].id + '-bar';
        }

        $(bar).removeClass(function (index, className) {
            return (className.match (/(^|\s)bg-\S+/g) || []).join(' ');
        }).addClass('bg-' + color);
    }

    function getText(responseData) {
        var text = responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '%)';

        if (responseData['runners']['running']['remoteWebCounters']) {
            if (responseData['runners']['running']['remoteWebCounters']['downloadTotal'] &&
                responseData['runners']['running']['remoteWebCounters']['downloadTotal'] > 0
            ) {
                isDownload = true;
                isUpload = false;
                isSteps = false;
                downloadTotal = responseData['runners']['running']['remoteWebCounters']['downloadTotal'];
                downloadedBytes = responseData['runners']['running']['remoteWebCounters']['downloadedBytes'];
            } else if (responseData['runners']['running']['remoteWebCounters']['uploadTotal'] &&
                responseData['runners']['running']['remoteWebCounters']['uploadTotal'] > 0
            ) {
                isUpload = true;
                isDownload = false;
                isSteps = false;
                uploadTotal = responseData['runners']['running']['remoteWebCounters']['uploadTotal'];
                uploadedBytes = responseData['runners']['running']['remoteWebCounters']['uploadedBytes'];
            }
        } else if (responseData['runners']['running']['stepsCounters']) {
            if (responseData['runners']['running']['stepsCounters']['stepsTotal'] &&
                responseData['runners']['running']['stepsCounters']['stepsTotal'] > 0
            ) {
                isSteps = true;
                isDownload = false;
                isUpload = false;
                stepsTotal = responseData['runners']['running']['stepsCounters']['stepsTotal'];
                stepsCurrent = responseData['runners']['running']['stepsCounters']['stepsCurrent'];
            }
        }

        if (isDownload || isUpload || isSteps) {
            if (isDownload) {
                text = responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '% | ' + downloadedBytes + '/' + downloadTotal + ' bytes)';
            } else if (isUpload) {
                text = responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '% | ' + uploadedBytes + '/' + uploadTotal + ' bytes)';
            } else if (isSteps) {
                text = responseData['runners']['running']['text'] + ' (' + responseData['percentComplete'] + '% | ' + stepsCurrent + '/' + stepsTotal + ' steps)';
            }
        }

        return text;
    }

    function resetProgressCounter(hideDiv = true) {
        if (progressCounter !== 60) {
            progressCounter ++;

            if (online === false) {
                BazHelpers.setTimeoutTimers.stopAll();
                BazHelpers.setTimeoutTimers.add(function() {
                    getProgress(progressOptions);
                }, 1000, null, 'progressCounter');
            }
        }

        if (hideDiv) {
            $('#' + $(element)[0].id).attr('hidden', true);
            $('#details-div').attr('hidden', true);
        }
        $('.' + $(element)[0].id + '-bar').css('width', '0%');
        $('.' + $(element)[0].id + '-bar').attr('aria-valuenow', 0);
        switchProgressBarColor('.' + $(element)[0].id + '-bar', 'info');
        $('#' + $(element)[0].id + '-cancel-button').text('Cancel');
        downloadTotal = 0;
        downloadedBytes = 0;
        uploadTotal = 0;
        uploadedBytes = 0;
        stepsTotal = 0;
        stepsCurrent = 0;
        isUpload = false;
        isDownload = false;
        isSteps = false;
        pid = 0;
        progressFile = null;
        $('body').trigger({'type':'bazProgressComplete', 'reset' : true});
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
        BazProgress.buildProgressBar = function(el, mSH = false, child = false, hasSubProcess = false, hasCancelButton = true, hasDetails = true) {
            buildProgressBar(el, mSH, child, hasSubProcess, hasCancelButton, hasDetails);
        }
        BazProgress.switchProgressBarColor = function(el, color) {
            switchProgressBarColor(el, color);
        }
        BazProgress.resetProgressCounter = function() {
            resetProgressCounter();
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
