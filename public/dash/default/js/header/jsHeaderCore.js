/* exported BazContentLoader */
/* global BazHelpers */
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
                }
                // Close all menu items if root item clicked
                if (!$(this).parents().is('.treeview')) {
                    $('.tree').trigger('closeAllMenu');
                }
                // Close Dropdown
                if ($(this).closest('.dropdown').length > 0) {
                    $(this).closest('.dropdown').dropdown('toggle');
                }

                loadAjax($(this), options); //do the magic

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

                    $('body').trigger('bazContentLoaderAjaxComplete');

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
                    // Trigger Modal Complete
                    $('body').trigger('bazContentLoaderModalComplete');

                    if (xhr.getResponseHeader('tokenKey') && xhr.getResponseHeader('token')) {
                        $('#security-token').attr('name', xhr.getResponseHeader('tokenKey'));
                        $('#security-token').val(xhr.getResponseHeader('token'));
                    }
                }
            });
        }, options.modalLoadDelay);
    }

    function getCurrentRoute(url) {
        var dataCollection = window.dataCollection;
        var uri = url.replace(dataCollection.env.rootPath, '');
        var splitUri = uri.split('/q/');
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
/* exported BazCore */
/* globals PNotify Pace BazContentLoader PNotifyBootstrap4 PNotifyFontAwesome5 PNotifyFontAwesome5Fix PNotifyPaginate PNotifyMobile BazTunnels */
/*
* @title                    : BazCore
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazCore._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazCore = function() {
    var BazCore = void 0;
    var dataCollection;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function bazHeader() {
        //
    }

    //Load Footer - load scripts if not loaded
    function bazFooter(options) {
        dataCollection = window.dataCollection;
        if (dataCollection.env.libsLoaded === false) {
            Pace.restart();
            if (options.loadFooterAt === 'auth') {
                $('.pace-loading-text').attr('hidden', false);
                $($('.pace-loading-text span')[1]).text(' core libs');
                $.ajax({
                    url: dataCollection.env.jsPath + 'footer/jsFooterCore.js',
                    dataType: 'script',
                    async: true,
                    cache: true
                }).done(function() {
                    $($('.pace-loading-text span')[1]).text(' plugins');
                    $.ajax({
                        url: dataCollection.env.jsPath + 'footer/jsFooterPlugins.js',
                        dataType: 'script',
                        async: true,
                        cache: true
                    }).done(function() {
                        $('.pace-loading-text').attr('hidden', true);
                        bazFooterFunctions(_extends(BazCore.defaults, options));
                        dataCollection.env.libsLoaded = true;
                    });
                });
            } else {
                $.ajax({
                    url: dataCollection.env.jsPath + 'footer/jsFooterCore.js',
                    dataType: 'script',
                    async: true,
                    cache: true
                }).done(function() {
                    $.ajax({
                        url: dataCollection.env.jsPath + 'footer/jsFooterPlugins.js',
                        dataType: 'script',
                        async: true,
                        cache: true
                    }).done(function() {
                        $('body').trigger('libsLoadComplete');
                        // BazLTELayout.init({
                        //     fixHeightTask       : 'init'
                        // });
                        // BazLTEPushMenu.init();
                        // BazLTETreeView.init();
                        // BazLTEControlSidebar.init();
                        bazFooterFunctions(_extends(BazCore.defaults, options));
                        dataCollection.env.libsLoaded = true;
                    });
                });
            }
        } else if (dataCollection.env.libsLoaded === true) {
            // BazLTELayout.init({
            //     fixHeightTask       : 'init'
            // });
            // BazLTEPushMenu.init();
            // BazLTETreeView.init();
            // BazLTEControlSidebar.init();
            bazFooterFunctions(_extends(BazCore.defaults, options));
        }
    }
    //Footer
    function bazFooterFunctions() {
        // PNotify, global defaults override
        // $.fn.select2.defaults.set("theme", "bootstrap4");
        PNotify.defaultModules.set(PNotifyBootstrap4, {});
        PNotify.defaultModules.set(PNotifyFontAwesome5, {});
        PNotify.defaultModules.set(PNotifyFontAwesome5Fix, {});
        PNotify.defaultModules.set(PNotifyMobile, {});
        // PNotify.defaultModules.set(PNotifyDesktop, {});
        PNotify.defaultModules.set(PNotifyPaginate, {});
        Pace.options.ajax.trackWebSockets = false;
        bazContent();
        toolTipsAndPopovers();
        bazUpdateBreadcrumb();
        openMenu();

        if (dataCollection.env.currentRoute.indexOf('auth') === -1) {
            BazTunnels.init();
        }
    }

    // Tooltips
    function toolTipsAndPopovers() {
        $('[data-toggle="tooltip"]').tooltip({container:'body'});
        $('[data-toggle="popover"]').popover({container:'body', trigger: 'focus'});
    }

    //Breadcrumb
    function bazUpdateBreadcrumb() {
        //eslint-disable-next-line
        // console.log(dataCollection.env.breadcrumb);
        if (dataCollection.env.breadcrumb) {
            var mainBreadcrumb, titleBreadcrumbArr;
            var breadcrumbArr = dataCollection.env.breadcrumb.split('/');

            //eslint-disable-next-line
            // console.log(breadcrumbArr);
            // if (breadcrumbArr.length > 1) {
                var titleText = breadcrumbArr.pop();
                $('#content-header-breadcrumb ol.breadcrumb').empty();
                titleBreadcrumbArr = [];
                $.each(breadcrumbArr, function(index,path) {
                    titleBreadcrumbArr.push('<li class="breadcrumb-item text-uppercase">' + path + '</li>');
                });
                mainBreadcrumb =
                    '<li class="breadcrumb-item"><i class="fa fa-home" style="position: relative;top: 4px;"></i></li>' +
                    titleBreadcrumbArr.join('');
                $('#content-header-breadcrumb ol.breadcrumb').append(mainBreadcrumb);
                $('#content-header-breadcrumb ol.breadcrumb').append('<li class="breadcrumb-item text-uppercase font-weight-bolder">' + titleText + '</li>');
            // } else {
            //     $('#content-header-breadcrumb ol.breadcrumb').empty().append(
            //         '<li class="breadcrumb-item"><i class="fa fa-home" style="position: relative;top: 4px;"></i></li>' +
            //         '<li class="breadcrumb-item text-uppercase">Home</li>'
            //     );
            // }
        } else {
            $('#content-header-breadcrumb ol.breadcrumb').empty().append(
                '<li class="breadcrumb-item"><i class="fas fa-fw fa-home" style="position: relative;top: 4px;"></i></li>' +
                '<li class="breadcrumb-item text-uppercase">ERROR</li>'
            );
        }
    }

    //Menu
    function openMenu() {

        var currentActiveLocation = $('a[href="/' + dataCollection.env.currentRoute + '"].nav-link');

        if (currentActiveLocation.length === 0) {
            if (dataCollection.env['parentComponentId']) {
                currentActiveLocation =
                    $('a[href="/' + dataCollection.env['parentComponentId'].replace(/-/g, '/') + '"]');
            } else {
                currentActiveLocation = $('a[href="' + dataCollection.env.rootPath + '/"]');
            }
        }

        if ($(currentActiveLocation).parents().is('ul.nav-treeview')) {
            $('#sidebar-menu .nav-link').removeClass('active');
            $(currentActiveLocation).addClass('active');
            $(currentActiveLocation).parents('ul.nav-treeview').show();
            $(currentActiveLocation).parents('ul.nav-treeview').siblings('a').addClass('active');
            $(currentActiveLocation).parents('li.has-treeview').addClass('menu-open');
            if ($(currentActiveLocation).parents('ul.nav-treeview').length > 1) {
                $(document).ready(function() {
                    // $(currentActiveLocation).parents('.has-treeview').siblings('.has-treeview').children('.nav-treeview').hide();
                });
            } else {
                $(document).ready(function() {
                    // $(currentActiveLocation).parent().siblings('.has-treeview').children('.nav-treeview').hide();
                });
            }
        } else {
            $(currentActiveLocation).addClass('active');
        }
    }

    //PageParser
    function bazContent() {
        BazContentLoader.init({
            'ajaxLinkClass'                 : '.contentAjaxLink',
            'ajaxContainer'                 : $("#baz-content"),
            'ajaxBefore'                    : function () {
                                                dataCollection.env['currentComponentId'] = null;
                                                $('#baz-error').attr('hidden', true);
                                                $('#baz-error-content').children().attr('hidden', true);
                                                Pace.restart();
                                                $("#baz-content").empty();
                                                $("#loader").attr('hidden', false);
                                            },
            'ajaxFinished'                  : function () {
                                                bazUpdateBreadcrumb();
                                                toolTipsAndPopovers();
                                                $("#loader").attr('hidden', true);
                                                openMenu();
                                            },
            'ajaxError'                     : function () {
                                                bazUpdateBreadcrumb();
                                                toolTipsAndPopovers();
                                                $("#loader").attr('hidden', true);
                                                $('#baz-error').attr('hidden', false);
                                                $('#baz-error-' + this).attr('hidden', false);
                                            },
            'modalLinkClass'                : '.contentModalLink',
            'modalFinished'                 : function() {
                                                toolTipsAndPopovers();
                                            }
        });
    }

    function bazCoreConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazCoreConstructor) {
        BazCore = BazCoreConstructor;
        BazCore.defaults = {
            loadHeaderAt : null,
            loadFooterAt : null
        };
        BazCore.header = function(options) {
            bazHeader(_extends(BazCore.defaults, options));
        }
        BazCore.footer = function(options) {
            bazFooter(_extends(BazCore.defaults, options));
        }
        BazCore.updateBreadcrumb = function(options) {
            bazUpdateBreadcrumb(_extends(BazCore.defaults, options));
        }
        BazCore.bazContent = function(options) {
            bazContent(_extends(BazCore.defaults, options));
        }
    }

    setup(bazCoreConstructor);

    return bazCoreConstructor;
}();