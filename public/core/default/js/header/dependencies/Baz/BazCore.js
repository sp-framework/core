/* exported BazCore */
/* globals PNotify Pace BazContentLoader PNotifyBootstrap4 PNotifyFontAwesome5 PNotifyFontAwesome5Fix PNotifyPaginate PNotifyMobile BazTunnels BazHelpers */
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
    var dataCollection, timerId, timeout;

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
        var url;
        dataCollection = window.dataCollection;
        if (dataCollection.env.libsLoaded === false) {
            Pace.restart();
            if (options.loadFooterAt === 'auth') {
                $('.pace-loading-text').attr('hidden', false);
                $($('.pace-loading-text span')[1]).text(' core libs');
                if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                    url = dataCollection.env.jsPath + 'footer/jsFooterCore.js';
                } else {
                    url = dataCollection.env.jsPath + 'footer/jsFooterCore.js?ver=' + dataCollection.env.coreVer;
                }
                $.ajax({
                    url: url,
                    dataType: 'script',
                    async: true,
                    cache: true
                }).done(function() {
                    $($('.pace-loading-text span')[1]).text(' plugins');
                    if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                        url = dataCollection.env.jsPath + 'footer/jsFooterPlugins.js';
                    } else {
                        url = dataCollection.env.jsPath + 'footer/jsFooterPlugins.js?ver=' + dataCollection.env.coreVer;
                    }
                    $.ajax({
                        url: url,
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
                if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                    url = dataCollection.env.jsPath + 'footer/jsFooterCore.js';
                } else {
                    url = dataCollection.env.jsPath + 'footer/jsFooterCore.js?ver=' + dataCollection.env.coreVer;
                }
                $.ajax({
                    url: url,
                    dataType: 'script',
                    async: true,
                    cache: true
                }).done(function() {
                    if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                        url = dataCollection.env.jsPath + 'footer/jsFooterPlugins.js';
                    } else {
                        url = dataCollection.env.jsPath + 'footer/jsFooterPlugins.js?ver=' + dataCollection.env.coreVer;
                    }
                    $.ajax({
                        url: url,
                        dataType: 'script',
                        async: true,
                        cache: true
                    }).done(function() {
                        $('body').trigger('libsLoadComplete');
                        bazFooterFunctions(_extends(BazCore.defaults, options));
                        dataCollection.env.libsLoaded = true;
                    });
                });
            }
        } else if (dataCollection.env.libsLoaded === true) {
            bazFooterFunctions(_extends(BazCore.defaults, options));
        }
    }
    //Footer
    function bazFooterFunctions() {
        PNotify.defaultModules.set(PNotifyBootstrap4, {});
        PNotify.defaultModules.set(PNotifyFontAwesome5, {});
        PNotify.defaultModules.set(PNotifyFontAwesome5Fix, {});
        PNotify.defaultModules.set(PNotifyMobile, {});
        PNotify.defaultModules.set(PNotifyPaginate, {});
        Pace.options.ajax.trackWebSockets = false;
        bazContent();
        toolTipsAndPopovers();
        bazUpdateBreadcrumb();
        initResizeElement();
        if ($('.navbar-content').length === 0) {
            openMenu();
        }

        if (dataCollection.env.currentRoute.indexOf('auth') === -1) {
            BazTunnels.init();
        }
        initPings();
    }

    //10 mins get update of site status. Note this is not PING, but webserver responsive time to reply with favicon.
    function initPings() {
        BazHelpers.ping(dataCollection.env.httpScheme + '://' + dataCollection.env.httpHost, {}, function(err, data) {
            timerId = BazHelpers.getTimerId('ping');

            if (!err && data) {
                if (data <= 100) {
                    $('.connectivity-icon').removeClass(function (index, className) {
                        return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                    }).addClass('text-success');
                    $('.connectivity').attr('title', 'Connectivity Status: Good (' + data + ' ms)').tooltip('_fixTitle');
                } else if (data > 100 && data <= 200) {
                    $('.connectivity-icon').removeClass(function (index, className) {
                        return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                    }).addClass('text-warning');
                    $('.connectivity').attr('title', 'Connectivity Status: Average (' + data + ' ms)').tooltip('_fixTitle');
                } else if (data > 200) {
                    $('.connectivity-icon').removeClass(function (index, className) {
                        return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                    }).addClass('text-danger');
                    $('.connectivity').attr('title', 'Connectivity Status: Bad (' + data + ' ms)').tooltip('_fixTitle');
                }
                if (timerId) {
                    BazHelpers.setTimeoutTimers.stop(timerId, null, 'ping');
                }

                if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                    timeout = 600000;
                } else {
                    timeout = 60000;
                }

                BazHelpers.setTimeoutTimers.add(function() {
                    initPings();
                }, timeout, null, 'ping');
            } else {
                //Connection is Dead
                $('.connectivity-icon').removeClass(function (index, className) {
                    return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                }).addClass('text-secondary');
                if (timerId) {
                    BazHelpers.setTimeoutTimers.stop(timerId, null, 'ping');
                }

                if (dataCollection.env.devmode && dataCollection.env.devmode == '1') {
                    timeout = 900000;
                } else {
                    timeout = 300000;
                }

                BazHelpers.setTimeoutTimers.add(function() {
                    initPings();
                }, timeout, null, 'ping');
            }
        });
    }

    // Tooltips
    function toolTipsAndPopovers() {
        $('[data-toggle="tooltip"]').tooltip({container:'body'});
        $('[data-toggle="popover"]').popover({container:'body', trigger: 'focus'});
    }

    var element = null;
    var startX, startWidth, parentWidth;
    // var startY, startHeight;

    function initResizeElement() {
        var vdivide = document.getElementsByClassName("vdivide-resizable");

        if (vdivide.length > 0) {

            for (var i = 0; i < vdivide.length; i++) {
                var p = vdivide[i];

                var right = document.createElement("div");
                right.className = "resizer-right";
                p.appendChild(right);
                right.addEventListener("mousedown", initDrag, false);
                right.parentPopup = p;

                var bottom = document.createElement("div");
                bottom.className = "resizer-bottom";
                p.appendChild(bottom);
                bottom.addEventListener("mousedown", initDrag, false);
                bottom.parentPopup = p;

                var both = document.createElement("div");
                both.className = "resizer-both";
                p.appendChild(both);
                both.addEventListener("mousedown", initDrag, false);
                both.parentPopup = p;
            }
        }
    }

    function initDrag(e) {
        element = this.parentPopup;

        startX = e.clientX;
        // startY = e.clientY;
        startWidth = parseInt(
                              document.defaultView.getComputedStyle(element).width,
                              10
                              );
        // startHeight = parseInt(
        //                        document.defaultView.getComputedStyle(element).height,
        //                        10
        //                        );
        parentWidth = parseInt(
                               document.defaultView.getComputedStyle(element.parentNode).width,
                               10
                               );
        document.documentElement.addEventListener("mousemove", doDrag, false);
        document.documentElement.addEventListener("mouseup", stopDrag, false);
    }

    function doDrag(e) {
        element.style.width = startWidth + e.clientX - startX + "px";
        element.style.maxWidth = startWidth + e.clientX - startX + "px";
        // element.style.height = startHeight + e.clientY - startY + "px";
        for (let sibling of element.parentNode.children) {
            if (sibling !== element) {
                sibling.style.maxWidth = parentWidth - (startWidth + e.clientX - startX) + "px";
                sibling.style.width = parentWidth - (startWidth + e.clientX - startX) + "px";
            }
        }
    }

    function stopDrag() {
        document.documentElement.removeEventListener("mousemove", doDrag, false);
        document.documentElement.removeEventListener("mouseup", stopDrag, false);
    }

    //Breadcrumb
    function bazUpdateBreadcrumb() {
        if (dataCollection.env.breadcrumb) {
            var mainBreadcrumb, titleBreadcrumbArr;
            var breadcrumbArr = dataCollection.env.breadcrumb.split('/');

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
        } else {
            $('#content-header-breadcrumb ol.breadcrumb').empty().append(
                '<li class="breadcrumb-item"><i class="fas fa-fw fa-home" style="position: relative;top: 4px;"></i></li>' +
                '<li class="breadcrumb-item text-uppercase">ERROR</li>'
            );
        }
    }

    //Menu
    function openMenu() {
        var currentActiveLocation = $('a[href="' + dataCollection.env.currentRoute + '"].nav-link');

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
                                                if ($('.navbar-content').length === 0) {
                                                    openMenu();
                                                }
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
        BazCore.initResizeElement = function() {
            initResizeElement();
        }
    }

    setup(bazCoreConstructor);

    return bazCoreConstructor;
}();