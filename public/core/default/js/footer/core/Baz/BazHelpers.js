/* exported BazHelpers */
/* globals */
/*
* @title                    : BazHelpers
* @description              : Baz Helper Tools Lib (include Various helper tools)
* @developer                : guru@bazaari.com.au
* @usage                    : BazHelpers._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazHelpers = function() {
    var BazHelpers = void 0;

    // Error
    function error(errorMsg) {
        throw new Error(errorMsg);
    }

    function bazModal(options) {
        var close, closeButton, createButton, updateButton, title, modalCentered, modalScrollable, modalSize, modalWidth;
        if (!options.modalId) {
            error('modalId not present!');
        } else {
            if (options.modalTitle) {
                title = options.modalTitle;
            } else {
                title = '';
            }
            if (options.modalCentered) {
                modalCentered = 'modal-dialog-centered';
            } else {
                modalCentered = '';
            }
            if (options.modalScrollable) {
                modalScrollable = 'modal-dialog-scrollable';
            } else {
                modalScrollable = '';
            }
            if (options.modalSize) {
                modalSize = 'modal-' + options.modalSize;
            } else {
                modalSize = '';
            }

            if (options.modalWidth) {
                modalWidth = 'style="max-width:' + options.modalWidth + ';"';
            } else {
                modalWidth = '';
            }
            if (options.modalButtons.close) {
                closeButton = '<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Close</button>';
                close = '<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">' +
                            '<span aria-hidden="true">&times;</span>' +
                        '</button>'
            } else {
                closeButton = '';
                close = '';
            }
            if (options.modalButtons.create) {
                createButton = '<button type="button" class="btn btn-sm btn-' + options.modalButtons.create.type + '">' + options.modalButtons.create.title + '</button>'
            } else {
                createButton = '';
            }
            if (options.modalButtons.update) {
                updateButton = '<button type="button" class="btn btn-sm btn-' + options.modalButtons.update.type + '">' + options.modalButtons.update.title + '</button>'
            } else {
                updateButton = '';
            }
        }
        var modalHTML =
            '<div class="modal fadeIn ' + options.modalAdditionalClasses + '" id="' + options.modalId + '-modal" tabindex="-1"  aria-labelledby="' +
            options.modalId + '-label" aria-hidden="true" data-backdrop="' + options.modalBackdrop + '" data-keyboard="' + options.modalEscClose + '">' +
            '<div ' + modalWidth + ' class="modal-dialog ' + modalCentered + ' ' + modalScrollable + ' ' + modalSize + '" role="document">' +
            '<div class="modal-content rounded-0 ' + options.modalContentAdditionalClasses + '">';

        if (options.modalHeader) {
            modalHTML +=
                '<div class="modal-header border-bottom-0 rounded-0 bg-' + options.modalType + ' ' + options.modalHeaderAdditionalClasses + '"><h5 class="modal-title" id="' +
                options.modalId + '-label">' + title + '</h5>' + close + '</div>';
        }

        modalHTML += '<div class="modal-body ' + options.modalBodyAdditionalClasses + '"></div>';

        if (options.modalFooter) {
            modalHTML += '<div class="modal-footer ' + options.modalFooterAdditionalClasses + '">' +
                                closeButton +
                                createButton +
                                updateButton +
                            '</div>';
        }

        modalHTML += '</div></div></div>';

        $(options.modalAppendOn).append(modalHTML);
    }

    function bazCreateHtmlList(obj) {
        var output = '';
        output += '<ul>';
        Object.keys(obj).forEach(function(k) {
            if (typeof obj[k] == "object" && obj[k] !== null){
                output += '<li>' + k + ' : ';
                output += bazCreateHtmlList(obj[k]);
                output += '</li>';
            } else {
                output += '<li>' + k + ' : ' + obj[k] + '</li>';
            }
        });
        output += '</ul>';

        return output;
    }

    // Copy object
    function iterationCopy(src) {
        var target = { };
        for (var prop in src) {
            if (src.hasOwnProperty(prop)) {
                target[prop] = src[prop];
            }
        }
        return target;
    }

    // Check if Object Empty
    function isEmpty(obj) {
        for (var prop in obj) {
            if (obj.hasOwnProperty(prop)) {
                return false;
            }
        }
        return true;
    }

    //find object key by value
    function getKeyByValue(objectOrArray, value) {
        if (typeof objectOrArray === 'object') {
            return Object.keys(objectOrArray).find(key => objectOrArray[key] == value);
        } else if (Array.isArray(objectOrArray)) {
            return objectOrArray.indexOf(value);
        }

        return false;
    }

    function bazHelpersConstructor() {
        // if something needs to be constructed
        return null;
    }

    /**
     * @param {*} val - value to check
     *
     * @returns {boolean} true if the value is then-able
     */
    function isPromise(val) {
        return val != null && typeof val.then === 'function';
    }

    /**
     * src  github interval-promise
     * @param {function} func - function to execute
     * @param {number|function(number):number} intervalLength - length in ms to wait before executing again
     * @param {{iterations: Infinity|number, stopOnError: boolean}} [options]
     *
     * @returns {Promise} Promise object with no result
     */
    function interval(func, intervalLength, options = { }) {

        validateArgs(func, intervalLength, options);

        var defaults = {
            iterations: Infinity,
            stopOnError: true
        };
        var settings = Object.assign(defaults, options);

        return new Promise(function (rootPromiseResolve, rootPromiseReject) {

            var callFunction = function callFunction(currentIteration) {

                // Set up a way to track if a "stop" was requested by the user function
                var stopRequested = false;
                var stop = function stop() {
                    stopRequested = true;
                };

                // Set up a function to call the next iteration. This is abstracted so it can be called by .then(), or in .catch(), if options allow.
                var callNext = function callNext() {
                    // If we've hit the desired number of iterations, or stop was called, resolve the root promise and return
                    if (currentIteration === settings.iterations || stopRequested) {
                        rootPromiseResolve();
                        return;
                    }

                    // Otherwise, call the next iteration
                    callFunction(currentIteration + 1);
                };

                // Calculate our interval length
                var calculatedIntervalLength = typeof intervalLength === 'function' ? intervalLength(currentIteration) : intervalLength;

                // If the interval length was calculated, validate the result
                if (typeof intervalLength === 'function') {
                    if (!Number.isInteger(calculatedIntervalLength) || calculatedIntervalLength < 0) {
                        rootPromiseReject(new Error('Function for "intervalLength" argument must return a non-negative integer.'));
                        return;
                    }
                }

                // Call the user function after the desired interval length. After, call the next iteration (and/or handle error)
                setTimeout(function () {

                    var returnVal = func(currentIteration, stop);

                    // Ensure that the value returned is a promise
                    if (!isPromise(returnVal)) {
                        rootPromiseReject(new Error('Return value of "func" must be a Promise.'));
                        return;
                    }

                    returnVal.then(callNext).catch(function (err) {
                        if (!settings.stopOnError) {
                            callNext();
                            return;
                        }

                        rootPromiseReject(err);
                    });
                }, calculatedIntervalLength);
            };

            callFunction(1);
        });
    }

    /**
     * A helper function to validate the arguments passed to interval(...)
     *
     * @param {*} func
     * @param {*} intervalLength
     * @param {*} options
     */
    function validateArgs(func, intervalLength, options) {

        // Validate "func"
        if (typeof func !== 'function') {
            throw new TypeError('Argument 1, "func", must be a function.');
        }

        // Validate "intervalLength"
        if (typeof intervalLength === 'number') {
            if (!Number.isInteger(intervalLength) || intervalLength < 0) {
                throw new TypeError('Argument 2, "intervalLength", must be a non-negative integer or a function that returns a non-negative integer.');
            }
        } else if (typeof intervalLength !== 'function') {
            throw new TypeError('Argument 2, "intervalLength", must be a non-negative integer or a function that returns a non-negative integer.');
        }

        // Validate options...
        if ((typeof options === 'undefined' ? 'undefined' : _typeof(options)) !== 'object') {
            throw new TypeError('Argument 3, "options", must be an object.');
        }

        // Validate passed keys
        var allowedKeys = ['iterations', 'stopOnError'];

        Object.keys(options).forEach(function (key) {
            if (!allowedKeys.includes(key)) {
                throw new TypeError('Option "' + key + '" is not a valid option.');
            }
        });

        // validate "iterations" option (if passed)
        if (typeof options.iterations !== 'undefined') {
            if (options.iterations !== Infinity && (!Number.isInteger(options.iterations) || options.iterations < 1)) {
                throw new TypeError('Option "iterations" must be Infinity or an integer greater than 0.');
            }
        }

        // validate "stopOnError" option (if passed)
        if (typeof options.stopOnError !== 'undefined') {
            if (typeof options.stopOnError !== 'boolean') {
                throw new TypeError('Option "stopOnError" must be a boolean.');
            }
        }
    }

    function findPath(obj, key, array = true) {
        const path = [];
        const keyExists = (obj) => {
            if (!obj || (typeof obj !== "object" && !Array.isArray(obj))) {
                return false;
            }
            else if (obj.hasOwnProperty(key)) {
                return true;
            }
            else if (Array.isArray(obj)) {
                let parentKey = path.length ? path.pop() : "";

                for (let i = 0; i < obj.length; i++) {
                    path.push(`${parentKey}[${i}]`);
                    const result = keyExists(obj[i], key);
                    if (result) {
                        return result;
                    }
                    path.pop();
                }
            }
            else {
                for (const k in obj) {
                    path.push(k);
                    const result = keyExists(obj[k], key);
                    if (result) {
                        return result;
                    }
                    path.pop();
                }
            }
            return false;
        };

        keyExists(obj);

        if (array) {
            return path;
        }

        return path.join(".");
    }

    function fetchFromObject(obj, key) {
        if (typeof obj === 'undefined') {
            return false;
        }

        var _index = key.indexOf('.')

        if (_index > -1) {
            return fetchFromObject(obj[key.substring(0, _index)], key.substr(_index + 1));
        }

        return obj[key];
    }

    function setTimeoutTimers() {
        var timers = []

        const getIndex = (array, attr, value) => {
            for (let i = 0; i < array.length; i += 1) {
                if (array[i][attr] === value) {
                    return i
                }
            }
            return -1
        };

        // add
        const add = (func, time = 1000, dataCollectionObj = null, identifier = null) => {
            var id = setTimeout(() => {
                let index = getIndex(timers, 'id', id)
                timers.splice(index, 1)
                func()
            }, time);

            timers.push({
                id: id,
                dataCollectionObj : dataCollectionObj,
                identifier : identifier,
                time: time,
                debug: func.toString()
            });

            return true;
        };

        // get all active timers
        const all = () => timers

        // stop timer by timer id
        const stop = (id = null, dataCollectionObj = null, identifier = null) => {
            let index;
            if (dataCollectionObj) {
                index = getIndex(timers, 'dataCollectionObj', dataCollectionObj);
                if (index !== -1) {
                    id = timers[index]['id'];
                }
            } else if (identifier) {
                index = getIndex(timers, 'identifier', identifier);
                if (index !== -1) {
                    id = timers[index]['id'];
                }
            } else {
                index = getIndex(timers, 'id', id);
            }
            if (index !== -1) {
                clearTimeout(timers[index].id)
                timers.splice(index, 1)
            }

            return true;
        };

        // stop all timers
        const stopAll = () => {
            for (let i = 0; i < timers.length; i++) {
                clearTimeout(timers[i].id)
            }
            timers = []

            return timers;
        };

        return {
            add: add,
            all: all,
            stop: stop,
            stopAll: stopAll,
        };
    }

    function getTimerId(identifier) {
        var timers = BazHelpers.setTimeoutTimers.all();
        var id = false;

        if (timers.length > 0) {
            $(timers).each(function(i, timer) {
                if (timer['identifier'] === identifier) {
                    id = timer['id'];
                }
            });
        }

        return id;
    }

    function ping(source, option, callback) {
        var promise, resolve, reject;
        this.option = option || {};
        this.favicon = this.option.favicon || "/favicon.ico";
        this.logError = this.option.logError || false;

        if (typeof Promise !== "undefined") {
            promise = new Promise(function(_resolve, _reject) {
                resolve = _resolve;
                reject = _reject;
            });
        }

        var self = this;
        self.wasSuccess = false;
        self.img = new Image();
        self.img.onload = onload;
        self.img.onerror = onerror;

        var timer = null;
        var start = new Date();

        function onload(e) {
            self.wasSuccess = true;
            pingCheck.call(self, e);
        }

        function onerror(e) {
            self.wasSuccess = false;
            pingCheck.call(self, e);
        }

        /**
         * Times ping and triggers callback.
         */
        function pingCheck() {
            if (timer) {
                clearTimeout(timer);
            }

            var pong = new Date() - start;

            if (!callback) {
                if (promise) {
                    return this.wasSuccess ? resolve(pong) : reject(pong);
                } else {
                    throw new Error("Promise is not supported by your browser. Use callback instead.");
                }
            } else if (typeof callback === "function") {
                if (!this.wasSuccess) {
                    if (self.logError) {
                        //eslint-disable-next-line
                        console.error("error loading resource");
                    }
                    if (promise) {
                        reject(pong);
                    }

                    return callback("error", pong);
                }

                if (promise) {
                    resolve(pong);
                }

                return callback(null, pong);
            } else {
                throw new Error("Callback is not a function.");
            }
        }

        self.img.src = source + self.favicon + "?" + (+new Date()); // Trigger image load with cache buster

        return promise;
    }

    function capitalizeFirstLetter(string) {
        return string.charAt(0).toUpperCase() + string.slice(1);
    }

    function stringToBoolean(string) {
        switch (string.toLowerCase().trim()) {
            case "true":
            case "yes":
            case "1":
              return true;

            case "false":
            case "no":
            case "0":
            case null:
            case undefined:
            case 'undefined':
              return false;

            default:
              return JSON.parse(string);
        }
    }

    function setup(BazHelpersConstructor) {
        BazHelpers = BazHelpersConstructor;

        BazHelpers.defaults = {
            'modalId'                               : '',
            'modalTitle'                            : '',
            'modalCentered'                         : false,
            'modalScrollable'                       : false,
            'modalSize'                             : 'lg',
            'modalWidth'                            : '',
            'modalAdditionalClasses'                : '',
            'modalBackdrop'                         : 'static',
            'modalEscClose'                         : true,
            'modalContentAdditionalClasses'         : '',
            'modalHeader'                           : true,
            'modalType'                             : 'primary',
            'modalHeaderAdditionalClasses'          : '',
            'modalBodyAdditionalClasses'            : '',
            'modalFooter'                           : true,
            'modalFooterAdditionalClasses'          : '',
            'modalAppendOn'                         : 'body',
            'modalButtons'                          : {
                'close'                             : false,
                'create'                            : {
                    'id'                            : 'add',
                    'title'                         : 'Add',
                    'type'                          : 'primary',
                    'action'                        : 'post',
                    'actionUrl'                     : '{{createActionUrl}}',
                    'createSuccessRedirectUrl'      : '{{createSuccessRedirectUrl}}',
                    'createSuccessNotifyMessage'    : '{{createSuccessNotifyMessage}}'
                },
                'update'                            : {
                    'id'                            : 'update',
                    'title'                         : 'Update',
                    'type'                          : 'primary',
                    'action'                        : 'post',
                    'actionUrl'                     : '{{updateActionUrl}}',
                    'updateSuccessRedirectUrl'      : '{{updateSuccessRedirectUrl}}',
                    'createSuccessNotifyMessage'    : '{{createSuccessNotifyMessage}}'
                }
            }
        }

        BazHelpers.modal = function(options) {
            bazModal(_extends(BazHelpers.defaults, options));
        }

        BazHelpers.createHtmlList = function(options) {
            var objToHtml = bazCreateHtmlList(options.obj);
            return objToHtml;
        }

        BazHelpers.iterationCopy = function(src) {
            return iterationCopy(src);
        }

        BazHelpers.md5 = function(d){var r = M(V(Y(X(d),8*d.length)));return r.toLowerCase()};function M(d){for(var _,m="0123456789ABCDEF",f="",r=0;r<d.length;r++)_=d.charCodeAt(r),f+=m.charAt(_>>>4&15)+m.charAt(15&_);return f}function X(d){for(var _=Array(d.length>>2),m=0;m<_.length;m++)_[m]=0;for(m=0;m<8*d.length;m+=8)_[m>>5]|=(255&d.charCodeAt(m/8))<<m%32;return _}function V(d){for(var _="",m=0;m<32*d.length;m+=8)_+=String.fromCharCode(d[m>>5]>>>m%32&255);return _}function Y(d,_){d[_>>5]|=128<<_%32,d[14+(_+64>>>9<<4)]=_;for(var m=1732584193,f=-271733879,r=-1732584194,i=271733878,n=0;n<d.length;n+=16){var h=m,t=f,g=r,e=i;f=md5_ii(f=md5_ii(f=md5_ii(f=md5_ii(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_hh(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_gg(f=md5_ff(f=md5_ff(f=md5_ff(f=md5_ff(f,r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+0],7,-680876936),f,r,d[n+1],12,-389564586),m,f,d[n+2],17,606105819),i,m,d[n+3],22,-1044525330),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+4],7,-176418897),f,r,d[n+5],12,1200080426),m,f,d[n+6],17,-1473231341),i,m,d[n+7],22,-45705983),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+8],7,1770035416),f,r,d[n+9],12,-1958414417),m,f,d[n+10],17,-42063),i,m,d[n+11],22,-1990404162),r=md5_ff(r,i=md5_ff(i,m=md5_ff(m,f,r,i,d[n+12],7,1804603682),f,r,d[n+13],12,-40341101),m,f,d[n+14],17,-1502002290),i,m,d[n+15],22,1236535329),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+1],5,-165796510),f,r,d[n+6],9,-1069501632),m,f,d[n+11],14,643717713),i,m,d[n+0],20,-373897302),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+5],5,-701558691),f,r,d[n+10],9,38016083),m,f,d[n+15],14,-660478335),i,m,d[n+4],20,-405537848),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+9],5,568446438),f,r,d[n+14],9,-1019803690),m,f,d[n+3],14,-187363961),i,m,d[n+8],20,1163531501),r=md5_gg(r,i=md5_gg(i,m=md5_gg(m,f,r,i,d[n+13],5,-1444681467),f,r,d[n+2],9,-51403784),m,f,d[n+7],14,1735328473),i,m,d[n+12],20,-1926607734),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+5],4,-378558),f,r,d[n+8],11,-2022574463),m,f,d[n+11],16,1839030562),i,m,d[n+14],23,-35309556),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+1],4,-1530992060),f,r,d[n+4],11,1272893353),m,f,d[n+7],16,-155497632),i,m,d[n+10],23,-1094730640),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+13],4,681279174),f,r,d[n+0],11,-358537222),m,f,d[n+3],16,-722521979),i,m,d[n+6],23,76029189),r=md5_hh(r,i=md5_hh(i,m=md5_hh(m,f,r,i,d[n+9],4,-640364487),f,r,d[n+12],11,-421815835),m,f,d[n+15],16,530742520),i,m,d[n+2],23,-995338651),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+0],6,-198630844),f,r,d[n+7],10,1126891415),m,f,d[n+14],15,-1416354905),i,m,d[n+5],21,-57434055),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+12],6,1700485571),f,r,d[n+3],10,-1894986606),m,f,d[n+10],15,-1051523),i,m,d[n+1],21,-2054922799),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+8],6,1873313359),f,r,d[n+15],10,-30611744),m,f,d[n+6],15,-1560198380),i,m,d[n+13],21,1309151649),r=md5_ii(r,i=md5_ii(i,m=md5_ii(m,f,r,i,d[n+4],6,-145523070),f,r,d[n+11],10,-1120210379),m,f,d[n+2],15,718787259),i,m,d[n+9],21,-343485551),m=safe_add(m,h),f=safe_add(f,t),r=safe_add(r,g),i=safe_add(i,e)}return Array(m,f,r,i)}function md5_cmn(d,_,m,f,r,i){return safe_add(bit_rol(safe_add(safe_add(_,d),safe_add(f,i)),r),m)}function md5_ff(d,_,m,f,r,i,n){return md5_cmn(_&m|~_&f,d,_,r,i,n)}function md5_gg(d,_,m,f,r,i,n){return md5_cmn(_&f|m&~f,d,_,r,i,n)}function md5_hh(d,_,m,f,r,i,n){return md5_cmn(_^m^f,d,_,r,i,n)}function md5_ii(d,_,m,f,r,i,n){return md5_cmn(m^(_|~f),d,_,r,i,n)}function safe_add(d,_){var m=(65535&d)+(65535&_);return(d>>16)+(_>>16)+(m>>16)<<16|65535&m}function bit_rol(d,_){return d<<_|d>>>32-_}

        BazHelpers.isEmpty = function(obj) {
            return isEmpty(obj);
        }

        BazHelpers.getKeyByValue = function(objectOrArray, value) {
            return getKeyByValue(objectOrArray, value);
        }

        BazHelpers.interval = function(func, intervalLength, options = { }) {
            return interval(func, intervalLength, options);
        }

        BazHelpers.findPath = function(obj, key, array) {
            return findPath(obj, key, array);
        }

        BazHelpers.fetchFromObject = function(obj, key) {
            return fetchFromObject(obj, key);
        }

        BazHelpers.setTimeoutTimers = (function() {
            return setTimeoutTimers();
        })();

        BazHelpers.getTimerId = function(identifier) {
            return getTimerId(identifier);
        }

        BazHelpers.ping = function(source, options, callback) {
            ping(source, options, callback);
        }

        BazHelpers.capitalizeFirstLetter = function(string) {
            return capitalizeFirstLetter(string);
        }

        BazHelpers.stringToBoolean = function(string) {
            return stringToBoolean(string);
        }
    }

    setup(bazHelpersConstructor);

    return bazHelpersConstructor;
}();