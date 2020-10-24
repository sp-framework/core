/* exported BazCore */
/* globals PNotify Pace BazContentLoader */
/*
* @title                    : BazCore
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazCore._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazCore = function() {
    'use strict';
    var BazCore = void 0;
    var dataCollection, params;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function bazHeader() {
        if (!window.dataCollection) {
            //For all data collection. Section Data collection, has list of fields, whole section HTML, section fields data
            window.dataCollection = { };
            dataCollection = window.dataCollection;
            dataCollection.env = { };
            //Paths
            dataCollection.env.rootPath = window.location.pathname;
            dataCollection.env.rootPath = window.location.origin + dataCollection.env.rootPath.match(/.*\//g);
            dataCollection.env.jsPath = dataCollection.env.rootPath + 'Tms/Default/js/';
            dataCollection.env.cssPath = dataCollection.env.rootPath + 'Tms/Default/css/';
            dataCollection.env.imagesPath = dataCollection.env.rootPath + 'Tms/Default/images/';
            dataCollection.env.soundPath = dataCollection.env.rootPath + 'Tms/Default/sounds/';
            dataCollection.env.fontsPath = dataCollection.env.rootPath + 'Tms/Default/fonts/';
            dataCollection.env.libsLoaded = false;
            //Parameters
            params = new URLSearchParams(window.location.search.substring(1));
            dataCollection.env.currentRoute = params.get("route");
            dataCollection.env.currentId = params.get("id");
        }

        if (localStorage.getItem('bazDevMode') === null) {
            localStorage.setItem('bazDevMode', null);
        }
    }

    //Load Footer - load scripts if not loaded
    function bazFooter(options) {
        dataCollection = window.dataCollection;
        if (dataCollection.env.libsLoaded === false) {
            Pace.restart();
            if (options.loadFooterAt === 'login') {
                $('.pace-loading-text').attr('hidden', false);
                $($('.pace-loading-text span')[1]).text(' core libs');
                $.ajax({
                    url: window.dataCollection.env.jsPath + 'footer/jsFooterCore.js',
                    dataType: 'script',
                    async: true,
                    cache: true
                }).done(function() {
                    $($('.pace-loading-text span')[1]).text(' plugins');
                    $.ajax({
                        url: window.dataCollection.env.jsPath + 'footer/jsFooterPlugins.js',
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
                    url: window.dataCollection.env.jsPath + 'footer/jsFooterCore.js',
                    dataType: 'script',
                    async: true,
                    cache: true
                }).done(function() {
                    $.ajax({
                        url: window.dataCollection.env.jsPath + 'footer/jsFooterPlugins.js',
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
        PNotify.defaults.styling = 'bootstrap4';
        PNotify.defaults.icons = 'fontawesome5';
        bazContent();
        toolTipsAndPopovers();
        bazUpdateBreadcrumb();
        openMenu();
    }

    // Tooltips
    function toolTipsAndPopovers() {
        $('[data-toggle="tooltip"]').tooltip({container:'body'});
        $('[data-toggle="popover"]').popover({container:'body', trigger: 'focus'});
    }

    //Breadcrumb
    function bazUpdateBreadcrumb() {
        if (window.dataCollection.env['currentComponentId']) {
            var mainBreadcrumb, titleComponentArr;
            var componentArr = window.dataCollection.env['currentComponentId'].split('-');

            if (componentArr.length > 1) {
                var titleText = componentArr.pop();
                $('#content-header-breadcrumb ol.breadcrumb').empty();
                titleComponentArr = [];
                $.each(componentArr, function(index,component) {
                    titleComponentArr.push('<li class="breadcrumb-item text-uppercase">' + component + '</li>');
                });
                mainBreadcrumb = '<li class="breadcrumb-item"><i class="fa fa-home"></i></li>' + titleComponentArr.join('');
                $('#content-header-breadcrumb ol.breadcrumb').append(mainBreadcrumb);
                $('#content-header-breadcrumb ol.breadcrumb').append('<li class="breadcrumb-item text-uppercase font-weight-bolder">' + titleText + '</li>');
            } else {
                $('#content-header-breadcrumb ol.breadcrumb').empty().append('<li class="breadcrumb-item"><i class="fas fa-fw fa-home"></i></li>');
            }
        } else {
            $('#content-header-breadcrumb ol.breadcrumb').empty().append(
                '<li class="breadcrumb-item"><i class="fas fa-fw fa-home"></i></li>' +
                '<li class="breadcrumb-item text-uppercase">ERROR</li>'
            );
        }
    }

    function openMenu() {
        var currentActiveLocation = $('a[href="' + dataCollection.env.rootPath + 'index.php?route=' + dataCollection.env.currentRoute + '"]');

        if (currentActiveLocation.length === 0) {
            if (dataCollection.env['parentComponentId']) {
                currentActiveLocation =
                    $('a[href="' + dataCollection.env.rootPath + 'index.php?route=' +
                        dataCollection.env['parentComponentId'].replace(/-/g, '/') + '/view"]');
            } else {
                currentActiveLocation = $('a[href="' + dataCollection.env.rootPath + 'index.php?route=home/view"]');
            }
        }
        if ($(currentActiveLocation).parents().is('.nav-treeview')) {
            $(currentActiveLocation).addClass('active');
            $(currentActiveLocation).parents('.nav-treeview').show();
            $(currentActiveLocation).parents('.nav-treeview').siblings('a').addClass('active');
            $(currentActiveLocation).parents('.has-treeview').addClass('menu-open');
        }
    }

    //PageParser
    function bazContent() {
        BazContentLoader.init({
            ajaxLinkClass                   : '.contentAjaxLink',
            ajaxContainer                   : $("#baz-content"),
            ajaxBefore                      : function () {
                                                window.dataCollection.env['currentComponentId'] = null;
                                                $('#baz-error').attr('hidden', true);
                                                $('#baz-error-content').children().attr('hidden', true);
                                                Pace.restart();
                                                $("#baz-content").empty();
                                                $("#loader").attr('hidden', false);
                                            },
            ajaxFinished                    : function () {
                                                bazUpdateBreadcrumb();
                                                toolTipsAndPopovers();
                                                $("#loader").attr('hidden', true);
                                            },
            ajaxError                       : function () {
                                                bazUpdateBreadcrumb();
                                                toolTipsAndPopovers();
                                                $("#loader").attr('hidden', true);
                                                $('#baz-error').attr('hidden', false);
                                                $('#baz-error-' + this).attr('hidden', false);
                                            },
            modalLinkClass                  : '.contentModalLink',
            modalFinished                   : function() {
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