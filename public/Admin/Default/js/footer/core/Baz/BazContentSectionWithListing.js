/* globals define exports BazContentFieldsValidator BazContentLoader Swal PNotify */
/*
* @title                    : BazContentSectionWithListing
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithListing;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    'use strict';
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {
    'use strict';

    var BazContentSectionWithListing = function ($) {

        var NAME                    = 'BazContentSectionWithListing';
        var DATA_KEY                = 'baz.contentsectionslist';
        // var EVENT_KEY               = "." + DATA_KEY;
        var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        var Default = {
            task                    : null
        };
        var dataCollection,
            componentId,
            sectionId,
            pnotifySound,
            classes,
            swalSound;
        var listColumns = { };
            // that;

        var BazContentSectionWithListing = function () {
            function BazContentSectionWithListing(element, settings) {
                // that = this;
                this._element = element;
                this._settings = settings;
            }

            var _proto = BazContentSectionWithListing.prototype;

            _proto._error = function _error(message) {
                throw new Error(message);
            };

            _proto._init = function _init(options) {
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;

                dataCollection = window['dataCollection'];
                pnotifySound = new Audio(dataCollection.env.soundPath + 'pnotify.mp3');
                swalSound = new Audio(dataCollection.env.soundPath + 'swal.mp3');

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                if ($(this._element).is('.sectionWithListingFilter')) {
                    $(this._element).BazContentFields();

                    BazContentFieldsValidator.initValidator({
                        'componentId'   : componentId,
                        'sectionId'     : sectionId,
                        'on'            : 'section'
                    });

                    this._buildListingFilters(options);
                }

                if ($(this._element).is('.sectionWithListingDatatable')) {
                    this._buildListingDatatable(options);
                }
            };

            //Build listing filters
            _proto._buildListingFilters = function() {

                //Buttons
                $('#' + sectionId + '-filters').change(function() {
                    if ($('#' + sectionId + '-filters option:selected').val() === "0") {

                        $('#' + sectionId + '-edit, ' +
                          '#' + sectionId + '-delete, ' +
                          '#' + sectionId + '-share, ' +
                          '#' + sectionId + '-apply'
                        ).attr("disabled", true);

                    } else {
                        $('#' + sectionId + '-apply').attr("disabled", false);

                        if ($('#' + sectionId + '-filters option:selected').data('permission') === 0) {
                            $('#' + sectionId + '-edit, ' +
                              '#' + sectionId + '-delete, ' +
                              '#' + sectionId + '-share'
                            ).attr("disabled", true);

                        } else if ($('#' + sectionId + '-filters option:selected').data('permission') === 1) {
                            //User
                            $('#' + sectionId + '-edit, ' +
                              '#' + sectionId + '-delete, ' +
                              '#' + sectionId + '-share'
                            ).attr("disabled", false);
                        }
                    }
                });

                //Add
                $('#' + sectionId + '-add').click(function() {
                    $('#' + sectionId + '-filter-modal').modal('show');
                });

                $('#' + sectionId + '-name').keyup(function() {
                    if ($(this).val() !== '') {
                        $('#' + sectionId + '-filter-save, #' + sectionId + '-filter-saveapply').attr('disabled', false);
                    } else {
                        $('#' + sectionId + '-filter-save, #' + sectionId + '-filter-saveapply').attr('disabled', true);
                    }
                });

                //Add Save
                //Add Apply (Temp)

                //Edit
                //Edit Save

                //Delete

                //Share
                //Select Guid and Uids
            }

            //Build listing datatable
            _proto._buildListingDatatable = function() {
                // For checkbox Sorting
                $.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col ) {
                    return this.api().column( col, {order:'index'} ).nodes().map( function ( td ) {
                        return $('input', td).prop('checked') ? '1' : '0';
                    } );
                };
                // For radio sorting
                $.fn.dataTable.ext.order['dom-radio'] = function ( settings, col ) {
                    return this.api().column( col, {order:'index'} ).nodes().map( function ( td ) {
                        return $('input[type=radio]:checked', td).prop('checked') ? '1' : '0';
                    } );
                };

                var thisOptions = dataCollection[componentId][sectionId];
                listColumns[thisOptions.listOptions.tableName] = [];

                var datatableOptions = thisOptions.listOptions.datatable;
                datatableOptions.columns = JSON.parse(datatableOptions.columns);

                var selectOptions, dom, showHideExportButton, showHideColumnsButton;

                if (datatableOptions.showHideColumnsButton === 'true' ||
                    datatableOptions.showHideColumnsButton === '1' ||
                    datatableOptions.showHideExportButton === 'true' ||
                    datatableOptions.showHideExportButton === '1') {
                    dom =
                    '<"row mb-1"<"col-md-3 col-xs-12"B><"col-md-3 col-xs-12"l><"col-md-3 col-xs-12"f><"col-md-3 col-xs-12"p>>' +
                    '<"row mb-1"<"col"tr>>' +
                    '<"row"<"col-md-6 col-xs-12"i><"col-md-6 col-xs-12"p>>';
                } else {
                    dom =
                    '<"row mb-1"<"col-md-4 col-xs-12"l><"col-md-4 col-xs-12"f><"col-md-4 col-xs-12"p>>' +
                    '<"row mb-1"<"col"tr>>' +
                    '<"row"<"col-md-6 col-xs-12"i><"col-md-6 col-xs-12"p>>';
                }

                // ID Column
                if (datatableOptions.addIdColumn === 'true' || datatableOptions.addIdColumn === '1') {
                    if (!datatableOptions.columns.find(({name}) => name === 'id')) {
                        listColumns[thisOptions.listOptions.tableName].push({
                            data    : 'id',
                            title   : 'ID'
                        });
                    }
                }

                var columnsCount = 0;

                // All Columns (except ID and __control and replacedColumns)
                $.each(datatableOptions.columns[0], function(index,column) {
                    var disabled;

                    // disable column ordering
                    var disableColumnOrdering = datatableOptions.disableColumnsOrdering.includes(column.id);
                    if (disableColumnOrdering) {
                        disabled = false;
                    } else {
                        disabled = true;
                    }

                    if (datatableOptions.colTextTruncate) {
                        classes = 'data-' + column.id + ' text-truncate dt-colTextTruncate';
                    } else {
                        classes = 'data-' + column.id;
                    }

                    if (datatableOptions.tableCompact) {
                        classes = classes + ' pb-1 pt-1';
                    }

                    listColumns[thisOptions.listOptions.tableName].push({
                        data            : column.id,
                        title           : column.name.toUpperCase(),
                        orderable       : disabled,
                        className       : classes
                    });
                    columnsCount++;
                });

                // Hide Columns
                var hideColumns = [];
                if (datatableOptions.hideIdColumn === 'true' || datatableOptions.hideIdColumn === '1') {
                    hideColumns.push(0);
                }

                if (columnsCount > datatableOptions.NoOfColumnsToShow) {
                    var colDiff = columnsCount - datatableOptions.NoOfColumnsToShow;
                    for (var i = 1; i <= colDiff; i++) {
                        hideColumns.push(columnsCount - i);
                    }
                }

                // Column Select
                if (datatableOptions.select === 'true' || datatableOptions.select === '1') {
                    selectOptions = {
                        style       : datatableOptions.selectStyle,
                        className   : 'bg-lightblue'
                    }
                    //Add class datatable-pointer to each row.
                    datatableOptions.createdRow = function (row) {
                        $(row).addClass("dataTable-pointer");
                    }
                } else {
                    selectOptions = false;
                }
                if (datatableOptions.selectAll === 'true' || datatableOptions.selectAll === '1') {
                    var selectAllNoneButton = ['selectAll','selectNone'];
                }

                // Column reorder disallow column 1st (id) and last (__control)
                if (datatableOptions.colReorder === 'true' || datatableOptions.colReorder === '1') {
                    datatableOptions.colReorder = $.extend(datatableOptions.colReorder, {
                        fixedColumnsLeft    : 1,//id
                        fixedColumnsRight   : 1,//__control
                    });
                } else {
                    datatableOptions.colReorder = false;
                }

                if (datatableOptions.stateSave === 'true' || datatableOptions.stateSave === '1') {
                    datatableOptions.stateSave = true;
                } else {
                    datatableOptions.stateSave = false;
                }

                if (datatableOptions.fixedHeader === 'true' || datatableOptions.fixedHeader === '1') {
                    datatableOptions.fixedHeader = true;
                } else {
                    datatableOptions.fixedHeader = false;
                }

                if (datatableOptions.searching === 'true' || datatableOptions.searching === '1') {
                    datatableOptions.searching = true;
                } else {
                    datatableOptions.searching = false;
                }

                if (datatableOptions.paging === 'true' || datatableOptions.paging === '1') {
                    datatableOptions.paging = true;
                } else {
                    datatableOptions.paging = false;
                }

                if (datatableOptions.showHideColumnsButton === 'true' || datatableOptions.showHideColumnsButton === '1') {
                    showHideColumnsButton =
                        {
                            extend          : 'colvis',
                            text            : function() {
                                                var totCols = listColumns[thisOptions.listOptions.tableName].length;
                                                var hiddenCols = hideColumns.length;
                                                var shownCols = totCols - hiddenCols;
                                                return '<i class="fas fa-eye fa-fw"></i> (' + shownCols + '/' + totCols + ')';
                                            },
                            className       : 'btn-sm btn-' + datatableOptions.showHideColumnsButtonType,
                            prefixButtons   : [{
                                                extend      : 'colvisGroup',
                                                text        : 'SHOW ALL',
                                                show        : ':hidden'
                                            },
                                            {
                                                extend      : 'colvisRestore',
                                                text        : 'RESTORE'
                                            }]
                        }
                }

                if (datatableOptions.showHideExportButton === 'true' || datatableOptions.showHideExportButton === '1') {
                    showHideExportButton =
                        {
                            extend          : 'collection',
                            text            : 'Export',
                            className       : 'btn-sm btn-' + datatableOptions.showHideColumnsButtonType,
                            buttons         : [{
                                                text            : 'Excel',
                                                title           : 'DataExport - ' + thisOptions.listOptions.componentName,
                                                extend          : 'excelHtml5',
                                                footer          : false,
                                                exportOptions   : {
                                                                    columns: ':visible'
                                                                }
                                            },
                                            {
                                                text            : 'CSV',
                                                extend          : 'csvHtml5',
                                                fieldSeparator  : ',',
                                                exportOptions   : {
                                                                    columns: ':visible'
                                                                }
                                            }
                                            ]
                        }
                }

                datatableOptions = $.extend(datatableOptions, {
                    columns         : listColumns[thisOptions.listOptions.tableName],
                    rowId           : 'id',
                    colReorder      : datatableOptions.colReorder,
                    stateSave       : datatableOptions.stateSave,
                    fixedHeader     : datatableOptions.fixedHeader,
                    searching       : datatableOptions.searching,
                    responsive      : datatableOptions.responsive,
                    paging          : datatableOptions.paging,
                    lengthMenu      : datatableOptions.lengthMenu,
                    select          : selectOptions,
                    columnDefs      : [{
                                        visible         : false,
                                        targets         : hideColumns
                                    }],
                    dom             : dom,
                    buttons         : [showHideColumnsButton, showHideExportButton, selectAllNoneButton],
                    language       : {
                                        paginate        : {
                                                            previous    : '<i class="fa fa-angle-left"></i>',
                                                            next        : '<i class="fas fa-angle-right"></i>'
                                                        },
                                        zeroRecords     : datatableOptions.zeroRecords,
                                        infoEmpty       : 'No entries found',
                                        searchPlaceholder: 'Search shown ' + thisOptions.listOptions.componentName + '...',
                                        select          : {
                                            rows    : {
                                                    _   : 'Selected %d rows. Click the selected row again to deselect',
                                                    0   : '<i class="fas fa-fw fa-info-circle"></i>Click a row to select it',
                                                    1   : 'Selected 1 row. Click the selected row again to deselect'
                                                }
                                        },
                                        buttons         : {
                                            selectAll   : '<i class="fas fa-fw fa-xs fa-check-double"></i>',
                                            selectNone  : '<span class="fa-stack">' +
                                                          '<i class="fas fa-fw fa-xs fa-check-double fa-stack-1x"></i>' +
                                                          '<i class="fas fa-fw fa-sm fa-ban fa-stack-2x text-danger"></i>' +
                                                          '</span>'
                                        }
                                    },
                    initComplete    : function() {
                                        //Remove button secondary
                                        $('.btn-' + datatableOptions.showHideColumnsButtonType).removeClass('btn-secondary');

                                        // Adjust hidden columns counter text in button
                                        $('#' + sectionId + '-table').on('column-visibility.dt', function(e) {
                                            var visCols = $('#' + sectionId + '-table thead tr:first th').length;
                                            //Below: The minus 2 because of the 2 extra buttons SHOW ALL and RESTORE
                                            var tblCols = $('.dt-button-collection a').length - 2;
                                            $('.buttons-colvis[aria-controls=' + sectionId + '-table] span').html('<i class="fa fa-eye fa-fw"></i> (' + visCols + '/' + tblCols + ')');
                                            thisOptions['datatable'].responsive.recalc();
                                            e.stopPropagation();
                                        });
                                    },
                    drawCallback    : function() {
                                        drawCallback();
                                    }
                });

                if (thisOptions.listOptions.postUrl) {
                    runDatatableAjax(thisOptions.listOptions.postParams);
                } else {
                    // Enable paging if data is more than 20 on static datatable
                    if (datatableOptions.pagination && datatableOptions.paginationCounters.total_items > 20) {
                        $.extend(thisOptions.listOptions.datatable, {
                            paging : true,
                        });
                    }
                    $('#list-data-loader').hide();
                    tableInit(false);
                    registerEvents();
                }

                function runDatatableAjax(postData, reDraw) {
                    var url = thisOptions.listOptions.postUrl;
                    $.ajax({
                        url         : url,
                        method      : 'post',
                        dataType    : 'json',
                        data        : postData,
                        success     : function(data) {
                            $('#list-data-loader').hide();
                            $.extend(thisOptions.listOptions.datatable, JSON.parse(data.rows));
                        }
                    }).done(function() {
                        tableInit(reDraw);
                        registerEvents();
                    });
                        // TODO: fix card-body height when more rows are loaded.
                        // TODO: BULK Edit/Delete
                        //     // tableData[sectionId].buttons().container().appendTo('#products-list-buttons .col-sm-6:eq(0)');
                        //     // that.fixHeight('fixedHeight');
                        //     // $('#' + sectionId + '-list-table').on('length.dt', function() {
                        //     //     that.fixHeight('fixedHeight');
                        //     // });
                        //     // $('#' + sectionId + '-filter').on('collapsed.boxwidget expanded.boxwidget', function() {
                        //     //     that.fixHeight('fixedHeight');
                        //     // });
                        //     // $('#' + sectionId + '-filter-filters-apply').click(function() {
                        //     //     $('#' + sectionId + '-filter .box').trigger('collapse.boxwidget');
                        //     //     that.fixHeight('fixedHeight');
                        //     // });
                        //     // $('#' + sectionId + '-list').find('.dataTables_info').addClass('pull-right');
                        //     //  }
                        //     // });
                    // });
                }

                //Initialize Table
                function tableInit(reDraw) {
                    // All Columns (except ID and __control)
                    $.each(listColumns, function(index,column) {
                        // Ordering of checkbox and radio columns
                        for (var replaceColumn in datatableOptions.replaceColumns) {
                            if (replaceColumn === column.data) {
                                if (datatableOptions.replaceColumns[replaceColumn] === 'customSwitch') {
                                    column.orderDataType = 'dom-checkbox';
                                } else if (datatableOptions.replaceColumns[replaceColumn] === 'radioButtons') {
                                    column.orderDataType = 'dom-radio';
                                }
                            }
                        }
                    });

                    if (!reDraw) {
                        // Pagination
                        if (datatableOptions.pagination && datatableOptions.paginationCounters.total_items > 20) {
                            $.extend(thisOptions.listOptions.datatable, {
                                paging : true,
                                pagingType : 'simple',
                            });

                            datatableOptions['language']['zeroRecords'] = '<i class="fas fa-cog fa-spin"></i> Loading...';
                        }
                        if (datatableOptions.tableCompact) {
                            classes = 'data-actions pb-1 pt-1';
                        } else {
                            classes = 'data-actions';
                        }
                        // Control Column
                        if (datatableOptions.rowControls) {
                            listColumns[thisOptions.listOptions.tableName].push({
                                data        : '__control',
                                title       : 'ACTIONS',
                                orderable   : false,
                                className   : classes
                            });
                        }

                        if (thisOptions.customFunctions.beforeTableInit) {
                            thisOptions.customFunctions.beforeTableInit();
                        }

                        thisOptions['datatable'] = $('#' + thisOptions.listOptions.tableName).DataTable(datatableOptions);

                        if (thisOptions.customFunctions.afterTableInit) {
                            thisOptions.customFunctions.afterTableInit();
                        }

                        thisOptions['datatable'].columns.adjust().responsive.recalc();

                        updateCounters();

                        // Datatable Events
                        //Responsive
                        thisOptions['datatable'].on('draw responsive-resize responsive-display', function() {
                            BazContentLoader.init({});
                            thisOptions['datatable'].columns.adjust().responsive.recalc();
                        });

                        //Toggle response rows open/close
                        thisOptions['datatable'].on('responsive-display', function(e, datatable, row, showHide) {
                            if (showHide) {
                                $($(row.node()).next('.child')).find('li').prepend(
                                    '<i class="fa fas fa-fw fa-plus-circle text-info dtr-expand mr-1 dataTable-pointer"><i>'
                                );

                                changeResponsiveLiWidths($($(row.node()).next('.child')));

                                $($(row.node()).next('.child')).find('.dtr-expand').click(function() {
                                    if ($(this).parent().is('.text-truncate')) {
                                        $(this).parent().removeClass('text-truncate dt-colTextTruncate');
                                        $(this).removeClass('fa-plus-circle text-info').addClass('fa-minus-circle text-danger');
                                    } else {
                                        $(this).parent().addClass('text-truncate dt-colTextTruncate');
                                        $(this).removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-info');
                                        changeResponsiveLiWidths($($(row.node()).next('.child')));
                                    }
                                });
                            }
                        });

                        //Search
                        thisOptions['datatable'].on('draw', function () {
                            if ($('#' + sectionId + '-table tbody td.dataTables_empty').length === 1) {
                                $('.dataTables_empty').html('...');
                            }
                        });

                        //Length Change
                        thisOptions['datatable'].on('length.dt', function (e, settings, len) {
                            if (len === -1) {
                                len = datatableOptions.paginationCounters.total_items;
                            }
                            thisOptions['datatable'].rows().clear().draw();
                            $('.dataTables_empty').html('<i class="fas fa-cog fa-spin"></i> Loading...');
                            runDatatableAjax({
                                'page' : datatableOptions.paginationCounters.first,
                                'limit': len
                            }, true);
                        });

                    } else { //redraw used on pagination prev and next

                        if (thisOptions.customFunctions.beforeRedraw) {
                            thisOptions.customFunctions.beforeRedraw();
                        }

                        thisOptions['datatable'].rows.add(datatableOptions.data).draw();

                        if (thisOptions.customFunctions.afterRedraw) {
                            thisOptions.customFunctions.afterRedraw();
                        }

                        updateCounters();
                    }

                    if (datatableOptions.rowControls) {
                        BazContentLoader.init({});
                    }
                }

                //Update width for open child
                function changeResponsiveLiWidths(child) {
                    var width = child.width();

                    var titleWidth = ((20 * width) / 100) / 16;//rem
                    var liWidth = width/16;

                    child.find('.dtr-title').css({"width" : titleWidth + 'rem'});
                    child.find('.dtr-title').parent('li').removeClass('dt-colTextTruncate');
                    child.find('.dtr-data').parent('li').css({"width" : liWidth + 'rem'});
                }

                //Register __control(Action buttons)
                function registerEvents() {
                    // customSwitch Toggle Function
                    $('#' + sectionId + '-table .custom-switch input').each(function(index,rowSwitchInput) {
                        $(rowSwitchInput).click(function() {
                            var rowSwitchInputId = $(rowSwitchInput)[0].id;
                            var url = dataCollection.env.rootPath + 'index.php?route=' + $(rowSwitchInput).data('switchactionurl');
                            var columnId = $(rowSwitchInput).data('columnid');
                            var checked = $(rowSwitchInput).is('[checked]');
                            var columnsDataToInclude = $(rowSwitchInput).data('switchactionincludecolumnsdata').split(',');
                            var rowData;
                            rowData = thisOptions['datatable'].row($(this).parents('tr')).data();
                            if (checked) {
                                rowData[columnId] = 0;
                                $(rowSwitchInput).attr('checked', false);
                                document.getElementById(rowSwitchInputId).checked = false;
                            } else {
                                rowData[columnId] = 1;
                                $(rowSwitchInput).attr('checked', true);
                                document.getElementById(rowSwitchInputId).checked = true;
                            }
                            var name = $(rowSwitchInput).parents('td').siblings('.data-' + $(rowSwitchInput).data('notificationtextfromcolumn')).html();
                            var switchOnText = name + ' enabled';
                            var switchOffText = name + ' disabled';
                            if (checked) {
                                PNotify.removeAll();
                                Swal.fire({
                                    title                       : '<i class="fa text-danger fa-lg fa-question-circle m-2"></i>' +
                                                                  ' <span style="font-size:40px;" class="text-danger"> Disable ' +
                                                                   name + '?</span>',
                                    width                       : '100%',
                                    background                  : 'rgba(0,0,0,.8)',
                                    backdrop                    : 'rgba(0,0,0,.6)',
                                    animation                   : false,
                                    customClass                 : 'rounded-0 animated fadeIn',
                                    buttonsStyling              : false,
                                    confirmButtonClass          : 'btn btn-danger text-uppercase',
                                    confirmButtonText           : 'Disable',
                                    cancelButtonClass           : 'ml-2 btn btn-default text-uppercase',
                                    showCancelButton            : true,
                                    keydownListenerCapture      : true,
                                    allowOutsideClick           : false,
                                    allowEscapeKey              : false,
                                    allowEnterKey               : false,
                                    onOpen                      : function() {
                                        swalSound.play();
                                    }
                                }).then((result) => {
                                    if (result.value) {
                                        runAjax(false, switchOffText);
                                    } else {
                                        $(rowSwitchInput).attr('checked', true);
                                        document.getElementById(rowSwitchInputId).checked = true;
                                    }
                                });
                            } else {
                                runAjax(true, switchOnText);
                            }
                            function runAjax(status, notificationText) {
                                var dataToSubmit = { };
                                for (var data in rowData) {
                                    if (columnsDataToInclude.includes(data)) {
                                        dataToSubmit[data] = rowData[data];
                                    }
                                }
                                $.ajax({
                                    url         : url,
                                    method      : 'post',
                                    data        : dataToSubmit,
                                    dataType    : 'json',
                                    success     : function(response) {
                                        if (response.status === 0) {
                                            PNotify.removeAll();
                                            PNotify.success({
                                                title           : notificationText,
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $(rowSwitchInput).attr('checked', status);
                                            document.getElementById(rowSwitchInputId).checked = true;
                                        } else {
                                            PNotify.removeAll();
                                            PNotify.error({
                                                title           : 'Error!',
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $(rowSwitchInput).attr('checked', false);
                                            document.getElementById(rowSwitchInputId).checked = false;
                                        }
                                        pnotifySound.play();
                                    }
                                });
                            }
                        });
                    });

                    // RadioButtons
                    $('#' + sectionId + '-table .btn-group-toggle label').each(function(index,radioButtonsLabel) {
                        $(radioButtonsLabel).click(function() {
                            var currentCheckedId, currentCheckedLabel;
                            $(this).siblings('label').children('input').each(function(index,sibling) {
                                if (sibling.checked) {
                                    currentCheckedId = sibling.id;
                                    currentCheckedLabel = sibling.parentElement;
                                } else if (sibling.defaultChecked) {
                                    currentCheckedId = sibling.id;
                                    currentCheckedLabel = sibling.parentElement;
                                }
                            });
                            var thisId = $(this).children('input')[0].id;
                            var url = dataCollection.env.rootPath + 'index.php?route=' + $(this).children('input').data('radiobuttonsactionurl');
                            var columnId = $(this).children('input').data('columnid');
                            var dataValue = $(this).children('input').data('value');
                            var checked = false;
                            if ($(this).children('input').is('[checked]') || $(this).children('input')[0].defaultChecked) {
                                checked = true;
                            }
                            var radioChangeText = $(this).parents('td').siblings('.data-' + $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                                                    $(this).children('input').data('columnid') + ' changed';
                            if (!checked) {
                                PNotify.removeAll();
                                Swal.fire({
                                    title                       : '<i class="fa text-danger fa-lg fa-question-circle m-2"></i>' +
                                                                    ' <span style="font-size:40px;" class="text-danger"> Change ' +
                                                                    $(this).parents('td').siblings('.data-' +
                                                                        $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                                                                    $(this).children('input').data('columnid') +
                                                                    '?</span>',
                                    width                       : '100%',
                                    background                  : 'rgba(0,0,0,.8)',
                                    backdrop                    : 'rgba(0,0,0,.6)',
                                    animation                   : false,
                                    customClass                 : 'rounded-0 animated fadeIn',
                                    buttonsStyling              : false,
                                    confirmButtonClass          : 'btn btn-danger text-uppercase',
                                    confirmButtonText           : 'Change',
                                    cancelButtonClass           : 'ml-2 btn btn-default text-uppercase',
                                    showCancelButton            : true,
                                    keydownListenerCapture      : true,
                                    allowOutsideClick           : false,
                                    allowEscapeKey              : false,
                                    allowEnterKey               : false,
                                    onOpen                      : function() {
                                        swalSound.play();
                                    }
                                }).then((result) => {
                                    if (result.value) {
                                        runAjax(false, radioChangeText);
                                    } else {
                                        $(this).removeClass('focus active');
                                        $('#' + currentCheckedId).attr('checked', true);
                                        document.getElementById(currentCheckedId).checked = true;
                                        $(currentCheckedLabel).addClass('focus active');
                                    }
                                });
                            }

                            function runAjax(status, notificationText) {
                                var columnsDataToInclude = $('#' + thisId).data('radiobuttonsactionincludecolumnsdata').split(',');
                                var rowData = thisOptions['datatable'].row($('#' + thisId).parents('tr')).data();
                                var dataToSubmit = { };
                                for (var data in rowData) {
                                    if (columnsDataToInclude.includes(data)) {
                                        if (columnId === data) {
                                            dataToSubmit[data] = dataValue;
                                        } else {
                                            dataToSubmit[data] = rowData[data];
                                        }
                                    }
                                }
                                $.ajax({
                                    url         : url,
                                    method      : 'post',
                                    data        : dataToSubmit,
                                    dataType    : 'json',
                                    success     : function(response) {
                                        if (response.status === 1) {
                                            PNotify.removeAll()
                                            PNotify.success({
                                                title           : notificationText,
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $('#' + currentCheckedId).attr('checked', false);
                                            document.getElementById(currentCheckedId).checked = false;
                                            $('#' + thisId).attr('checked', true);
                                            document.getElementById(thisId).checked = true;
                                        } else {
                                            PNotify.removeAll();
                                            PNotify.error({
                                                title           : 'Error!',
                                                cornerClass     : 'ui-pnotify-sharp'
                                            });
                                            $('#' + thisId).parent('label').removeClass('focus active');
                                            $('#' + thisId).attr('checked', false);
                                            document.getElementById(thisId).checked = false;
                                            $('#' + currentCheckedId).attr('checked', true);
                                            document.getElementById(currentCheckedId).checked = true;
                                            $(currentCheckedLabel).addClass('focus active');
                                        }
                                        pnotifySound.play();
                                    }
                                });
                            }
                        });
                    });

                    // Deleting Row (element .rowRemove)
                    $('#' + sectionId + '-table .rowRemove').each(function(index,rowRemove) {
                        $(rowRemove).click(function(e) {
                            e.preventDefault();
                            var thisButton = this;
                            var url = $(this).attr('href');
                            var deleteText = $(this).parents('td').siblings('.data-' + $(this).data('notificationtextfromcolumn')).html();
                            var dataToSend = { };
                            dataToSend.id = thisOptions['datatable'].row($(thisButton).parents('tr')).id();
                            Swal.fire({
                                title                       : '<i class="fa text-danger fa-lg fa-question-circle m-2">' +
                                                              '</i> <span style="font-size:40px;" class="text-danger"> Delete ' +
                                                              deleteText + '?</span>',
                                width                       : '100%',
                                background                  : 'rgba(0,0,0,.8)',
                                backdrop                    : 'rgba(0,0,0,.6)',
                                animation                   : false,
                                customClass                 : 'rounded-0 animated fadeIn',
                                buttonsStyling              : false,
                                confirmButtonClass          : 'btn btn-danger text-uppercase',
                                confirmButtonText           : 'Delete',
                                cancelButtonClass           : 'ml-2 btn btn-default text-uppercase',
                                showCancelButton            : true,
                                keydownListenerCapture      : true,
                                allowOutsideClick           : false,
                                allowEscapeKey              : false,
                                allowEnterKey               : false,
                                onOpen                      : function() {
                                    swalSound.play();
                                }
                            }).then((result) => {
                                if (result.value) {
                                    if (datatableOptions.sendConfirmRemove === 'true' || datatableOptions.sendConfirmRemove === '1') {
                                        dataToSend.confirm = '1';
                                    }
                                    $.ajax({
                                        url         : url,
                                        method      : 'post',
                                        dataType    : 'json',
                                        data        : dataToSend,
                                        success     : function(response) {
                                            if (response.status === 0) {
                                                PNotify.removeAll();
                                                PNotify.success({
                                                    title           : deleteText + ' deleted.',
                                                    cornerClass     : 'ui-pnotify-sharp'
                                                });
                                                // remove row on success
                                                thisOptions['datatable'].row($(thisButton).parents('tr')).remove().draw();
                                            } else {
                                                PNotify.removeAll();
                                                PNotify.error({
                                                    title           : 'Error!',
                                                    cornerClass     : 'ui-pnotify-sharp'
                                                });
                                            }
                                            pnotifySound.play();
                                        }
                                    });
                                }
                            });
                        });
                    });
                }

                function updateCounters() {
                    var counters = { };

                    counters.total = datatableOptions.paginationCounters.total_items;
                    counters.end = datatableOptions.paginationCounters.limit * datatableOptions.paginationCounters.current;
                    counters.start = (counters.end - datatableOptions.paginationCounters.limit) + 1;

                    if (datatableOptions.paginationCounters.current === datatableOptions.paginationCounters.last) {
                        counters.end = datatableOptions.paginationCounters.total_items;
                    }
                    if (thisOptions['datatable']) {
                        $('#admin-users-listing-table_info').empty().html(
                            "Showing " + counters.start + " to " + counters.end +" of " + counters.total + " entries"
                        );
                    } else {
                        $('#admin-users-listing-table_info').empty().html(
                            "Showing " + counters.start + " to " + counters.end +" of " + counters.total + " entries"
                        );
                    }
                }

                function drawCallback() {
                    if (datatableOptions.pagination &&
                        datatableOptions.paginationCounters.total_items > 20 &&
                        (datatableOptions.paginationCounters.total_items !== datatableOptions.paginationCounters.limit)
                    ) {

                        if (datatableOptions.paginationCounters.current !== datatableOptions.paginationCounters.first) {
                            $('.paginate_button.previous').removeClass('disabled');
                            $('.paginate_button.previous').click(function() {
                                thisOptions['datatable'].rows().clear().draw();
                                $('.dataTables_empty').html('<i class="fas fa-cog fa-spin"></i> Loading...');
                                runDatatableAjax({
                                    'page' : datatableOptions.paginationCounters.previous,
                                    'limit': datatableOptions.paginationCounters.limit
                                }, true);
                            });
                        }
                        if (datatableOptions.paginationCounters.current !== datatableOptions.paginationCounters.last) {
                            $('.paginate_button.next').removeClass('disabled');
                            $('.paginate_button.next').click(function() {
                                thisOptions['datatable'].rows().clear().draw();
                                $('.dataTables_empty').html('<i class="fas fa-cog fa-spin"></i> Loading...');
                                runDatatableAjax({
                                    'page' : datatableOptions.paginationCounters.next,
                                    'limit': datatableOptions.paginationCounters.limit
                                }, true);
                            });
                        }
                    }
                }
            };

            BazContentSectionWithListing._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;

                dataCollection[componentId][sectionId]['BazContentSectionWithListing'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentSectionWithListing']) {
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing'] = new BazContentSectionWithListing($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentSectionWithListing'];
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing'] = new BazContentSectionWithListing($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithListing']._init(options);
                }
            };

        return BazContentSectionWithListing;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentWizardAjaxComplete', function() {
        if ($('.sectionWithListingFilter').length > 0) {
            $('.sectionWithListingFilter').each(function() {
                BazContentSectionWithListing._jQueryInterface.call($(this));
            });
        }
        if ($('.sectionWithListingDatatable').length > 0) {
            $('.sectionWithListingDatatable').each(function() {
                BazContentSectionWithListing._jQueryInterface.call($(this));
            });
        }
    });

    $.fn[NAME] = BazContentSectionWithListing._jQueryInterface;
    $.fn[NAME].Constructor = BazContentSectionWithListing;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentSectionWithListing._jQueryInterface;
    };

    return BazContentSectionWithListing;
}(jQuery);

exports.BazContentSectionWithListing = BazContentSectionWithListing;

Object.defineProperty(exports, '__esModule', { value: true });

}));