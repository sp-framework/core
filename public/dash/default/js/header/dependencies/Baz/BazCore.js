/* exported BazCore */
/* globals PNotify Pace BazContentLoader PNotifyBootstrap4 PNotifyFontAwesome5 PNotifyFontAwesome5Fix PNotifyPaginate PNotifyMobile BazHelpers */
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
        bazContent();
        toolTipsAndPopovers();
        bazUpdateBreadcrumb();
        openMenu();

        if (dataCollection.env.currentRoute.indexOf('auth') === -1) {
            initNotifications();
        }
        // $(document).ready(function() {
        //     getNewToken();
        // });
    }

    // function getNewToken() {
    //     $.post("/getnewtoken", { }, function(response) {
    //         if (response.tokenKey && response.token) {
    //             $("#security-token").attr("name", response.tokenKey);
    //             $("#security-token").val(response.token);
    //         }
    //     }, "json");
    // }
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
        var currentActiveLocation = $('a[href="' + dataCollection.env.rootPath + dataCollection.env.currentRoute + '"].nav-link');

        if (currentActiveLocation.length === 0) {
            if (dataCollection.env['parentComponentId']) {
                currentActiveLocation =
                    $('a[href="' + dataCollection.env.rootPath + dataCollection.env['parentComponentId'].replace(/-/g, '/') + '"]');
            } else {
                currentActiveLocation = $('a[href="' + dataCollection.env.rootPath + '/"]');
            }
        }
        if ($(currentActiveLocation).parents().is('ul.nav-treeview')) {
            $(currentActiveLocation).addClass('active');
            $(currentActiveLocation).parents('ul.nav-treeview').show();
            // $(currentActiveLocation).parents('ul.nav-treeview').siblings('a').addClass('active');
            $(currentActiveLocation).parents('li.has-treeview').addClass('menu-open');
            if ($(currentActiveLocation).parents('ul.nav-treeview').length > 1) {
                $(document).ready(function() {
                    $(currentActiveLocation).parents('.has-treeview').siblings('.has-treeview').children('.nav-treeview').hide();
                });
            } else {
                $(document).ready(function() {
                    $(currentActiveLocation).parent().siblings('.has-treeview').children('.nav-treeview').hide();
                });
            }
        } else {
            $(currentActiveLocation).addClass('active');
        }
    }

    //Notifications
    function getNotifications() {
        var url = dataCollection.env.rootPath + dataCollection.env.appRoute + '/system/notifications/fetchNewNotificationsCount';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();

        $.post(url, postData, function(response) {
            if (response.responseCode == 0 && response.responseData) {
                if (!Number.isInteger(response.responseData.count)) {
                    parseInt(response.responseData.count);
                }

                if (response.responseData.count === window.dataCollection.env.notifications) {
                    return;
                } else if (response.responseData.count < window.dataCollection.env.notifications) {
                    window.dataCollection.env.notifications = response.responseData.count;
                    updateCounter();
                    return;
                }

                window.dataCollection.env.notifications = response.responseData.count;
                updateCounter();

                if (response.responseData.count > 0) {
                    window.dataCollection.env.sounds.notificationSound.play();
                }
                //eslint-disable-next-line
                console.log(response.responseData.count);
            }

            function updateCounter() {
                if (response.responseData.count === 0) {
                    $('#notifications-button-counter').html('');
                } else if (response.responseData.count < 10) {
                    $('#notifications-button-counter').css({'right': '10px'});
                    $('#notifications-button-counter').html(response.responseData.count);
                    shakeBell();
                } else if (response.responseData.count < 99) {
                    $('#notifications-button-counter').css({'right': '5px'});
                    $('#notifications-button-counter').html(response.responseData.count);
                    shakeBell();
                } else if (response.responseData.count > 99) {
                    $('#notifications-button-counter').css({'right': 0});
                    $('#notifications-button-counter').html('99+');
                    shakeBell();
                }
            }

            function shakeBell() {
                $('#notifications-button').addClass('animated tada');

                setTimeout(function() {
                    $('#notifications-button').removeClass('animated tada');
                }, 10000);
            }

            if (response.tokenKey && response.token) {
                $("#security-token").attr("name", response.tokenKey);
                $("#security-token").val(response.token);
            }
        }, 'json');
    }

    function initNotifications() {
        $(document).ready(function() {
            getNotifications();
        });

        BazHelpers.interval(
            async() => {
                BazCore.getNotifications();
            },
            100000
        );
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
        BazCore.getNotifications = function(options) {
            getNotifications(_extends(BazCore.defaults, options));
        }
    }

    setup(bazCoreConstructor);

    return bazCoreConstructor;
}();