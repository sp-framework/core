/* exported BazContentSectionWithStorage */
/* globals  */
/*
* @title                    : BazContentSectionWithStorage
* @description              : Baz Storage Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSectionWithStorage._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSectionWithStorage = function() {
    var BazContentSectionWithStorage = void 0;
    var dataCollection = window.dataCollection;
    var componentId, sectionId, treeId, tableId, dropzoneId, fileId, modalId, thisOptions, datatableOptions, files;
    var dateAdded, dateChanged, extractDateAdded, extractDateChanged, dataSize, dataStatus;
    var storageURL = window.dataCollection.env.rootPath + 'index.php?route=common/storage/view';

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function init(options) {
        componentId = $(options.storageId).parents('.component')[0].id;
        sectionId = $(options.storageId)[0].id;
        treeId = sectionId + '-tree';
        tableId = sectionId + '-table';
        dropzoneId = sectionId + '-dropzone';
        fileId = sectionId + '-file';
        modalId = sectionId + '-modal';

        if (!dataCollection[componentId]) {
            dataCollection[componentId] = { };
        }
        if (!dataCollection[componentId][sectionId]) {
            dataCollection[componentId][sectionId] = { };
        }

        fixHeight();
        initTable();
        initDz();
        getStorageList();
        registerButtons();
    }

    function fixHeight() {
        $(document).on('heightFixed', function() {
            var bodyHeight = $('#' + sectionId + '-card .card-body').height();
            var buttonsHeight = $('#' + sectionId + '-buttons-div').height();
            var storageContentHeight = bodyHeight - buttonsHeight;
            $('#' + sectionId + '-content-div').css({
                'min-height'    : storageContentHeight,
                'max-height'    : storageContentHeight,
            });
            $('div.dataTables_scrollBody').css({'max-height':storageContentHeight - 100});
            $('#' + treeId + '-div').css({'max-height':storageContentHeight - 10, 'overflow': 'auto'})
        });
    }

    function initTable() {
        var listColumns = { };
        var selectOptions, rowId;
        thisOptions = dataCollection[componentId][sectionId][tableId];
        thisOptions['rowsData'] = { };
        datatableOptions = thisOptions.listOptions.datatable;
        listColumns[thisOptions.listOptions.tableName] = [];
        var hideColumns = [];

        $.each(datatableOptions.columns, function(index,column) {
            listColumns[thisOptions.listOptions.tableName].push({
                data            : column,
                title           : column.toUpperCase()
            });
        });
        listColumns[thisOptions.listOptions.tableName][1]['title'] = 'NAME';
        listColumns[thisOptions.listOptions.tableName][2]['title'] = 'SIZE (kB)';
        listColumns[thisOptions.listOptions.tableName][3]['title'] = 'MODIFIED';

        // Hide ID Column
        hideColumns.push(0);
        // Number of Columns to show/hide
        if (!datatableOptions.NoOfColumnsToShow) {
            datatableOptions.NoOfColumnsToShow = 4;
        }
        if (datatableOptions.columns.length > datatableOptions.NoOfColumnsToShow) {
            var colDiff = datatableOptions.columns.length - datatableOptions.NoOfColumnsToShow;
            for (var i = 1; i <= colDiff; i++) {
                hideColumns.push(datatableOptions.columns.length - i);
            }
        }

        // Column Select
        if (datatableOptions.select) {
            selectOptions = {
                style       : datatableOptions.selectStyle,
                className   : 'bg-info'
            }
        } else {
            selectOptions = false;
        }

        datatableOptions = $.extend(datatableOptions, {
            columns         : listColumns[thisOptions.listOptions.tableName],
            rowId           : 'id',
            fixedHeader     : datatableOptions.fixedHeader,
            responsive      : datatableOptions.responsive,
            paging          : false,
            scrollY         : 100,
            scrollCollapse  : true,
            select          : selectOptions,
            searching       : false,
            lengthMenu      : false,
            columnDefs      : [{
                                visible         : false,
                                targets         : hideColumns
                            },
                            { "width": "60%", "targets": 1 }],
            language       : {
                                paginate        : {
                                                    previous    : '<i class="fa fa-angle-left"></i>',
                                                    next        : '<i class="fas fa-angle-right"></i>'
                                                },
                                zeroRecords     : datatableOptions.zeroRecords,
                                info            : 'Showing _START_ to _END_ of _TOTAL_ files',
                                infoEmpty       : '',
                                select          : {
                                    rows    : {
                                            _   : 'Selected %d files. Click again to deselect file',
                                            0   : '',
                                            1   : 'Selected 1 file. Click again to deselect file'
                                        }
                                }
                            },
            initComplete    : function() {
                            },
            // drawCallback    : function() {
            //                     drawCallback();
            //                 }
        });

        // Pagination
        // if (datatableOptions.paging) {
        //     $.extend(thisOptions.listOptions.datatable, {
        //         paging : true,
        //         pagingType : 'simple',
        //     });
        //     datatableOptions['language']['zeroRecords'] = '<i class="fas fa-cog fa-spin"></i> Loading...';
        // }

        thisOptions['datatable'] = $('#' + thisOptions.listOptions.tableName).DataTable(datatableOptions);
        // Select
        thisOptions['datatable']
            .on('select', function(e, dt, type, indexes) {
                rowId = thisOptions['datatable'].row(indexes).data().id;
                thisOptions['rowsData'][rowId] = { };
                thisOptions['rowsData'][thisOptions['datatable'].row(indexes).data().id] = thisOptions['datatable'].row(indexes).data();
                updateMimeIcon(thisOptions['rowsData'][thisOptions['datatable'].row(indexes).data().id]);
                updateInfo('files', thisOptions['rowsData'], true);
                $('#' + fileId + '-info').attr('disabled', false);
            })
            .on('deselect', function(e, dt, type, indexes) {
                rowId = thisOptions['datatable'].row(indexes).data().id;
                delete thisOptions['rowsData'][rowId];
                if (Object.keys(thisOptions['rowsData']) > 0) {
                    updateInfo('files', thisOptions['rowsData'], false, rowId);
                } else {
                    $('#' + fileId + '-info').attr('disabled', true);
                }
            });
    }

    function updateMimeIcon(rowData) {
        if (rowData['mime'] === 'text/plain') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-alt';
        } else if (rowData['mime'] === 'application/pdf') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-pdf';
        } else if (rowData['mime'] === 'application/msword' ||
                    rowData['mime'] === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-word';
        } else if (rowData['mime'] === 'text/csv') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-csv';
        } else if (rowData['mime'] === 'application/vnd.ms-excel' ||
                    rowData['mime'] === 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-excel';
        } else if (rowData['mime'] === 'image/jpeg' ||
                    rowData['mime'] === 'image/png' ||
                    rowData['mime'] === 'image/bmp' ||
                    rowData['mime'] === 'image/gif') {
            rowData['mime_icon'] = 'fas fa-fw fa-file-image';
        } else {
            rowData['mime_icon'] = 'fas fa-fw fa-file';
        }
    }

    function redrawTable(data) {
         $.fn.dataTable.settings[0]["oLanguage"]["select"]["rows"][0] = "<i class=\"fas fa-fw fa-info-circle\"></i>Click row to select file";
        thisOptions['datatable'].rows.add(data).draw();
        thisOptions['rowsData'] = { };
        $('#' + fileId + '-info').attr('disabled', true);
    }

    function initDz() {
        dataCollection[componentId][sectionId][dropzoneId]['dropzone'] = $('#' + dropzoneId).dropzone(dataCollection[componentId][sectionId][dropzoneId]);
    }

    function getStorageList() {
        doAjax(storageURL, { }, 'get', null);
    }

    function getStorageItems(data) {
        if (!data.node.original['data-type'] || data.node.original['data-type'] === '1') {
            var postData;
            $('#' + treeId).jstree(true).set_icon(data.node, 'fas fa-cog fa-fw fa-spin');
            if (data.node.parent === '#') {
                postData = {'folder_id' : data.node.li_attr['data-id']};
            } else {
                postData = {'folder_id' : data.node.original['data-id']};
            }
            doAjax(storageURL, postData, 'post', data);
            updateInfo('folder', data);
        } else {
            updateInfo('folder', data);
        }
    }

    function updateInfo(type, data, select, deselectId) {
        if (type === 'folder') {
            if ($('#' + modalId + '-info-content').length > 0) {
                $('#' + modalId + '-info-content').remove();
            }
            $('#' + modalId + ' .modal-body').append(
                '<div class="row" id="' + modalId + '-info-content">' +
                    '<div class="col border p-2">' +
                        '<div class="row" id="' + modalId + '-preview"></div>' +
                        '<div class="row" id="' + modalId + '-name"></div>' +
                        '<div class="row" id="' + modalId + '-description"></div>' +
                        '<div class="row" id="' + modalId + '-size"></div>' +
                        '<div class="row" id="' + modalId + '-added"></div>' +
                        '<div class="row" id="' + modalId + '-modified"></div>' +
                        '<div class="row" id="' + modalId + '-location"></div>' +
                        '<div class="row" id="' + modalId + '-status"></div>' +
                    '</div>' +
                '</div>'
            );

            if (data.node.parent !== '#') {
                if (data.node.original['data-added'] !== '0') {
                    dateAdded = new Date(data.node.original['data-added'] * 1000);
                    extractDateAdded = dateAdded.toDateString() + ' ' + dateAdded.toTimeString();
                }
                if (data.node.original['data-changed'] !== '0') {
                    dateChanged = new Date(data.node.original['data-changed'] * 1000);
                    extractDateChanged = dateChanged.toDateString() + ' ' + dateChanged.toTimeString();
                } else {
                    extractDateChanged = extractDateAdded;
                }

                if (data.node.original['data-size'] === null) {
                    dataSize = '-';
                } else {
                    dataSize = data.node.original['data-size'] + ' KB';
                }

                if (data.node.original['data-status'] === '0' || data.node.original['data-status'] === '1') {
                    dataStatus = '<span class="badge badge-warning">Processing...</span>';
                } else if (data.node.original['data-status'] === '2') {
                    dataStatus = '<span class="badge badge-success">Ok</span>';
                } else if (data.node.original['data-status'] === '3' || data.node.original['data-status'] === '4') {
                    dataStatus = '<span class="badge badge-danger">Error</span>';
                }

                $('#' + modalId + '-preview').empty().append(
                    '<div class="col text-center m-1 p-1">' +
                        '<span><i class="' + data.node.original['data-icon'] + ' fa-6x"></i></span>' +
                    '</div>'
                );
                $('#' + modalId + '-name').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Name</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + data.node.text + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-size').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Size</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + dataSize + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-added').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Added On</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + extractDateAdded + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-modified').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Modified On</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + extractDateChanged + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-location').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Location</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + 'location' + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-status').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Status</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + dataStatus + '</span>' +
                    '</div>'
                );
            } else {
                $('#' + modalId + '-preview').empty().append(
                    '<div class="col text-center m-1 p-1">' +
                        '<span><i class="fas fa-fw fa-hdd fa-6x"></i></span>' +
                    '</div>'
                );
                $('#' + modalId + '-name').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Name</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + data.node.text + '</span>' +
                    '</div>'
                );
                $('#' + modalId + '-description').empty().append(
                    '<div class="col-md-2 col-xs-12">' +
                        '<span class="text-upper text-bold">Description</span>' +
                    '</div>' +
                    '<div class="col-md-10 col-xs-12" >' +
                        '<span>: ' + data.node.data.description + '</span>' +
                    '</div>'
                );
            }
        } else if (type === 'files') {
            if (select && Object.keys(data).length === 1) {
                if ($('#' + modalId + '-info-content').length > 0) {
                    $('#' + modalId + '-info-content').remove();
                }
                $('#' + modalId + ' .modal-body').append(
                    '<div class="row" id="' + modalId + '-info-content">' +
                        '<div class="col card-body p-0">' +
                            '<ul class="nav nav-tabs" id="' + modalId + '-info-content-files-tabs-links" role="tablist">' +
                                '<li class="nav-item">' +
                                    '<a class="nav-link active" id="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '-tab" ' +
                                    'data-toggle="pill" href="#' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '" ' +
                                    'role="tab" aria-controls="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '" ' +
                                    'aria-selected="true">' + data[Object.keys(data)[0]]['entry'] + '</a>' +
                                '</li>' +
                            '</ul>' +
                            '<div class="tab-content" id="' + modalId + '-info-content-files-tabs-content">' +
                                '<div class="tab-pane fade show active" id="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '" ' +
                                'role="tabpanel" aria-labelledby="' + modalId + '-info-content-files-tabs-' + Object.keys(data)[0] + '">' +
                                    '<div class="col border-bottom p-2">' +
                                        '<div class="row" id="' + modalId + '-preview-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-12 text-center">' +
                                                '<i class="' + data[Object.keys(data)[0]]['mime_icon'] + ' fa-6x"></i>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-name-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Name</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['entry'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-size-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Size</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['entry_size'] + ' kB</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-added-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Added</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['added'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-modified-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Modified</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['changed'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-location-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Location</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: LOCATION</span>' +
                                            '</div>' +
                                        '</div>' +
                                        '<div class="row" id="' + modalId + '-status-' + Object.keys(data)[0] + '">' +
                                            '<div class="col-md-2 col-xs-12">' +
                                                '<span class="text-upper text-bold">Status</span>' +
                                            '</div>' +
                                            '<div class="col-md-10 col-xs-12" >' +
                                                '<span>: ' + data[Object.keys(data)[0]]['status'] + '</span>' +
                                            '</div>' +
                                        '</div>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>' +
                    '</div>'
                );
            } else if (select && Object.keys(data).length > 1 && Object.keys(data).length < 6) {
                var links = '';
                var linksContent = '';

                for (var selectedFileData in data) {
                    links +=
                            '<li class="nav-item">' +
                                '<a class="nav-link" id="' + modalId + '-info-content-files-tabs-' + selectedFileData + '-tab" ' +
                                'data-toggle="pill" href="#' + modalId + '-info-content-files-tabs-' + selectedFileData + '" ' +
                                'role="tab" aria-controls="' + modalId + '-info-content-files-tabs-' + selectedFileData + '" ' +
                                'aria-selected="true">' + data[selectedFileData]['entry'] + '</a>' +
                            '</li>';

                    linksContent +=
                        '<div class="tab-pane fade" id="' + modalId + '-info-content-files-tabs-' + selectedFileData + '" ' +
                        'role="tabpanel" aria-labelledby="' + modalId + '-info-content-files-tabs-' + selectedFileData + '">' +
                            '<div class="col border-bottom p-2">' +
                                '<div class="row" id="' + modalId + '-preview-' + selectedFileData + '">' +
                                    '<div class="col-md-12 text-center">' +
                                        '<i class="' + data[selectedFileData]['mime_icon'] + ' fa-6x"></i>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-name-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Name</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['entry'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-size-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Size</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['entry_size'] + ' kB</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-added-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Added</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['added'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-modified-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Modified</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['changed'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-location-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Location</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: LOCATION</span>' +
                                    '</div>' +
                                '</div>' +
                                '<div class="row" id="' + modalId + '-status-' + selectedFileData + '">' +
                                    '<div class="col-md-2 col-xs-12">' +
                                        '<span class="text-upper text-bold">Status</span>' +
                                    '</div>' +
                                    '<div class="col-md-10 col-xs-12" >' +
                                        '<span>: ' + data[selectedFileData]['status'] + '</span>' +
                                    '</div>' +
                                '</div>' +
                            '</div>' +
                        '</div>';
                }

                $('#' + modalId + '-info-content').empty().append(
                    '<div class="col card-body p-0">' +
                        '<ul class="nav nav-tabs" id="' + modalId + '-info-content-files-tabs-links" role="tablist">' +
                            links +
                        '</ul>' +
                        '<div class="tab-content" id="' + modalId + '-info-content-files-tabs-content">' +
                            linksContent +
                        '</div>' +
                    '</div>'
                    );

                $($('#' + modalId + '-info-content-files-tabs-links li a')[0]).addClass('active');
                $($('#' + modalId + '-info-content-files-tabs-content .tab-pane')[0]).addClass('active show');
            } else if (select && Object.keys(data).length > 5) { //Allow only 5 files information to be viewed at a time
                $('#' + fileId + '-info').attr('disabled', true);
            } else if (!select) {
                $('#' + modalId + '-info-content-files-tabs-' + deselectId + '-tab').parent('li').remove();
                $('#' + modalId + '-info-content-files-tabs-' + deselectId).remove();
                $($('#' + modalId + '-info-content-files-tabs-links li a')[0]).addClass('active');
                $($('#' + modalId + '-info-content-files-tabs-content .tab-pane')[0]).addClass('active show');
            }
        }
    }

    function doAjax(url, postData, method, jstreeData) {
        $.ajax({
            'url'           : url,
            'data'          : postData,
            'method'        : method,
            'dataType'      : 'json',
            'success'       : function(content) {
                if (content.storages) {
                    for (var storage in content.storages) {
                        $('#' + treeId + ' ul').append(
                            '<li data-id="' + content.storages[storage].id + '" data-description="' + content.storages[storage].description +
                                '" data-jstree=\'{"icon" : "fas fa-fw fa-hdd"}\'>' + content.storages[storage].name + '</li>'
                        );
                    }
                    // Init Jstree
                    dataCollection[componentId][sectionId][treeId]['jstree'] = $('#' + treeId).jstree(dataCollection[componentId][sectionId][treeId]);
                    // Register Get Folders on Select Event
                    $('#' + treeId).on('select_node.jstree', function(e, data) {
                        $('#' + treeId + '-info').attr('disabled', false);
                        $('#' + fileId + '-upload').attr('disabled', false);
                        $('#' + sectionId + '-dropzone-div label span').empty().append(
                            data.node.text
                            );
                        getStorageItems(data);
                    });
                } else if (content.storage_content) {
                    $.fn.dataTable.settings[0]["oLanguage"]["sEmptyTable"] = '<i class="fas fa-cog fa-spin"></i> Loading...';
                    thisOptions['datatable'].rows().clear().draw();

                    var folderData = [];
                    var filesData = [];
                    files = false;

                    for (var folderContent in content.storage_content) {
                        if (content.storage_content[folderContent].type === '1') {
                            folderData.push({
                                'data-id'           : content.storage_content[folderContent].id,
                                'data-parent_id'    : content.storage_content[folderContent].parent_id,
                                'data-added'        : content.storage_content[folderContent].added,
                                'data-changed'      : content.storage_content[folderContent].changed,
                                'text'              : content.storage_content[folderContent].entry,
                                'data-icon'         : 'fas fa-fw fa-folder',
                                'icon'              : 'fas fa-fw fa-folder',
                                'data-size'         : content.storage_content[folderContent].entry_size,
                                'data-status'       : content.storage_content[folderContent].status,
                                'data-type'         : content.storage_content[folderContent].type
                            });
                        } else if (content.storage_content[folderContent].type === '2') {
                            files = true;
                            var today = new Date();
                            if (content.storage_content[folderContent].added !== '0') {
                                dateAdded = new Date(content.storage_content[folderContent].added * 1000);
                                if (today.toDateString() === dateAdded.toDateString()) {
                                    extractDateAdded = dateAdded.getHours() + ':' + dateAdded.getMinutes() + ':' + dateAdded.getSeconds();
                                } else {
                                    extractDateAdded = dateAdded.toDateString() + ' ' +
                                        dateAdded.getHours() + ':' + dateAdded.getMinutes() + ':' + dateAdded.getSeconds();
                                }
                            }
                            if (content.storage_content[folderContent].changed !== '0') {
                                dateChanged = new Date(content.storage_content[folderContent].changed * 1000);
                                if (today.toDateString() === dateChanged.toDateString()) {
                                    extractDateChanged = dateChanged.getHours() + ':' + dateChanged.getMinutes() + ':' + dateChanged.getSeconds();
                                } else {
                                    extractDateChanged = dateChanged.toDateString() + ' ' +
                                        dateChanged.getHours() + ':' + dateChanged.getMinutes() + ':' + dateChanged.getSeconds();
                                }
                            } else {
                                extractDateChanged = extractDateAdded;
                            }

                            if (content.storage_content[folderContent].entry_size === null) {
                                dataSize = '-';
                            } else {
                                dataSize = content.storage_content[folderContent].entry_size;
                            }

                            if (content.storage_content[folderContent].status === '0' || content.storage_content[folderContent].status === '1') {
                                dataStatus = '<span class="badge badge-warning">Processing...</span>';
                            } else if (content.storage_content[folderContent].status === '2') {
                                dataStatus = '<span class="badge badge-success">Ok</span>';
                            } else if (content.storage_content[folderContent].status === '3' || content.storage_content[folderContent].status === '4') {
                                dataStatus = '<span class="badge badge-danger">Error</span>';
                            }

                            filesData.push({
                                'id'                    : content.storage_content[folderContent].id,
                                'added'                 : extractDateAdded,
                                'changed'               : extractDateChanged,
                                // 'childs'                : content.storage_content[folderContent].childs,
                                'entry'                 : content.storage_content[folderContent].entry,
                                'entry_size'            : dataSize,
                                'parent_id'             : content.storage_content[folderContent].parent_id,
                                'status'                : dataStatus,
                                'type'                  : content.storage_content[folderContent].type,
                                'mime'                  : content.storage_content[folderContent].mime
                            });
                        }
                    }
                    if (files) {
                        redrawTable(filesData);
                    } else {
                        $.fn.dataTable.settings[0]["oLanguage"]["sEmptyTable"] = 'No files in folder <strong>' + jstreeData.node.text + '</strong>';
                        $.fn.dataTable.settings[0]["oLanguage"]["select"]["rows"][0] = '';
                        thisOptions['datatable'].rows().clear().draw();
                    }

                    if ($('#' + treeId).jstree(true).get_node(jstreeData.node).children.length > 0) {
                        $('#' + treeId).
                            jstree(true).delete_node($('#' + treeId).jstree(true).get_node(jstreeData.node).children);
                        $.each(folderData, function(index,folder) {
                            $('#' + treeId).jstree(true).create_node(jstreeData.node, folder);
                        });
                    } else {
                        $.each(folderData, function(index,folder) {
                            $('#' + treeId).jstree(true).create_node(jstreeData.node, folder);
                        });
                    }

                    $('#' + treeId).jstree(true).open_node(jstreeData.node);

                    if (jstreeData.node.parent === '#') {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-hdd');
                    } else {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-folder-open');
                    }
                } else if (content.length === 0) {
                    $.fn.dataTable.settings[0]["oLanguage"]["sEmptyTable"] = 'No files in folder';
                    $.fn.dataTable.settings[0]["oLanguage"]["select"]["rows"][0] = '';

                    thisOptions['datatable'].rows().clear().draw();

                    if (jstreeData.node.parent === '#') {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-hdd');
                    } else {
                        $('#' + treeId).jstree(true).set_icon(jstreeData.node, 'fas fa-fw fa-folder-open');
                    }

                }
            },
            'complete'      : function() {
            }
        });
    }

    function registerButtons() {
        $('#' + treeId + '-info').click(function(e) {
            e.preventDefault();
            $('#' + modalId + ' .modal-header h5').empty().append(
                'Folder Information'
                );
            $('#' + sectionId + '-modal').modal('show');
        });
        $('#' + fileId + '-info').click(function(e) {
            e.preventDefault();
            $('#' + modalId + ' .modal-header h5').empty().append(
                'File(s) Information'
                );
            $('#' + sectionId + '-modal').modal('show');
        });
        $('#' + fileId + '-upload').click(function(e) {
            e.preventDefault();
            $('#' + sectionId + '-dropzone-div').removeClass('d-none');
        });
        $('#' + dropzoneId + '-close').click(function(e) {
            e.preventDefault();
            $('#' + sectionId + '-dropzone-div').addClass('d-none');
        });
    }

    function bazContentSectionWithWizard() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentSectionWithStorageConstructor) {
        BazContentSectionWithStorage = BazContentSectionWithStorageConstructor;
        BazContentSectionWithStorage.defaults = { };
        BazContentSectionWithStorage.init = function(options) {
            init(_extends(BazContentSectionWithStorage.defaults, options));
        }
    }

    setup(bazContentSectionWithWizard);


    return bazContentSectionWithWizard;
}();
$(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
    if ($('.sectionWithStorage').length > 0) {
        $('.sectionWithStorage').each(function() {
            BazContentSectionWithStorage.init({'storageId' : $(this)});
        });
    }
});
