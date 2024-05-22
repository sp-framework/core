/* globals define exports PNotify paginatedPNotify BazHelpers */
/*
* @title                    : BazContentDevModeTools
* @description              : Baz Lib for devmode tools for sections/content
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ section/componentID).BazContentDevModeTools;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {

    var BazContentDevModeTools = function ($) {

        var NAME                    = 'BazContentDevModeTools';
        var DATA_KEY                = 'baz.devmodetools';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
        };
        var dataCollection = window['dataCollection'];
        var pnotifySound = dataCollection.env.sounds.pnotifySound;
        var sectionId,
            componentId;

        var BazContentDevModeTools = function () {
            function BazContentDevModeTools(element, settings) {
                this._element = element;
                this._settings = $.extend({}, Default, settings);

                this._init(this._settings);
                this._initSectionWithFormDevModeTools(this._settings);
            }

            var _proto = BazContentDevModeTools.prototype;

            _proto._error = function(message) {
                throw new Error(message);
            };

            _proto._init = function _init() {
                sectionId = $(this._element)[0].id;
                componentId = $(this._element).parents('.component')[0].id;
            };

            _proto._initSectionWithFormDevModeTools = function _initSectionWithFormDevModeTools() {
                if ($('#' + sectionId).parents().is('#contentModalLink-modal')) {//show cancel button if in modal
                    $('#' + sectionId + '-action-buttons button').each(function() {
                        if (!$('#' + $(this)[0].id).is('#' + sectionId + '-cancel')) {
                            $(this).addClass('d-none');
                        }
                    });
                } else {
                    $('#' + sectionId + '-action-buttons').addClass('d-none');//else hide all action buttons
                }
                $('#' + sectionId).find('[data-bazscantype]').each(function(i,v) {
                    var devModeToolsCheckbox =
                        '<div class="icheck-info">' +
                            '<input class="devModeTools" type="checkbox" id="' + $(v)[0].id + '-devmodetools-value-checkbox"/>' +
                            '<label for="' + $(v)[0].id + '-devmodetools-value-checkbox">Include field data</label>' +
                        '</div>'
                    $(v).parent('.form-group').removeClass('d-none');
                    $(v).attr('disabled', false);
                    $(v).data('bazdevpost', true);
                    if ($(v).data('bazscantype') === 'select2') {
                        var parent = $(v).parent('.form-group');
                        var id = $(v)[0].id;
                        var placeholder = $(v).siblings('label').text();
                        // var placeholder = $(v).siblings('.select2').find('.select2-selection__placeholder').text();
                        // $(v).select2('destroy');
                        $(v).remove();
                        $(parent).prepend(devModeToolsCheckbox);
                        $(parent).append(
                            '<input id="' + id + '" name="' + id + '" placeholder="' + placeholder + '"' +
                            'data-bazpostoncreate="true" data-bazdevpost="true" data-bazpostonupdate="true" data-bazscantype="input" type="text" class="' +
                            'form-control form-control-sm rounded-0"/>'
                            );
                    }
                    if ($(v).data('bazscantype') === 'radio' || $(v).data('bazscantype') === 'checkbox')  {
                        $(v).prepend(devModeToolsCheckbox);
                    } else {
                        $(v).parents('.form-group').prepend(devModeToolsCheckbox);
                    }
                });
                $('#' + sectionId + ' .card-footer').append(
                    '<div class="row">' +
                        '<div class="col" id="' + sectionId + '-devmodetools-test-tools">' +
                            '<h4 class="text-uppercase">Dev Test Tools</h4>' +
                        '</div>' +
                    '</div>'
                );
                $('#' + sectionId + '-devmodetools-test-tools').append(
                    '<div class="row"><div class="col">' +
                    '<div class="form-group">' +
                        '<label class="text-uppercase">Testing Route</label> ' +
                        '<i data-toggle="tooltip" data-html="true" data-placement="top"' +
                        ' title="" class="fa fa-fw fa-question-circle fa-1 helper " data-original-title="' +
                        'Enter testing route">' +
                        '</i>' +
                        '<sup><i data-toggle="tooltip" data-html="true" data-placement="top"' +
                        ' title="" style="font-size: 7px;" class="fa fa-fw fa-star fa-1 helper text-danger" ' +
                        'data-original-title="Required"></i></sup>' +
                        '<input type="text" class="form-control form-control-sm rounded-0" id="' + sectionId + '-devmodetools-route" name="' + sectionId +
                        '-devmodetools-route" placeholder="TESTING ROUTE">' +
                    '</div>' +
                    '<button type="button" class="btn bg-orange m-1" id="' + sectionId + '-devmodetools-get-button">' +
                        '<span class="text-uppercase">Test Get</span>' +
                    '</button>' +
                    '<button type="button" class="btn bg-purple m-1" id="' + sectionId + '-devmodetools-post-button">' +
                        '<span class="text-uppercase">Test Post</span>' +
                    '</button>' +
                    '</div></div><hr>' +
                    '<div class="row"><div class="col">' +
                    '<button type="button" class="btn bg-info m-1" id="' + sectionId + '-devmodetools-component-info">' +
                        '<span class="text-uppercase">Component Info</span>' +
                    '</button><br>' +
                    '<span class="text-danger">NOTE: Running Dev Mode. All fields are enabled and visible.</span><br>' +
                    '<span class="text-info">HOW-TO (Test): <br>' +
                    '1) Click on COMPONENT INFO button to get details regarding this Component, it\'s packages and Input to provide and output to expect during tests.<br>' +
                    '2) Enter route parameter example: account/view OR account/edit. Route information is available in COMPONENT INFO.<br>' +
                    '3) Select checkboxes that are before the fields to sent that fields data to the above given route.<br>' +
                    '4) Click "TEST GET" to test get method and "TEST POST" to test post method' +
                    '</span>' +
                    '</div>' +
                    '</div>'
                );
                $('#' + sectionId + '-devmodetools-get-button').click(function(e) {
                    e.preventDefault();
                    componentId = $(this).parents('.component')[0].id;
                    sectionId = $(this).parents('.sectionWithForm')[0].id;
                    doAjax('get', componentId, sectionId);
                });
                $('#' + sectionId + '-devmodetools-post-button').click(function(e) {
                    e.preventDefault();
                    componentId = $(this).parents('.component')[0].id;
                    sectionId = $(this).parents('.sectionWithForm')[0].id;
                    doAjax('post', componentId, sectionId);
                });
                $('#' + sectionId + '-devmodetools-component-info').click(function() {
                    var currentRouteArr = dataCollection.env.currentRoute.split('/');
                    var componentName, componentXMLUrl;
                    for (var i = 0 ; i < currentRouteArr.length ; i++){
                        currentRouteArr[i] = currentRouteArr[i].charAt(0).toUpperCase() + currentRouteArr[i].substr(1);
                    }
                    currentRouteArr.pop();
                    componentName = currentRouteArr.join('/');
                    componentXMLUrl = dataCollection.env.rootPath + '/application/Dashboard/Install/' + componentName + '/component.xml';
                    $.ajax({
                        url         : componentXMLUrl,
                        method      : 'get',
                        success     : function(componentResponse) {
                                        var packagesName = [];
                                        $.each($(componentResponse).children().children('package').children('name'), function() {
                                            packagesName.push($(this).html());
                                        });
                                        $('#devmodetools-modal .modal-title').addClass('text-uppercase').html('Component Information');
                                        var modalBody =
                                            '<div class="row">' +
                                                '<div class="col">' +
                                                    '<label class="text-bold">Component Name: </label><span> ' + $(componentResponse).children().children("name").html() + '</span><br>' +
                                                    '<label class="text-bold">Component Version: </label><span> ' + $(componentResponse).children().children("version").html() + '</span><br>' +
                                                '</div>' +
                                            '<div class="col">' +
                                                '<label class="text-bold">Component Description: </label><span> ' + $(componentResponse).children().children("description").html() + '</span><br>';

                                        if (packagesName.length > 0) {
                                            modalBody +=
                                                        '<label class="text-bold">Packages: </label><span> ' + packagesName.join(",") + '</span><br>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<div class="col">' +
                                                        '<div class="form-group">' +
                                                            '<label class="text-bold">Select Package:</label>' +
                                                            '<select id="' + sectionId + '-devmodetools-packages-select" class="form-control"><option></option></select>' +
                                                        '</div>' +
                                                    '</div>' +
                                                    '<div class="col">' +
                                                        '<div class="form-group">' +
                                                            '<label class="text-bold">Select Package Action:</label>' +
                                                            '<select id="' + sectionId + '-devmodetools-actions-select" class="form-control"></select>' +
                                                        '</div>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<div class="col">' +
                                                        '<div id="' + sectionId + '-devmodetools-package-info-data"></div>' +
                                                    '</div>' +
                                                '</div>' +
                                                '<div class="row">' +
                                                    '<div class="col">' +
                                                        '<div id="' + sectionId + '-devmodetools-package-action-data"></div>' +
                                                    '</div>' +
                                                '</div>';
                                        } else {
                                            modalBody +=
                                                        '<label class="text-bold">Packages: </label><span> None</span><br>' +
                                                    '</div>' +
                                                '</div>';
                                        }

                                        $('#devmodetools-modal .modal-body').empty().append(modalBody);
                                        var packagesLocation = { };
                                        $.each(packagesName, function(index, packageName) {
                                            $('#' + sectionId + '-devmodetools-packages-select').append(
                                                '<option value="' + index + '">' + packageName + '</option>'
                                            );
                                            var packageNameArr = packageName.split('\\');
                                            packagesLocation[index] = packageNameArr.join('/');
                                        });

                                        $('#' + sectionId + '-devmodetools-packages-select').change(function() {
                                            getPackageInfo(packagesLocation[$(this).children("option:selected").val()]);
                                            getPackageActionsList($(componentResponse).children().children("name").html(), packagesLocation[$(this).children("option:selected").val()]);
                                        });
                                        $('#devmodetools-modal').modal('show');

                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        paginatedPNotify('error', {
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                });

                function getPackageInfo(packageLocation) {
                    var packageXMLUrl = dataCollection.env.rootPath + '/system/' + packageLocation + '/Install/';
                    $.ajax({
                        url         : packageXMLUrl + 'package.xml',
                        method      : 'get',
                        success     : function(packageResponse) {
                                        $('#' + sectionId + '-devmodetools-package-info-data').empty().append(
                                            '<div class="row">' +
                                                '<div class="col">' +
                                                    '<label class="text-bold">Package Name: </label><span> ' + $(packageResponse).children().children("name").html() + '</span><br>' +
                                                    '<label class="text-bold">Package Version: </label><span> ' + $(packageResponse).children().children("version").html() + '</span><br>' +
                                                '</div>' +
                                                '<div class="col">' +
                                                    '<div class="row">' +
                                                        '<div class="col" id="' + sectionId + '-devmodetools-package-info-dependencies"></div>' +
                                                    '</div>' +
                                                    '<div class="row">' +
                                                        '<div class="col" id="' + sectionId + '-devmodetools-package-testing-route"></div>' +
                                                    '</div>' +
                                                '</div>' +
                                            '</div>'
                                        );
                                        var dependencies = [];
                                        $.each($(packageResponse).children().children('dependency'), function() {
                                            dependencies.push($(this).children('name').html());
                                        });
                                        if (dependencies.length > 0) {
                                            $('#' + sectionId + '-devmodetools-package-info-dependencies').empty().append(
                                                '<label class="text-bold">Package Dependencies: </label><span> ' + dependencies.join(', ') + '</span><br>'
                                            );
                                        } else {
                                            $('#' + sectionId + '-devmodetools-package-info-dependencies').empty().append(
                                                '<label class="text-bold">Package Dependencies: </label><span> None</span><br>'
                                            );
                                        }
                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        paginatedPNotify('error', {
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                }

                function getPackageActionsList(component,packageLocation) {
                    var componentArr = component.split(':');
                    componentArr.splice(0,1); //Remove Dashboard
                    var routePath = componentArr.join('/');
                    var packageXMLUrl = dataCollection.env.rootPath + '/system/' + packageLocation + '/Install/';
                    $('#' + sectionId + '-devmodetools-actions-select').empty();
                    $('#' + sectionId + '-devmodetools-actions-select').append('<option></option>');
                    $('#' + sectionId + '-devmodetools-package-action-data').empty();
                    $.ajax({
                        url         : packageXMLUrl + 'sdk.xml',
                        method      : 'get',
                        success     : function(sdkResponse) {
                                        $.each($(sdkResponse).children().children(), function(index,child) {
                                            $('#' + sectionId + '-devmodetools-actions-select').append(
                                                '<option value="' + index + '">' + child.innerHTML + '</option>'
                                            );
                                        });
                                        $('#' + sectionId + '-devmodetools-actions-select').off();
                                        $('#' + sectionId + '-devmodetools-actions-select').change(function() {
                                            parseSdkData(packageXMLUrl, $(this).children("option:selected").html());
                                            var actionStr = $(this).children("option:selected").html();
                                            var firstSplit = actionStr.split('/');
                                            var secondSplit = firstSplit[1].split('.');
                                            var methodName;
                                            if (secondSplit[0].search('Get') !== -1) {
                                                methodName = 'view';
                                            } else if (secondSplit[0].search('Update') !== -1) {
                                                methodName = 'edit';
                                            } else {
                                                methodName = secondSplit[0].toLowerCase();
                                            }
                                            $('#' + sectionId + '-devmodetools-package-testing-route').empty().append(
                                                '<label class="text-bold">Testing Route: </label><span> ' + routePath + '/' + methodName + '</span><br>'
                                            );
                                        });
                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        paginatedPNotify('error', {
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                }

                function parseSdkData(packageXMLUrl, sdk) {
                    $.ajax({
                        url         : packageXMLUrl + '/' + sdk,
                        method      : 'get',
                        success     : function(sdkResponse) {
                                        $('#' + sectionId + '-devmodetools-package-action-data').empty().append(
                                            '<table id="' + sectionId + '-devmodetools-package-action-data-table" class="table table-striped">' +
                                                '<thead>' +
                                                    '<tr>' +
                                                        '<th>INPUT</th>' +
                                                        '<th>OUTPUT</th>' +
                                                    '</tr>' +
                                                '</thead>' +
                                                '<tbody>' +
                                                '</tbody>' +
                                                '</table>'
                                        );

                                        var row;
                                        $.each($(sdkResponse).children().children('hook'), function() {
                                            row = '<tr>';
                                            row += '<td style="width:50%;">';
                                            $.each($(this).children(), function() {
                                                if ($(this)[0].tagName === 'input') {
                                                    $.each($(this).children(), function() {
                                                        if ($(this)[0].tagName === 'name') {
                                                            row += '<div class="row"><div class="col">' +
                                                                    '<label class="text-bold">Name: </label><span> ' +
                                                                        $(this).html() + '</span>' +
                                                                '</div>';
                                                        } else if ($(this)[0].tagName === 'value') {
                                                            row += '<div class="col">' +
                                                                '<label class="text-bold">Value: </label><span> ' +
                                                                    $(this).html() + '</span>' +
                                                            '</div></div>';
                                                        }
                                                    });
                                                }
                                            });
                                            row += '</td>';
                                            row += '<td style="width:50%;">';
                                            $.each($(this).children(), function() {
                                                if ($(this)[0].tagName === 'output') {
                                                    row += '<label class="text-bold">Status: </label><span> ' + $(this).children().children().html() + '</span>';
                                                    $.each($(this).children().children().children(), function() {
                                                        if ($(this)[0].tagName === 'name') {
                                                            row += '<div class="row"><div class="col">' +
                                                                    '<label class="text-bold">Name: </label><span> ' +
                                                                        $(this).html() + '</span>' +
                                                                '</div>';
                                                        } else if ($(this)[0].tagName === 'value') {
                                                            row += '<div class="col">' +
                                                                '<label class="text-bold">Value: </label><span> ' +
                                                                    $(this).html() + '</span>' +
                                                            '</div></div>';
                                                        }
                                                    });
                                                }
                                            });
                                            row += '</td>';
                                            row += '</tr>';
                                            $('#' + sectionId + '-devmodetools-package-action-data-table tbody').append(row);
                                        });
                                    },
                        error       : function(data) {
                                        PNotify.removeAll();
                                        paginatedPNotify('error', {
                                            title   : 'Error',
                                            text    : data.status + ' : ' + data.statusText
                                        });
                                    }
                    });
                }

                function doAjax(method, componentId, sectionId) {
                    PNotify.removeAll();
                    var route = $('#' + sectionId + '-devmodetools-route').val().trim();
                    if (route === '') {
                        $('#' + sectionId + '-devmodetools-route').parents('.form-group').children('.help-block').remove();
                        $('#' + sectionId + '-devmodetools-route').parents('.form-group').append(
                            '<span class="help-block text-uppercase text-danger text-xs">Route cannot be empty!</span>'
                        );
                        $('#' + sectionId + '-devmodetools-route').focus(function() {
                            $('#' + sectionId + '-devmodetools-route').parents('.form-group').children('.help-block').remove();
                        });
                    } else {
                        route = dataCollection.env.rootPath + 'index.php?route=' + route;
                        $('#' + sectionId).BazContentSectionWithForm({'task' : 'sectionToObj'});
                        var devData = { };
                        var extractComponentId;
                        var extractComponentIdCount = componentId.split('-').length;
                        if ($('#' + sectionId + ' .devModeTools').is(':checked')) {
                            $('#' + sectionId + ' .devModeTools').each(function(i,devmodetools) {
                                extractComponentId = $(devmodetools)[0].id.split('-', extractComponentIdCount + 1);
                                extractComponentId = extractComponentId[extractComponentId.length - 1];
                                if ($(devmodetools).is(':checked')) {
                                        devData[extractComponentId] =
                                        dataCollection[componentId][sectionId]['data'][extractComponentId];
                                }
                            });
                            var data = $.param(devData);
                            $.ajax({
                                url         : route,
                                data        : data,
                                method      : method,
                                dataType    : 'json',
                                success     : function(response) {
                                                $('#devmodetools-modal .modal-body').empty().append(
                                                '<label>URL: ' + route + '</label><br>' +
                                                '<label>Data Sent: ' + data + '</label><br>' +
                                                '<label>Response: </label><br>'
                                                );
                                                var html = BazHelpers.createHtmlList({obj: response});
                                                $('#devmodetools-modal .modal-title').addClass('text-uppercase').html(method + ' Response');
                                                $('#devmodetools-modal .modal-body').append(html);
                                                $('#devmodetools-modal').modal('show');
                                            },
                                error       : function(data) {
                                                PNotify.removeAll();
                                                paginatedPNotify('error', {
                                                    title   : 'Error',
                                                    text    : data.status + ' : ' + data.statusText
                                                });
                                            }
                            });
                        } else {
                            PNotify.removeAll();
                            paginatedPNotify('error', {
                                title: 'Checkbox not selected!',
                                text: 'No include field data checkbox checked',
                                type: 'error'
                            });
                            pnotifySound.play();
                        }
                    }
                }
            };

            BazContentDevModeTools._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;
                dataCollection[componentId][sectionId]['BazContentDevModeTools'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentDevModeTools']) {
                    dataCollection[componentId][sectionId]['BazContentDevModeTools'] = new BazContentDevModeTools($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentDevModeTools']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentDevModeTools'];
                    dataCollection[componentId][sectionId]['BazContentDevModeTools'] = new BazContentDevModeTools($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentDevModeTools']._init(options);
                }
            };

        return BazContentDevModeTools;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
        var bazDevMode = localStorage.getItem('bazDevMode');
        var sdkButton = '<button class="btn btn-xs bg-maroon text-uppercase mr-2" id="devmodetools-sdk-generator">SDK Generator</button>';
        var serverInfoButton;
        if (window.location.hostname === 'ind-alpha.bazaari.com.au') {
            serverInfoButton =
                '<button class="btn btn-xs btn-danger text-uppercase mr-2" id="devmodetools-open-template-issue">Open Template Issue</button>' +
                '<button class="btn btn-xs btn-info text-uppercase mr-2" id="devmodetools-server-info">Server Info</button>';
        } else {
            serverInfoButton = '';
        }
        if ($('#devModeToolsButtons').length === 0) {
            var devButtons =
                '<div id="devModeToolsButtons" class="float-right">' +
                    sdkButton +
                    serverInfoButton +
                    '<div class="btn-group btn-group-toggle btn-group-xs" data-toggle="buttons">' +
                    '    <label class="btn btn-xs btn-outline-warning" style="cursor: pointer;">' +
                    '        <input type="radio" name="options" id="devon" autocomplete="off" data-value="">DEV ON' +
                    '    </label>' +
                    '    <label class="btn btn-xs btn-outline-success" style="cursor: pointer;">' +
                    '        <input type="radio" name="options" id="devoff" autocomplete="off" data-value="">DEV OFF' +
                    '    </label>' +
                    '</div>' +
                '</div>';
            $('.main-footer').append(devButtons);
        }

        if ($('.devmodetoolsmodal').length > 0) {
            $('.devmodetoolsmodal').each(function() {
                $(this).remove();
                buildModal();
            });
        } else {
            buildModal();
        }

        function buildModal() {
            BazHelpers.modal({
                'modalId'                   : 'devmodetools',
                'modalWidth'                : '90%',
                'modalAdditionalClasses'    : 'devmodetoolsmodal',
                'modalType'                 : 'primary',
                'modalBackdrop'             : 'static',
                'modalEscClose'             : 'true',
                'modalHeader'               : true,
                'modalScrollable'           : true,
                'modalBodyAdditionalClasses': '',
                'modalFooter'               : true,
                'modalButtons'              : {
                    'close'                 : true
                },
            });
        }

        if (bazDevMode === 'true') {
            $('#devon').attr('checked', true);
            $('#devon').parents('label').addClass('active focus');
            if ($('.sectionWithForm').data('bazdevmodetools') === 'false' ||
                $('.sectionWithForm').data('bazdevmodetools') === false) {
                $('.sectionWithForm').before(
                    '<div class="devmodetoolswarning p-2 bg-danger"><h5 class="mb-0">DEV MODE : ON, DEVMODETOOLS : FALSE. CANNOT TEST THIS COMPONENT</h5></div>');
            } else {
                $('.sectionWithForm').before(
                    '<div class="devmodetoolswarning p-2 bg-warning"><h5 class="mb-0">DEV MODE : ON.</h5></div>');
            }
            $('.sectionWithForm').each(function() {
                if ($(this).data('bazdevmodetools') === 'true' ||
                    $(this).data('bazdevmodetools') === true) {
                    BazContentDevModeTools._jQueryInterface.call($(this));
                }
            });
        } else {
            $('#devoff').attr('checked', true);
            $('#devoff').parents('label').addClass('active focus');
            $('.sectionWithForm').each(function() {
                $(this).data('bazdevmodetools', false);
            });
        }

        $('#devon').parents('label').off();
        $('#devon').parents('label').click(function() {
            bazDevMode = true;
            localStorage.setItem('bazDevMode', true);
            window.location.reload(true);
        });
        $('#devoff').parents('label').off();
        $('#devoff').parents('label').click(function() {
            bazDevMode = false;
            localStorage.setItem('bazDevMode', false);
            window.location.reload(true);
        });

        // Server Info Button next to devmodetools toggle switch
        $('#devmodetools-server-info').click(function() {
            $('#devmodetools-modal .modal-title').addClass('text-uppercase').html('Server Information');
            $('#devmodetools-modal .modal-body').empty().append(
                '<div class="row vdivide">' +
                    '<div class="col">' +
                        '<div class="form-group">' +
                            '<label class="text-uppercase">Install Component(s)</label> ' +
                            '<i data-toggle="tooltip" data-html="true" data-placement="right"' +
                            ' title="" class="fa fa-fw fa-question-circle fa-1 helper " data-original-title="' +
                            'Enter component(s) name, separated by comma Example: dashboard:account,dashboard:home">' +
                            '</i>' +
                            '<sup><i data-toggle="tooltip" data-html="true" data-placement="right"' +
                            ' title="" style="font-size: 7px;" class="fa fa-fw fa-star fa-1 helper text-danger" ' +
                            'data-original-title="Required"></i></sup>' +
                            '<input type="text" class="form-control form-control-sm rounded-0" id="devmodetools-server-info-install-params" name="' + sectionId +
                            '-devmodetools-server-info-install-params" placeholder="INSTALL COMPONENT(S)">' +
                        '</div>' +
                        '<button type="button" class="btn bg-orange m-1" id="devmodetools-server-install">' +
                            '<span class="text-uppercase">Install</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="col">' +
                        '<div class="row">' +
                            '<div class="col">' +
                                '<label class="text-uppercase">Server Update Options</label><br>' +
                                '<div id="server-update-radio" class="btn-group btn-group-toggle btn-group-xs" data-toggle="buttons">' +
                                '    <label class="btn btn-xs btn-outline-primary active focus" style="cursor: pointer;">' +
                                '    <input type="radio" name="options" id="server-update-all" autocomplete="off" data-value="ALL" checked="checked">ALL' +
                                '    </label>' +
                                '    <label class="btn btn-xs btn-outline-primary" style="cursor: pointer;">' +
                                '    <input type="radio" name="options" id="server-update-component" autocomplete="off" data-value="COMPONENT">COMPONENT ONLY' +
                                '    </label>' +
                                '    <label class="btn btn-xs btn-outline-primary" style="cursor: pointer;">' +
                                '    <input type="radio" name="options" id="server-update-template" autocomplete="off" data-value="TEMPLATE">TEMPLATE ONLY' +
                                '    </label>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                        '<div class="row">' +
                            '<div class="col">' +
                                '<button type="button" class="btn bg-purple mt-4" id="devmodetools-server-update">' +
                                    '<span class="text-uppercase">Update Server</span>' +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div><hr>' +
                '<div class="row text-center">' +
                    '<div class="col">' +
                        '<button type="button" class="btn bg-teal" id="devmodetools-server-install-lastinfo">' +
                            '<span class="text-uppercase">Last Update Info</span>' +
                        '</button>' +
                    '</div>' +
                    '<div class="col">' +
                        '<button type="button" class="btn bg-teal" id="devmodetools-server-current-status">' +
                            '<span class="text-uppercase">Current Status</span>' +
                        '</button>' +
                    '</div>' +
                '</div>' +
                '<div class="row text-center" id="devmodetools-server-loader" hidden>' +
                    '<div class="col">' +
                        '<div class="fa-2x">' +
                            '<i class="fa fa-cog fa-spin"></i> LOADING PLEASE WAIT...' +
                        '</div>' +
                    '</div>' +
                '</div>' +
                '<div id="devmodetools-server-info-details">' +
                '</div>'
            );

            $('#devmodetools-modal').modal('show');
            $('[data-toggle="tooltip"]').tooltip({container:'body'});
            $('[data-toggle="popover"]').popover({container:'body', trigger: 'focus'});

            function getServerLastInstallInfo() {
                var serverLastInstall = dataCollection.env.rootPath + 'lastinstallwithparams.xml';
                $.ajax({
                    url         : serverLastInstall,
                    method      : 'get',
                    success     : function(serverResponse) {
                                    $('#devmodetools-server-loader').addClass('d-none');
                                    $('#devmodetools-server-info-details').html(
                                        '<div class="row">' +
                                            '<div class="col">' +
                                                '<label class="text-bold">Last Updated on: </label><pre> ' + $(serverResponse).children().children('lastupdate').html().trim() + '</pre>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row">' +
                                            '<div class="col">' +
                                                '<label class="text-bold">Status: </label><br><pre> ' + $(serverResponse).children().children('status').html().trim() + '</pre>' +
                                            '</div>' +
                                        '</div>'
                                    );
                                    enableButtons();
                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    paginatedPNotify('error', {
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                                }
                });
            }

            function getServerCurrentStatus() {
                var currentStatus = dataCollection.env.rootPath + 'currentstatus.php';
                $.ajax({
                    url         : currentStatus,
                    method      : 'get',
                    success     : function() {

                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    paginatedPNotify('error', {
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                    },
                    complete    : function() {
                                    var loadCurrentStatus = dataCollection.env.rootPath + 'currentstatus.xml';
                                    $.ajax({
                                        url         : loadCurrentStatus,
                                        method      : 'get',
                                        success     : function(serverResponse) {
                                                        $('#devmodetools-server-loader').addClass('d-none');
                                                        $('#devmodetools-server-info-details').html(
                                                            '<div class="row">' +
                                                                '<div class="col">' +
                                                                    '<label class="text-bold">Status checked on: </label><pre> ' + $(serverResponse).children().children('lastupdate').html().trim() + '</pre>' +
                                                                '</div>' +
                                                            '</div>' +
                                                            '<div class="row">' +
                                                                '<div class="col">' +
                                                                    '<label class="text-bold">Status: </label><br><pre> ' + $(serverResponse).children().children('status').html().trim() + '</pre>' +
                                                                '</div>' +
                                                            '</div>'
                                                        );
                                                        enableButtons();
                                        },
                                        error       : function(data) {
                                                        PNotify.removeAll();
                                                        paginatedPNotify('error', {
                                                            title   : 'Error:',
                                                            text    : data.status + ' : ' + data.statusText
                                                        });
                                        },
                                    });
                    }
                });
            }

            function checkIfCanRun(task) {
                var canRun;
                var currentProcessStatus = dataCollection.env.rootPath + 'installrunning.xml';

                $.ajax({
                    url         : currentProcessStatus,
                    method      : 'get',
                    success     : function(serverResponse) {
                                    if ($(serverResponse).children().html() === 'NO') {
                                        canRun = true;
                                    } else if ($(serverResponse).children().html() === 'YES') {
                                        canRun = false;
                                    }

                                    if (canRun) {
                                        disableButtons();
                                        $('#devmodetools-server-info-details').empty();
                                        $('#devmodetools-server-loader').removeClass('d-none');
                                        if (task === 'update') {
                                            var updateParams;
                                            var updateChecked = $('#server-update-radio').find('input[type=radio]:checked').data('value');
                                            if (updateChecked === 'ALL') {
                                                updateParams = {'installWithParams' : ''}
                                            } else if (updateChecked === 'COMPONENT') {
                                                updateParams = {'installWithParams' : 'component-only'}
                                            } else if (updateChecked === 'TEMPLATE') {
                                                updateParams = {'installWithParams' : 'template-only'}
                                            }
                                            runUpdateInstallAjax(updateParams);
                                        } else if (task === 'install') {
                                            var installParams = $('#devmodetools-server-info-install-params').val().trim();
                                            if (installParams === '') {
                                                $('#devmodetools-server-info-install-params').parents('.form-group').children('.help-block').remove();
                                                $('#devmodetools-server-info-install-params').parents('.form-group').append(
                                                    '<span class="help-block text-uppercase text-danger text-xs">Install Parameters cannot be empty!</span>'
                                                );
                                                $('#devmodetools-server-info-install-params').focus(function() {
                                                    $('#devmodetools-server-info-install-params').parents('.form-group').children('.help-block').remove();
                                                });
                                            } else {
                                                $('#devmodetools-server-info-details').empty();
                                                runUpdateInstallAjax({'installWithParams' : 'component=' + installParams});
                                            }
                                        } else if (task === 'getlastinstallinfo') {
                                            getServerLastInstallInfo();
                                        }
                                    } else {
                                        PNotify.removeAll();
                                        paginatedPNotify('error', {
                                            title   : 'Error:',
                                            text    : 'Cannot execute as another install/update process is currently running. Please try again in couple of minutes.'
                                        });
                                    }
                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    paginatedPNotify('error', {
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                                    canRun = false;
                                }
                });
            }

            function runUpdateInstallAjax(params) {
                $.ajax({
                    url         : dataCollection.env.rootPath + 'installwithparams.php',
                    method      : 'POST',
                    data        : params,
                    success     : function() {
                    },
                    error       : function(data) {
                                    PNotify.removeAll();
                                    paginatedPNotify('error', {
                                        title   : 'Error:',
                                        text    : data.status + ' : ' + data.statusText
                                    });
                    },
                    complete    : function() {
                                    getServerLastInstallInfo();
                                    enableButtons();
                    }
                });
            }

            function disableButtons() {
                $('#devmodetools-server-update').attr('disabled', true);
                $('#devmodetools-server-install').attr('disabled', true);
                $('#devmodetools-server-install-lastinfo').attr('disabled', true);
                $('#devmodetools-server-current-status').attr('disabled', true);
                $('#server-update-radio label').each(function() {
                    $(this).children('input').attr('disabled', true);
                    $(this).addClass('disabled');
                    $(this).css({'cursor': 'not-allowed'});
                    if ($(this).hasClass('active')) {
                        $(this).addClass('bg-primary');
                    }
                });
            }

            function enableButtons() {
                $('#devmodetools-server-update').attr('disabled', false);
                $('#devmodetools-server-install').attr('disabled', false);
                $('#devmodetools-server-install-lastinfo').attr('disabled', false);
                $('#devmodetools-server-current-status').attr('disabled', false);
                $('#server-update-radio label').each(function() {
                    $(this).children('input').attr('disabled', false);
                    $(this).removeClass('disabled');
                    $(this).css({'cursor': 'pointer'});
                    if ($(this).hasClass('active')) {
                        $(this).removeClass('bg-primary');
                    }
                });
            }

            $('#devmodetools-server-install-lastinfo').click(function() {
                checkIfCanRun('getlastinstallinfo');
            });

            $('#devmodetools-server-current-status').click(function() {
                $('#devmodetools-server-loader').removeClass('d-none');
                getServerCurrentStatus();
            });

            $('#devmodetools-server-update').click(function() {
                checkIfCanRun('update');
            });

            $('#devmodetools-server-install').click(function() {
                checkIfCanRun('install');
            });
        });

        if (window.location.hostname === 'ind-alpha.bazaari.com.au') {
            // Open Template Issue
            $.ajax({
                url         : 'http://projects.bazaari.com.au:8080/s/0cb5c4e1c93476061f285066dd7b68b8-T/l0lk6p/803002/' +
                                'f10222311c901db76695e748d4ff0aab/3.0.10/_/download/batch/com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs/' +
                                'com.atlassian.jira.collector.plugin.jira-issue-collector-plugin:issuecollector-embededjs.js?locale=en-AU&collectorId=e551b3b3',
                type        : 'get',
                cache       : true,
                dataType    : 'script'
            });

            window.ATL_JQ_PAGE_PROPS = {
                "triggerFunction": function(showCollectorDialog) {
                    $("#devmodetools-open-template-issue").click(function(e) {
                        e.preventDefault();
                        showCollectorDialog();
                    });
                }
            };
        }

        // SDK Generator Button next to devmodetools toggle switch
        $('#devmodetools-sdk-generator').click(function() {
            $('#devmodetools-modal .modal-title').addClass('text-uppercase').html('Sdk Data Generator');
            $('#devmodetools-modal .modal-body').empty().append(
                '<div class="row">' +
                '    <div class="col">' +
                '        <label style="display:block;">SELECT SDK GENERATOR TYPE</label>' +
                '        <div class="btn-group btn-group-toggle btn-group-sm" data-toggle="buttons">' +
                '            <label class="btn btn-sm  btn-outline-primary active focus" style="cursor: pointer;">' +
                '                <input type="radio" name="options" id="devmodetools-sdk-generator-component-selector" autocomplete="off" data-value="" checked="">COMPONENT' +
                '            </label>' +
                '            <label class="btn btn-sm  btn-outline-primary" style="cursor: pointer;">' +
                '                <input type="radio" name="options" id="devmodetools-sdk-generator-simple-listing-selector" autocomplete="off" data-value="">SIMPLE LISTING' +
                '            </label>' +
                '            <label class="btn btn-sm  btn-outline-primary" style="cursor: pointer;">' +
                '                <input type="radio" name="options" id="devmodetools-sdk-generator-datatable-listing-selector" autocomplete="off" data-value="">DATATABLE LISTING' +
                '            </label>' +
                '        </div>' +
                '    </div>' +
                '</div>' +
                '<hr>' +
                '<div id="devmodetools-sdk-generator-form">' +
                '    <legend>HOOKS</legend>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>METHOD</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Input data method" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="METHOD"></a>' +
                '                   <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <input data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" type="text" class="form-control rounded-0" id="devmodetools-sdk-generator-method" name="devmodetools-sdk-generator-method" placeholder="METHOD" minlength="1" maxlength="45" value="">' +
                '             </div>' +
                '        </div>' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>STATUS</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Status of ouput data" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="STATUS"></a>' +
                '                <span>' +
                '                    <sup>' +
                '                        <i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i>' +
                '                    </sup>' +
                '                </span>' +
                '                <select class="form-control" id="devmodetools-sdk-generator-status" name="devmodetools-sdk-generator-status">' +
                '                    <option data-value="0" value="0"></option>' +
                '                    <option data-value="APIE_NONE" value="1">APIE_NONE</option>' +
                '                    <option data-value="APIE_UNKNOWN" value="2">APIE_UNKNOWN</option>' +
                '                    <option data-value="APIE_INPUT_INVALID_DATA" value="3">APIE_INPUT_INVALID_DATA</option>' +
                '                    <option data-value="APIE_DUPLICATE" value="4">APIE_DUPLICATE</option>' +
                '                    <option data-value="APIE_NOT_FOUND" value="5">APIE_NOT_FOUND</option>' +
                '                    <option data-value="APIE_FOREIGN" value="6">APIE_FOREIGN</option>' +
                '                    <option data-value="APIE_PROCESSING_DATA" value="7">APIE_PROCESSING_DATA</option>' +
                '                    <option data-value="APIE_UNAUTHORIZED" value="8">APIE_UNAUTHORIZED</option>' +
                '                    <option data-value="APIE_UNSUPPORTED" value="9">APIE_UNSUPPORTED</option>' +
                '                    <option data-value="APIE_MUTEX" value="10">APIE_MUTEX</option>' +
                '                    <option data-value="APIE_REFERENCE_ERROR" value="11">APIE_REFERENCE_ERROR</option>' +
                '                </select>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div class="row">' +
                '        <div class="col" id="devmodetools-sdk-generator-input-col">' +
                '            <div class="form-group">' +
                '                <label>INPUT DATA</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="1 entry per line. Defined as key=value, Example:id=1 or name=user" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="INPUT DATA"></a>' +
                '                <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <textarea data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" class="form-control rounded-0" id="devmodetools-sdk-generator-input" placeholder="INPUT DATA" value="" rows="4"></textarea>' +
                '            </div>' +
                '        </div>' +
                '        <div class="col" id="devmodetools-sdk-generator-output-col">' +
                '            <div class="form-group">' +
                '                <label>OUTPUT DATA</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="1 entry per line. Defined as key=value, Example: id=1 or name=user. For Listing each row data is separated by a colon (:). Example: id=1:id=2"  class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="OUTPUT DATA"></a>' +
                '                <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <textarea data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" class="form-control rounded-0" id="devmodetools-sdk-generator-output" placeholder="OUTPUT DATA" value="" rows="4"></textarea>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>HOOK NAME</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Name of the hook, only for this tool. It will not be copied to SDK" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="HOOK NAME"></a>' +
                '                   <span><sup><i data-toggle="tooltip" data-html="true" data-placement="auto" title="" style="font-size: 7px;" class="fas fa-fw fa-star fa-1 helper text-danger" data-original-title="Required"></i></sup></span>' +
                '                <input data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" type="text" class="form-control rounded-0" id="devmodetools-sdk-generator-hook-name" name="devmodetools-sdk-generator-hook-name" placeholder="HOOK NAME" minlength="1" maxlength="45" value="">' +
                '            </div>' +
                '        </div>' +
                '        <div class="col">' +
                '            <div class="form-group">' +
                '                <label>AVAILABLE HOOKS</label>' +
                '                <a style="cursor: pointer;" tabindex="0" data-toggle="popover" data-html="true" data-trigger="focus" title="" data-content="Hooks that are added" class="fa fa-fw fa-question-circle fa-1 helper" data-original-title="AVAILABLE HOOKS"></a>' +
                '                <select data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="select2" class="form-control" id="devmodetools-sdk-generator-hooks" name="devmodetools-sdk-generator-hooks" style="width:100%;">' +
                '                    <option></option>' +
                '                </select>' +
                '            </div>' +
                '        </div>' +
                '    </div>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <button type="button" class="btn btn-sm btn-orange float-left text-white bg-purple" id="devmodetools-sdk-generator-add-update-hook">' +
                '                <i class="fas fa-fw fa-candy-cane"></i> ADD HOOK' +
                '            </button>' +
                '        </div>' +
                '        <div class="col">' +
                '            <button type="button" class="btn btn-sm btn-warning mr-1 float-left text-white" id="devmodetools-sdk-generator-edit-hook" disabled="">' +
                '                <i class="fas fa-fw fa-edit"></i> EDIT HOOK' +
                '            </button>' +
                '            <button type="button" class="btn btn-sm btn-info mr-1 float-left text-white" id="devmodetools-sdk-generator-copy-hook" disabled="">' +
                '                <i class="fas fa-fw fa-copy"></i> COPY HOOK' +
                '            </button>' +
                '            <button type="button" class="btn btn-sm btn-danger mr-1 float-right" id="devmodetools-sdk-generator-delete-hook" disabled="">' +
                '                <i class="fas fa-fw fa-trash"></i> DELETE HOOK' +
                '            </button>' +
                '        </div>' +
                '    </div>' +
                '    <hr>' +
                '    <div class="row">' +
                '        <div class="col">' +
                '            <button type="button" class="btn btn-sm btn-primary text-white float-right bg-maroon" id="devmodetools-sdk-generator-generate-sdk" disabled="">' +
                '                <i class="fas fa-fw fa-magic"></i> GENERATE COMPONENT SDK' +
                '            </button>' +
                '        </div>' +
                '    </div>' +
                '</div>' +
                '<hr>' +
                '<div class="row">' +
                '    <div class="col">' +
                '        <div class="form-group">' +
                '            <label>SDK OUTPUT</label>' +
                '            <textarea data-bazpostoncreate="true" data-bazpostonupdate="true" data-bazscantype="input" class="form-control rounded-0" id="devmodetools-sdk-generator-sdk-output" placeholder="SDK OUTPUT" value="" rows="4"></textarea>' +
                '        </div>' +
                '    </div>' +
                '</div>'
            );

            window['dataCollection']['devmodetools-sdk-generator'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['component'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['component']['hooks'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['listing'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['listing']['hooks'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['datatable'] = { };
            window['dataCollection']['devmodetools-sdk-generator']['datatable']['hooks'] = { };
            var hooksData = window['dataCollection']['devmodetools-sdk-generator']['component']['hooks'];
            var generatorType = 'component';

            $('#devmodetools-modal').modal('show');
            $('[data-toggle="tooltip"]').tooltip({container:'body'});
            $('[data-toggle="popover"]').popover({container:'body', trigger: 'focus'});

            function addUpdateHook(id) {
                var enteredMethod = $('#devmodetools-sdk-generator-method').val();
                if ($('#devmodetools-sdk-generator-component-selector').is('input:checked')) {
                    generatorType = 'component';
                } else if ($('#devmodetools-sdk-generator-simple-listing-selector').is('input:checked')) {
                    generatorType = 'listing';
                } else if ($('#devmodetools-sdk-generator-datatable-listing-selector').is('input:checked')) {
                    generatorType = 'datatable';
                }
                if (!enteredMethod) {
                    $('#devmodetools-sdk-generator-method').parents('.form-group').children('.help-block').remove();
                    $('#devmodetools-sdk-generator-method').parents('.form-group').append(
                        '<span class="help-block text-uppercase text-danger text-xs">Method cannot be empty!</span>'
                    );
                    $('#devmodetools-sdk-generator-method').focus(function() {
                        $('#devmodetools-sdk-generator-method').parents('.form-group').children('.help-block').remove();
                    });
                }

                if (generatorType === 'component' || generatorType === 'datatable') {
                    var enteredInput = $('#devmodetools-sdk-generator-input').val();
                    if (enteredInput) {
                        var enteredInputArr = enteredInput.split('\n');
                        var inputArr = [];
                        $.each(enteredInputArr, function(index,arr) {
                            var enteredInputArrObject = { };
                            var array = arr.split('=');
                            enteredInputArrObject['name'] = array[0];
                            enteredInputArrObject['value'] = array[1];
                            inputArr.push(enteredInputArrObject);
                        });
                    } else {
                        $('#devmodetools-sdk-generator-input').parents('.form-group').children('.help-block').remove();
                        $('#devmodetools-sdk-generator-input').parents('.form-group').append(
                            '<span class="help-block text-uppercase text-danger text-xs">Input data cannot be empty!</span>'
                        );
                        $('#devmodetools-sdk-generator-input').focus(function() {
                            $('#devmodetools-sdk-generator-input').parents('.form-group').children('.help-block').remove();
                        });
                    }
                }

                // if (generatorType === 'component' || generatorType === 'listing') {
                    var enteredOutput = $('#devmodetools-sdk-generator-output').val();

                    var outputArr;
                    if (generatorType === 'component') {
                        if (enteredOutput) {
                            var enteredOutputArr = enteredOutput.split('\n');
                            outputArr = [];
                            $.each(enteredOutputArr, function(index,arr) {
                                var enteredOutputArrObject = { };
                                var array = arr.split('=');
                                enteredOutputArrObject['name'] = array[0];
                                enteredOutputArrObject['value'] = array[1];
                                outputArr.push(enteredOutputArrObject);
                            });
                        } else {
                            $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            $('#devmodetools-sdk-generator-output').parents('.form-group').append(
                                '<span class="help-block text-uppercase text-danger text-xs">Output data cannot be empty!</span>'
                            );
                            $('#devmodetools-sdk-generator-output').focus(function() {
                                $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            });
                        }
                    } else if (generatorType === 'listing' || generatorType === 'datatable') {
                        if (enteredOutput) {
                            var rows = enteredOutput.split(':');
                            var columns = [];
                            $.each(rows, function(index,row) {
                                var eachColumn = row.split('\n');
                                var column = [];
                                $.each(eachColumn, function(index,arr) {
                                    if (arr !== '') {
                                        column.push(arr);
                                    }
                                });
                                columns.push(column);
                            });
                            outputArr = [];
                            $.each(columns, function(index,column) {
                                var outputColumnArr = [];
                                $.each(column, function(index,field){
                                    var enteredOutputArrObject = { };
                                    var array = field.split('=');
                                    enteredOutputArrObject['name'] = array[0];
                                    enteredOutputArrObject['value'] = array[1];
                                    outputColumnArr.push(enteredOutputArrObject);
                                });
                                outputArr.push(outputColumnArr);
                            });
                        } else {
                            $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            $('#devmodetools-sdk-generator-output').parents('.form-group').append(
                                '<span class="help-block text-uppercase text-danger text-xs">Output data cannot be empty!</span>'
                            );
                            $('#devmodetools-sdk-generator-output').focus(function() {
                                $('#devmodetools-sdk-generator-output').parents('.form-group').children('.help-block').remove();
                            });
                        }
                    }

                var statusVal = $('#devmodetools-sdk-generator-status').val();
                var statusValue = $('#devmodetools-sdk-generator-status').children("option:selected").data('value');
                if (statusVal === '0') {
                    $('#devmodetools-sdk-generator-status').parents('.form-group').children('.help-block').remove();
                    $('#devmodetools-sdk-generator-status').parents('.form-group').append(
                        '<span class="help-block text-uppercase text-danger text-xs">Please select status!</span>'
                    );
                    $('#devmodetools-sdk-generator-status').change(function() {
                        $('#devmodetools-sdk-generator-status').parents('.form-group').children('.help-block').remove();
                    });
                }

                var hookName = $('#devmodetools-sdk-generator-hook-name').val();
                if (!hookName) {
                    $('#devmodetools-sdk-generator-hook-name').parents('.form-group').children('.help-block').remove();
                    $('#devmodetools-sdk-generator-hook-name').parents('.form-group').append(
                        '<span class="help-block text-uppercase text-danger text-xs">Hook name cannot be empty!</span>'
                    );
                    $('#devmodetools-sdk-generator-hook-name').focus(function() {
                        $('#devmodetools-sdk-generator-hook-name').parents('.form-group').children('.help-block').remove();
                    });
                }

                if ($('#devmodetools-sdk-generator-form').find('.help-block').length === 0) {
                    hooksData[hookName] = { };
                    hooksData[hookName]['hookName'] = hookName;
                    hooksData[hookName]['enteredMethod'] = enteredMethod;
                    if (generatorType === 'component' || generatorType === 'datatable') {
                        hooksData[hookName]['inputArr'] = inputArr;
                    }
                    hooksData[hookName]['outputArr'] = outputArr;

                    hooksData[hookName]['statusVal'] = statusVal;
                    hooksData[hookName]['statusValue'] = statusValue;

                    var data = {
                        id: hookName,
                        text: hookName
                    };
                    if ($('#devmodetools-sdk-generator-hooks').find("option[value='" + data.id + "']").length) {
                        $('#devmodetools-sdk-generator-hooks').val('').trigger('change');
                    } else {
                        var newOption = new Option(data.text, data.id, false, false);
                        $('#devmodetools-sdk-generator-hooks').append(newOption).trigger('change');
                    }

                    $('#devmodetools-sdk-generator-method').val('');
                    $('#devmodetools-sdk-generator-status').val('').trigger('change');
                    $('#devmodetools-sdk-generator-input').val('');
                    $('#devmodetools-sdk-generator-output').val('');
                    $('#devmodetools-sdk-generator-hook-name').val('');
                    $('#devmodetools-sdk-generator-hooks').val('').trigger('change');
                    $('#devmodetools-sdk-generator-generate-sdk').attr('disabled', false);

                    if (id === 'devmodetools-sdk-generator-edit-hook') {
                        $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', false);
                        $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                        $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                        $('#devmodetools-sdk-generator-delete-hook').attr('disabled', true);
                    }

                    PNotify.removeAll();
                    paginatedPNotify('success', {
                        title: 'Hook Added',
                        text: 'Now you can select hook to edit/delete it',
                    });
                }
            }

            function editCopyHook(id) {
                var selected = $('#devmodetools-sdk-generator-hooks').children("option:selected").val();
                if (id === 'devmodetools-sdk-generator-copy-hook') {
                    $('#devmodetools-sdk-generator-hook-name').val(hooksData[selected]['hookName'] + ' (COPY)');
                } else if (id === 'devmodetools-sdk-generator-edit-hook') {
                    $('#devmodetools-sdk-generator-hook-name').val(hooksData[selected]['hookName']);
                }
                $('#devmodetools-sdk-generator-method').val(hooksData[selected]['enteredMethod']);
                $('#devmodetools-sdk-generator-status').val(hooksData[selected]['statusVal']).trigger('change');

                if (generatorType === 'component' || generatorType === 'datatable') {
                    var rebuildInputArr = [];
                    $.each(hooksData[selected]['inputArr'], function() {
                        rebuildInputArr.push(this.name + '=' + this.value);
                    });
                    $('#devmodetools-sdk-generator-input').val(rebuildInputArr.join('\n'));

                    var rebuildOutputArr = [];
                    $.each(hooksData[selected]['outputArr'], function() {
                        rebuildOutputArr.push(this.name + '=' + this.value);
                    });
                    $('#devmodetools-sdk-generator-output').val(rebuildOutputArr.join('\n'));
                }
                if (generatorType === 'listing' || generatorType === 'datatable') {
                    var rebuildOutputListingArr = [];
                    $.each(hooksData[selected]['outputArr'], function(index,row) {
                        var rowData = [];
                        $.each(row, function() {
                            rowData.push(this.name + '=' + this.value);
                        });
                        rebuildOutputListingArr.push(rowData.join('\n'));
                    });
                    $('#devmodetools-sdk-generator-output').val(rebuildOutputListingArr.join('\n:\n'));
                }

                $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', false);
            }

            function deleteHook() {
                var selected = $('#devmodetools-sdk-generator-hooks').children("option:selected").val();
                if (selected !== '') {
                    $('#devmodetools-sdk-generator-method').val('');
                    $('#devmodetools-sdk-generator-status').val('').trigger('change');
                    $('#devmodetools-sdk-generator-input').val('');
                    $('#devmodetools-sdk-generator-output').val('');
                    $('#devmodetools-sdk-generator-hook-name').val('');
                    $('#devmodetools-sdk-generator-hooks option[value="' + selected + '"]').remove().trigger('change');
                    delete hooksData[selected];
                    $('#devmodetools-sdk-generator-sdk-output').val('Hook deleted, please regenerate SDK');
                    if ($('#devmodetools-sdk-generator-hooks option').length === 1) {
                        $('#devmodetools-sdk-generator-generate-sdk').attr('disabled', true);
                    }
                    $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', false);
                    $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-delete-hook').attr('disabled', true);
                }
            }

            function objToXML(objectValue) {

                var xml = '';
                xml +=
                '   <hook>\n' +
                '       <component>APPLICATION</component>\n' +
                '       <method>' + objectValue.enteredMethod + '</method>\n';

                if (generatorType === 'component') {
                    for (var input in objectValue.inputArr) {
                        xml +=
                            '       <input>\n' +
                            '            <name>' + objectValue.inputArr[input]['name'] + '</name>\n' +
                            '            <value>' + objectValue.inputArr[input]['value'] + '</value>\n' +
                            '       </input>\n';
                    }

                    xml +=
                        '       <output>\n' +
                        '           <api>\n' +
                        '               <status>' + objectValue.statusValue + '</status>\n';


                    for (var output in objectValue.outputArr) {
                        xml +=
                            '               <array>\n' +
                            '                   <name>' + objectValue.outputArr[output]['name'] + '</name>\n' +
                            '                   <value>' + objectValue.outputArr[output]['value'] + '</value>\n' +
                            '               </array>\n';
                    }

                    xml +=
                        '           </api>\n' +
                        '       </output>\n' +
                        '   </hook>\n';
                }

                if (generatorType === 'listing') {
                    xml +=
                        '       <output>\n' +
                        '           <api>\n' +
                        '               <status>' + objectValue.statusValue + '</status>\n';

                    for (var rows in objectValue.outputArr) {
                        xml +=
                            '               <array>\n';
                            for (var row in objectValue.outputArr[rows]) {
                                xml +=
                                    '                   <array>\n' +
                                    '                       <name>' + objectValue.outputArr[rows][row]['name'] + '</name>\n' +
                                    '                       <value>' + objectValue.outputArr[rows][row]['value'] + '</value>\n' +
                                    '                   </array>\n';
                            }
                        xml +=
                            '               </array>\n';
                    }

                    xml +=
                        '           </api>\n' +
                        '       </output>\n' +
                        '   </hook>\n';
                }

                if (generatorType === 'datatable') {
                    for (var datatableInput in objectValue.inputArr) {
                        xml +=
                            '       <input>\n' +
                            '            <name>' + objectValue.inputArr[datatableInput]['name'] + '</name>\n' +
                            '            <value>' + objectValue.inputArr[datatableInput]['value'] + '</value>\n' +
                            '       </input>\n';
                    }

                    xml +=
                        '       <output>\n' +
                        '           <api>\n' +
                        '               <status>' + objectValue.statusValue + '</status>\n' +
                        '               <listingdata>\n';

                    for (var datatableRows in objectValue.outputArr) {
                        xml +=
                            '                   <rows>\n';
                            for (var datatableRow in objectValue.outputArr[datatableRows]) {
                                xml +=
                                    '                       <array>\n' +
                                    '                           <name>' + objectValue.outputArr[datatableRows][datatableRow]['name'] + '</name>\n' +
                                    '                           <value>' + objectValue.outputArr[datatableRows][datatableRow]['value'] + '</value>\n' +
                                    '                       </array>\n';
                            }
                        xml +=
                            '                   </rows>\n';
                    }

                    xml +=
                        '                    <pagination>\n' +
                        '                        <next>\n' +
                        '                            <id>ENTER YOUR PAGINATION NEXT ID</id>\n' +
                        '                        </next>\n' +
                        '                        <prev>\n' +
                        '                            <id>ENTER YOUR PAGNIATION PREV ID</id>\n' +
                        '                        </prev>\n' +
                        '                    </pagination>\n' +
                        '                </listingdata>\n' +
                        '           </api>\n' +
                        '       </output>\n' +
                        '   </hook>\n';
                }
                return xml;
            }

            $('#devmodetools-sdk-generator-hooks').change(function() {
                var selected = $(this).children("option:selected").val();
                if (selected !== '') {
                    $('#devmodetools-sdk-generator-add-update-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-edit-hook').attr('disabled', false);
                    $('#devmodetools-sdk-generator-copy-hook').attr('disabled', false);
                    $('#devmodetools-sdk-generator-delete-hook').attr('disabled', false);
                } else {
                    $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                    $('#devmodetools-sdk-generator-delete-hook').attr('disabled', true);
                }
            });

            //Component Click
            $('#devmodetools-sdk-generator-component-selector').parent().click(function() {
                $('#devmodetools-sdk-generator-generate-sdk').html('<i class="fas fa-fw fa-magic"></i> GENERATE COMPONENT SDK');
                hooksData = window['dataCollection']['devmodetools-sdk-generator']['component']['hooks'];
                $('#devmodetools-sdk-generator-input-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-input').attr('disabled', false);
                $('#devmodetools-sdk-generator-input').val('');
                $('#devmodetools-sdk-generator-output-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-output').attr('disabled', false);
                $('#devmodetools-sdk-generator-status').val('0').attr('disabled', false);
                $('#devmodetools-sdk-generator-form').find('.help-block').remove();
            });
            // Simple Listing Click
            $('#devmodetools-sdk-generator-simple-listing-selector').parent().click(function() {
                $('#devmodetools-sdk-generator-generate-sdk').html('<i class="fas fa-fw fa-magic"></i> GENERATE SIMPLE LISTING SDK');
                hooksData = window['dataCollection']['devmodetools-sdk-generator']['listing']['hooks'];
                $('#devmodetools-sdk-generator-input-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-input').attr('disabled', true);
                $('#devmodetools-sdk-generator-input').val('');
                $('#devmodetools-sdk-generator-output-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-output').attr('disabled', false);
                $('#devmodetools-sdk-generator-form').find('.help-block').remove();
            });
            // Datatable Listing Click
            $('#devmodetools-sdk-generator-datatable-listing-selector').parent().click(function() {
                $('#devmodetools-sdk-generator-generate-sdk').html('<i class="fas fa-fw fa-magic"></i> GENERATE DATATABLE LISTING SDK');
                hooksData = window['dataCollection']['devmodetools-sdk-generator']['datatable']['hooks'];
                $('#devmodetools-sdk-generator-input-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-input').attr('disabled', false);
                $('#devmodetools-sdk-generator-input').val('operation=load\nresults=20');
                $('#devmodetools-sdk-generator-output-col').removeClass('d-none');
                $('#devmodetools-sdk-generator-output').attr('disabled', false);
                $('#devmodetools-sdk-generator-form').find('.help-block').remove();
            });

            $('#devmodetools-sdk-generator-add-update-hook').click(function() {
                $('#devmodetools-sdk-generator-add-update-hook').html('<i class="fas fa-fw fa-candy-cane"></i> ADD HOOK');
                addUpdateHook($(this)[0].id);
                $('#devmodetools-sdk-generator-sdk-output').val('Hook Added/Updated, please regenerate SDK');
            });

            $('#devmodetools-sdk-generator-edit-hook').click(function() {
                $('#devmodetools-sdk-generator-add-update-hook').html('<i class="fas fa-fw fa-candy-cane"></i> UPDATE HOOK');
                $('#devmodetools-sdk-generator-copy-hook').attr('disabled', true);
                editCopyHook($(this)[0].id);
            });

            $('#devmodetools-sdk-generator-copy-hook').click(function() {
                $('#devmodetools-sdk-generator-edit-hook').attr('disabled', true);
                editCopyHook($(this)[0].id);
            });

            $('#devmodetools-sdk-generator-delete-hook').click(function() {
                deleteHook();
            });

            $('#devmodetools-sdk-generator-generate-sdk').click(function() {
                var sdk =
                        '<?xml version="1.0" encoding="UTF-8"?>\n' +
                        '<sdk>\n';

                if (generatorType === 'datatable') {
                    sdk +=
                    '   <hook>\n' +
                    '       <component>APPLICATION</component>\n' +
                    '       <method>ENTER YOUR LISTING METHOD NAME</method>\n' +
                    '        <output>\n' +
                    '            <api>\n' +
                    '                <status>APIE_NONE</status>\n' +
                    '                <listing>\n' +
                    '                    <debug>1</debug>\n' +
                    '                    <ajax>\n' +
                    '                        <url>test</url>\n' +
                    '                    </ajax>\n' +
                    '                    <style>\n' +
                    '                        <table>baztable</table>\n' +
                    '                    </style>\n' +
                    '                    <html>\n' +
                    '                        <empty>\n' +
                    '                            <html><![CDATA[<center>No data</center>]]></html>\n' +
                    '                        </empty>\n' +
                    '                    </html>\n' +
                    '                    <refs>id</refs>\n' +
                    '                    <row>\n' +
                    '                        <checkbox>0</checkbox>\n' +
                    '                        <control>0</control>\n' +
                    '                    </row>\n' +
                    '                    <pagination>\n' +
                    '                        <show>1</show>\n' +
                    '                    </pagination>\n' +
                    '                    <runtime>\n' +
                    '                        <columns>\n' +
                    '                            <name>username</name>\n' +
                    '                            <value>username</value>\n' +
                    '                            <style>\n' +
                    '                                <width>40%</width>\n' +
                    '                                <responsive>\n' +
                    '                                    <xs>true</xs>\n' +
                    '                                </responsive>\n' +
                    '                            </style>\n' +
                    '                            <sort>\n' +
                    '                                <enabled>0</enabled>\n' +
                    '                            </sort>\n' +
                    '                        </columns>\n' +
                    '                        <columns>\n' +
                    '                            <name>status</name>\n' +
                    '                            <value>status</value>\n' +
                    '                            <style>\n' +
                    '                                <width>40%</width>\n' +
                    '                                <responsive>\n' +
                    '                                    <xs>true</xs>\n' +
                    '                                </responsive>\n' +
                    '                            </style>\n' +
                    '                            <sort>\n' +
                    '                                <enabled>0</enabled>\n' +
                    '                            </sort>\n' +
                    '                        </columns>\n' +
                    '                    </runtime>\n' +
                    '                </listing>\n' +
                    '            </api>\n' +
                    '        </output>\n' +
                    '    </hook>\n';
                }

                // Loop through hooks
                for (var hook in hooksData) {
                    sdk += objToXML(hooksData[hook]);
                }
                sdk += '</sdk>';
                $('#devmodetools-sdk-generator-sdk-output').val(sdk);
            });
        });
    });

    $.fn[NAME] = BazContentDevModeTools._jQueryInterface;
    $.fn[NAME].Constructor = BazContentDevModeTools;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentDevModeTools._jQueryInterface;
    };

    return BazContentDevModeTools;
}(jQuery);

exports.BazContentDevModeTools = BazContentDevModeTools;

Object.defineProperty(exports, '__esModule', { value: true });

}));