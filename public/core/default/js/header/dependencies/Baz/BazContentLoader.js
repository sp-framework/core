/* exported BazContentLoader */
/* global BazHelpers Swal BazCore */
/*
* @title                    : BazContentLoader
* @description              : Make Ajax Calls and parse content
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentLoader._function_(_options_);
* @functions                :
* @options                  :
*/
var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };

// eslint-disable-next-line no-unused-vars
var BazContentLoader = function() {
    var BazContentLoader = void 0;
    var copyDataCollection;

    function init(options) {
        var ajaxElements, loadCount, shouldCheckCount, modalElements;
        // AJAX
        ajaxElements = $('body').find(options.ajaxLinkClass);
        ajaxElements.each(function () {
            $(this).attr('data-bclaid', '').off();
        });
        loadCount = 0; //make sure to not load if the element is initialy loaded via the popstate call
        shouldCheckCount = false; //if it should check the loadCount to avoid the first popstate call

        ajaxElements.each(function (count) {
            $(this).attr('data-bclaid', count + 1); //set unique id for the popstate
            $(this).off(options.ajaxTrigger);
            $(this).on(options.ajaxTrigger, function (e) {
                e.preventDefault();

                if ($(this).parents().is('.sidebar')) {
                    $(this).parents('.sidebar').find('a.active').removeClass('active');
                    $(this).addClass('active');
                    $(this).parents('.nav-treeview').siblings('a.nav-link').addClass('active');
                    $('[data-widget="pushmenu"]').PushMenu('collapse');

                    // Close all menu items if root item clicked
                    if (!$(this).parents().is('.treeview')) {
                        $('.tree').trigger('closeAllMenu');
                    }
                    // Close Dropdown
                    if ($(this).closest('.dropdown').length > 0) {
                        $(this).closest('.dropdown').dropdown('toggle');
                    }
                }

                if (window['dataCollection']['env']['wizard'] === true) {
                    var swalSound = window.dataCollection.env.sounds.swalSound;
                    Swal.fire({
                        title                       : '<span class="text-warning"> Quit current wizard?</span>',
                        icon                        : 'question',
                        background                  : 'rgba(0,0,0,.8)',
                        backdrop                    : 'rgba(0,0,0,.6)',
                        buttonsStyling              : false,
                        confirmButtonText           : 'Yes',
                        customClass                 : {
                            'confirmButton'             : 'btn btn-warning text-uppercase',
                            'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                        },
                        showCancelButton            : true,
                        keydownListenerCapture      : true,
                        allowOutsideClick           : true,
                        allowEscapeKey              : true,
                        didOpen                     : function() {
                            swalSound.play();
                        }
                    }).then((result) => {
                        //eslint-disable-next-line
                        console.log(result);
                        if (result.value) {
                            loadAjax($(this), options); //do the magic
                        }
                    });
                } else {
                    loadAjax($(this), options); //do the magic
                }

                return false; //also return false
            });
        });

        $(window).on("popstate", function () {
            location.reload(true);
            //if its a pushed state we dynamicly load the previous page
            if (history.state) {
                if (shouldCheckCount && loadCount != 0) {
                    if ($("*[data-bclaid=" + history.state.id + "]").length > 0) { //if exists then load
                        loadAjax($("*[data-bclaid=" + history.state.id + "]"), options, true);
                    }
                } else {
                    loadCount = 1;
                }
            }
        });

        //MODAL
        modalElements = $('body').find(options.modalLinkClass);
        modalElements.each(function () {
            $(this).attr('data-bclmid', '').off();
        });

        if (modalElements.length > 0 && $('#contentModalLink-modal').length === 0) {
            BazHelpers.modal({
                'modalId'                               : options.modalId,
                'modalSize'                             : options.modalSize,
                'modalWidth'                            : '',
                'modalAdditionalClasses'                : options.modalAdditionalClasses,
                'modalBackdrop'                         : options.modalBackdrop,
                'modalEscClose'                         : options.modalEscClose,
                'modalHeader'                           : options.modalHeader,
                'modalBodyAdditionalClasses'            : options.modalBodyAdditionalClasses,
                'modalFooter'                           : options.modalFooter,
                'modalAppendOn'                         : options.modalAppendOn,
                'modalTitle'                            : options.modalTitle,
                'modalType'                             : options.modalType
            });
            $('#contentModalLink-modal .modal-body').append(
                '<div class="row m-2 text-center" id="contentModalLink-modal-loader" hidden>' +
                '    <div class="col">' +
                '        <div class="fa-2x">' +
                '            <i class="fa fa-cog fa-spin"></i>' +
                '        </div>' +
                '    </div>' +
                '</div>'
            );
        }

        modalElements.each(function (count) {
            $(this).attr('data-bclmid', count + 1); //set unique id for the popstate
            $(this).on(options.modalTrigger, function (e) {
                e.preventDefault(); //prevent default  behaviour

                //Show the modal on success
                $('#contentModalLink-modal-loader').attr('hidden', false);
                $('#contentModalLink-modal').modal('show');

                options.modalContainer = '#contentModalLink-modal .modal-body';
                loadModal($(this), options); //do the magic

                return false; //also return false
            });
        });

        $('body').trigger('bazContentLoaderInitComplete');
    }

    function loadAjax(element, options, popped) {
        if (element.length === 0) {
            return false;
        }

        BazHelpers.setTimeoutTimers.stopAll();//Stop all timers from previous component.
        var urlToLoad, elementId;
        var dataCollection = window.dataCollection;
        // Delete old Content Objects
        for (var object in dataCollection) {
            if (object !== 'env') {
                for (var objects in dataCollection[object]) {
                    if (dataCollection[object][objects]['datatable']) {
                        dataCollection[object][objects]['datatable'].destroy();
                    }
                }

                delete dataCollection[object];
            }
        }

        if (element) {
            // Check if the element is inside modal
            if (element.parents().is('.modal')) {
                var elementModalId = element.parents('.modal')[0].id;
                $('#' + elementModalId).modal('hide').modal('dispose').remove();//Lets Close the modal on cancel or add/edit button.
                if (element.text().trim() === 'CANCEL' || element.text().trim() === 'CLOSE' || element.text().trim() === 'QUIT') {
                    window.dataCollection = Object.assign({}, copyDataCollection);
                    copyDataCollection = { };
                    init(options);
                    dataCollection.env.currentRoute = getCurrentRoute(element.attr(options.ajaxUrlAttribute));
                    dataCollection.env.currentId = null;
                    return;
                }
            }

            // Adding id="?" to dataCollection
            // if (element[0].tagName === 'A') {
            //     params = new URLSearchParams(element[0].search.substring(1));
            //     if (params.get("id")) {
            //         dataCollection.env.currentId = params.get("id");
            //     } else {
            //         dataCollection.env.currentId = null;
            //     }
            // }
            options.ajaxBefore.call(element); //call the 'ajaxBefore' callback
            urlToLoad = element.attr(options.ajaxUrlAttribute);
            elementId = element.attr('data-bclaid');

            if (options.ajaxParseElement != null) {
                urlToLoad += " " + options.ajaxParseElement; //append the
            }
        } else {
            options.ajaxBefore.call(); //call the 'ajaxBefore' callback
            urlToLoad = options.ajaxLoadLink;
            elementId = '999';
        }

        setTimeout(function () {
            $(options.ajaxContainer).load(urlToLoad, options.ajaxLoadLinkParams, function (response, status, xhr) {
                if (xhr.getResponseHeader('NEED_AUTH') === '1') {
                    response = null;
                    window.location = xhr.getResponseHeader('REDIRECT_URL');
                } else {
                    dataCollection.env.currentRoute = getCurrentRoute(urlToLoad);

                    if (xhr.getResponseHeader('currentid')) {
                        dataCollection.env.currentId  = xhr.getResponseHeader('currentid');
                    } else {
                        dataCollection.env.currentId  = null;
                    }
                    // console.log(xhr.getAllResponseHeaders()); //trying to get page last edit for storing data locally
                    if (options.ajaxSetTitle) {
                        var titlePart = response.split("title>"); //dirty little trick to get an html element
                        titlePart = titlePart[1].split("</"); //since the <title> element is always the same, this is possible
                        var title = titlePart[0];
                        document.title = title; //set the title
                    }

                    if (options.ajaxDynamicUrl && popped != true) {
                        var state = { name: urlToLoad, page: title, id: elementId };

                        history.pushState(state, title, urlToLoad); //change url to the one provided
                    }
                    if (status == "success") {
                        // BAZ Template Not Found
                        var template = /^Error: can't load template.*$/;
                        if (template.test(response)) {
                            $(options.ajaxContainer).empty();
                            options.ajaxError.call('templateError');
                        }
                        dataCollection.env.breadcrumb = xhr.getResponseHeader('breadcrumb');
                        if (element) {
                            options.ajaxFinished.call(element); //call the 'finished' callback
                        } else {
                            options.ajaxFinished.call();
                        }

                    } else if (status == "error" || status == "timeout" || status == "parseerror") {
                        options.ajaxError.call(xhr.status); //call the 'error' callback, 'this' = xhr.status
                    }
                    // Reset counter after page load complete to accommodate new links (if any)
                    init(options);

                    if (xhr.getResponseHeader('tokenKey') && xhr.getResponseHeader('token')) {
                        $('#security-token').attr('name', xhr.getResponseHeader('tokenKey'));
                        $('#security-token').val(xhr.getResponseHeader('token'));
                    }
                }

                if ($('.component').length > 0) {
                    if (dataCollection[$('.component')[0].id]) {
                        var timers = BazHelpers.setTimeoutTimers.all();

                        if (timers.length > 0) {
                            $(timers).each(function(i, timer) {
                                if (timer['dataCollectionObj']) {
                                    if (timer['dataCollectionObj'] !== $('.component')[0].id) {
                                        BazHelpers.setTimeoutTimers.stop(timer['id']);
                                    }
                                }
                            });
                        }
                    }
                }
                $('.tooltip').remove();
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover('enable');
                if ($('.sectionWithWizard').length > 0) {
                    window['dataCollection']['env']['wizard'] = true;
                } else {
                    window['dataCollection']['env']['wizard'] = false;
                }
                BazCore.initResizeElement();

                $('body').trigger('bazContentLoaderAjaxComplete');
            });
        }, options.ajaxLoadDelay);
    }

    function loadModal(element, options) {
        var urlToLoad;
        var dataCollection = window.dataCollection;
        copyDataCollection = Object.assign({}, window.dataCollection);
        // Delete old Content Objects
        for (var object in dataCollection) {
            if (object !== 'env') {
                delete dataCollection[object];
            }
        }
        if (element) {
            // Adding id="?" to dataCollection
            // if (element[0].tagName === 'A') {
            //     params = new URLSearchParams(element[0].search.substring(1));
            //     if (params.get("id")) {
            //         dataCollection.env.currentId = params.get("id");
            //     } else {
            //         dataCollection.env.currentId = null;
            //     }
            // }
            options.modalBefore.call(element); //call the 'ajaxBefore' callback
            urlToLoad = element.attr(options.modalUrlAttribute);

            if (options.modalParseElement != null) {
                urlToLoad += " " + options.modalParseElement; //append the
            }
        } else {
            options.modalBefore.call(); //call the 'ajaxBefore' callback
            urlToLoad = options.modalLoadLink;
        }

        setTimeout(function () {
            $(options.modalContainer).load(urlToLoad, options.modalLoadLinkParams, function (response, status, xhr) {
                if (xhr.getResponseHeader('NEED_AUTH') === '1') {
                    window.location = xhr.getResponseHeader('REDIRECT_URL');
                } else {
                    dataCollection.env.currentRoute = getCurrentRoute(urlToLoad);

                    if (xhr.getResponseHeader('currentid')) {
                        dataCollection.env.currentId  = xhr.getResponseHeader('currentid');
                    } else {
                        dataCollection.env.currentId  = null;
                    }

                    if (status == "success") {
                        // BAZ Template Not Found
                        var template = /^Error: can't load template.*$/;
                        if (template.test(response)) {
                            $(options.modalContainer).html(($('#baz-error-templateError').html()));
                            $(options.modalContainer).removeClass('p-0');
                            options.modalError.call('templateError');
                        }
                        dataCollection.env.breadcrumb = xhr.getResponseHeader('breadcrumb');
                        if (element) {
                            options.modalFinished.call(element); //call the 'finished' callback
                        } else {
                            options.modalFinished.call();
                        }

                    } else if (status == "error" || status == "timeout" || status == "parseerror") {
                        $(options.modalContainer).html(($('#baz-error-' + xhr.status).html()));
                        $(options.modalContainer).removeClass('p-0');
                        options.modalError.call(xhr.status); //call the 'error' callback, 'this' = xhr.status
                    }
                    // Reset counter after page load complete to accommodate new links (if any)
                    init(options);

                    if (xhr.getResponseHeader('tokenKey') && xhr.getResponseHeader('token')) {
                        $('#security-token').attr('name', xhr.getResponseHeader('tokenKey'));
                        $('#security-token').val(xhr.getResponseHeader('token'));
                    }
                }
                $('.tooltip').remove();
                $('[data-toggle="tooltip"]').tooltip();
                $('[data-toggle="popover"]').popover('enable');
                BazCore.initResizeElement();
                // Trigger Modal Complete
                $('body').trigger('bazContentLoaderModalComplete');
            });
        }, options.modalLoadDelay);
    }

    function getCurrentRoute(url) {
        var dataCollection = window.dataCollection;
        var uri = url.replace(dataCollection.env.rootPath, '');
        var splitUri = uri.split('/q/');
        if (splitUri[1]) {
            dataCollection.env.currentQuery = splitUri[1];
        } else {
            dataCollection.env.currentQuery = '';
        }
        return splitUri[0];
    }

    function bazContentLoaderConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(bazContentLoaderConstructor) {
        BazContentLoader = bazContentLoaderConstructor;
        BazContentLoader.Defaults = {
            ajaxLinkClass               : null, //main link class ".maincontentlink"
            ajaxContainer               : null, //the container for the data to be displayed in, cannot be null - required
            ajaxDynamicUrl              : true, //if false, the plugin doesn't update the URL to match the loaded page
            ajaxInitialElement          : null, //the element that is active / the page is loaded from on initial load. Needed to go back to the first page in with popstate. Only usefull if you use dynamic urls
            ajaxParseElement            : null, //the element on the page you want to implement, if empty it loads the whole page
            ajaxSetTitle                : false, //set the title of the page to the one you are loading
            ajaxTrigger                 : 'click', //when to trigger the loading, default is on click
            ajaxUrlAttribute            : 'href', //the attribute to be checked for the url, default is href (for a tags)
            ajaxLoadDelay               : null, //sometimes nice for animation
            ajaxLoadLink                : null,
            ajaxLoadLinkParams          : null,
            ajaxBefore                  : function () { }, //the callback that gets called ajaxBefore loading, say for displaying a loader. 'this' returns the clicked button
            ajaxFinished                : function () { }, //the callback that gets called after everything is loaded, say to hide the loader, toggle button state.  'this' returns the clicked button
            ajaxError                   : function () { }, //the callback that gets called after an error, do custom error handling here. Returns the xhr status.

            modalLinkClass              : null, //modal link class ".modallink"
            modalContainer              : null, //the container for the data to be displayed in, cannot be null - required
            modalParseElement           : null, //the element on the page you want to implement, if empty it loads the whole page
            modalTrigger                : 'click', //when to trigger the loading, default is on click
            modalUrlAttribute           : 'href', //the attribute to be checked for the url, default is href (for a tags)
            modalLoadDelay              : null, //sometimes nice for animation
            modalLoadLink               : null,
            modalLoadLinkParams         : null,
            modalBefore                 : function () { }, //the callback that gets called ajaxBefore loading, say for displaying a loader. 'this' returns the clicked button
            modalFinished               : function () { }, //the callback that gets called after everything is loaded, say to hide the loader, toggle button state.  'this' returns the clicked button
            modalError                  : function () { }, //the callback that gets called after an error, do custom error handling here. Returns the xhr status.
            modalId                     : 'contentModalLink',
            modalSize                   : 'lg',
            modalAdditionalClasses      : '',
            modalBackdrop               : 'static',
            modalEscClose               : 'false',
            modalHeader                 : false,
            modalBodyAdditionalClasses  : 'p-0',
            modalFooter                 : false,
            modalAppendOn               : 'body'
        }
        BazContentLoader.init = function(options) {
            init(_extends(BazContentLoader.Defaults, options));
        }
        BazContentLoader.loadAjax = function(element, options) {
            loadAjax(element, _extends(BazContentLoader.Defaults, options));
        }
        BazContentLoader.loadModal = function(options) {
            loadModal(_extends(BazContentLoader.Defaults, options));
        }
    }

    setup(bazContentLoaderConstructor);

    return bazContentLoaderConstructor;
}();