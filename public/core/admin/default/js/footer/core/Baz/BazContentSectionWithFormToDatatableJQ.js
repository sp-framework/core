/* globals define exports BazContentFieldsValidator BazContentFieldsValidator PNotify */
/*
* @title                    : BazContentSectionWithFormToDatatable
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithFormToDatatable;
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

    var BazContentSectionWithFormToDatatable = function ($) {

        var NAME                    = 'BazContentSectionWithFormToDatatable';
        var DATA_KEY                = 'baz.contentsectionwithform';
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
            that,
            sectionsOptions,
            extractDatatableFieldsData,
            multiTable,
            selectedTable,
            dataTableFields,
            tableData,
            pnotifySound;

        var BazContentSectionWithFormToDatatable = function () {
            function BazContentSectionWithFormToDatatable(element, settings) {
                that = this;
                that._element = element;
                that._settings = settings;
            }

            var _proto = BazContentSectionWithFormToDatatable.prototype;

            _proto._error = function _error(message) {
                throw new Error(message);
            };

            _proto._init = function _init(options) {
                pnotifySound = new Audio(dataCollection.env.soundPath + 'pnotify.mp3'); //Error Sound for Swal
                componentId = $(this._element).parents('.component')[0].id;
                sectionId = $(this._element)[0].id;

                dataTableFields = { };
                tableData = { };
                dataTableFields[componentId] = { };
                dataTableFields[componentId][sectionId] = { };
                tableData[sectionId] = { };

                sectionsOptions = dataCollection[componentId][sectionId];
                //eslint-disable-next-line
                // console.log(sectionsOptions);

                dataCollection = window['dataCollection'];

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                if (options.task === 'tableDataToObj') {
                    this._tableDataToObj();
                } else {
                    $(this._element).BazContentFields();

                    BazContentFieldsValidator.initValidator({
                        'componentId'   : componentId,
                        'sectionId'     : sectionId,
                        'on'            : 'section'
                    });

                    this._fieldsToDatatable(sectionId);
                }

            }

            _proto._validateForm = function (onSuccess, type, preValidated, formId) {

                var validated = BazContentFieldsValidator.validateForm({
                    'componentId'     : componentId,
                    'sectionId'       : sectionId,
                    'onSuccess'       : onSuccess,
                    'type'            : type,
                    'preValidated'    : preValidated,
                    'formId'          : formId
                });

                return validated;
            };

            //Fields to Datatable
            _proto._fieldsToDatatable = function(fieldsetDatatable) {
                var addSeq = [];

                var columnDefsObj =
                    [
                        { 'targets': 0, 'visible': false },
                        { 'orderable': true, 'className': 'reorder', 'targets': 0 },
                        { 'orderable': false, 'targets': '_all' }
                    ];

                if (sectionsOptions[fieldsetDatatable + '-datatables']) {
                    $.each(sectionsOptions[fieldsetDatatable + '-datatables'], function(i,v) {
                        if (sectionsOptions[v].datatable.rowReorder) {
                            if (!sectionsOptions[v].datatable.columnDefs) {
                                sectionsOptions[v].datatable.columnDefs = [];
                                sectionsOptions[v].datatable.columnDefs = columnDefsObj;
                            } else {
                                sectionsOptions[v].datatable.columnDefs = $.merge(columnDefsObj, sectionsOptions[v].datatable.columnDefs);
                            }
                            addSeq.push('true');
                        }
                    });
                } else {
                    that._error('Datatable Parameters missing for datatable - ' + fieldsetDatatable);
                }
                if ($.inArray('true', addSeq) !== -1) {
                    $('#' + fieldsetDatatable + '-fields').prepend(
                        '<div class="row margin-top-10 d-none">' +
                        '   <div class="col-md-12">' +
                        '       <label>SEQ</label>' +
                        '       <div data-bazScan="true" data-bazScanType="seq" id="' + fieldsetDatatable + '-seq"></div>' +
                        '   </div>' +
                        '</div>' +
                        '<div class="row margin-top-10 d-none">' +
                        '   <div class="col-md-12">' +
                        '       <label>SORT</label>' +
                        '       <div data-bazScan="true" data-bazScanType="html" id="' + fieldsetDatatable + '-sort"><i class="fa fa-sort"></i></div>' +
                        '   </div>' +
                        '</div>'
                    );
                }

                $(sectionsOptions[fieldsetDatatable + '-datatables']).each(function(i,v) {
                    // Generate table th
                    var extractDatatableFieldsLabel = that._extractDatatableFieldsLabel(fieldsetDatatable, v);
                    var labels = [];
                    for (var label in extractDatatableFieldsLabel) {
                        labels.push('<th class="pb-1 pt-1">' + extractDatatableFieldsLabel[label].labelName + '</th>');
                    }
                    labels = labels.join('');
                    $('#' + v).append(
                        '<div class="margin-bottom-10" id="' + v + '-div">' +
                        '<label class="text-uppercase">' + sectionsOptions[v].tableTitle + '</label> '+
                        ' <i data-toggle="tooltip" data-placement="right" title="' + sectionsOptions[v].tableTitle + ' table" class="fa fa-question-circle fa-1 helper"></i>' +
                        '<table id="' + v + '-data" class="table table-striped dt-responsive compact" style="margin:0 !important;"' +
                        ' width="100%" cellspacing="0"><thead>' +
                        labels + '</thead><tbody></tbody></table></div>'
                        );
                    //Init Datatable
                    tableData[sectionId][v] = { };
                    tableData[sectionId][v] = $('#' + v + '-data').DataTable(sectionsOptions[v].datatable);
                    if (sectionsOptions[v].datatable.rowReorder) {
                        // If rowReorder enabled
                        tableData[sectionId][v].on('row-reorder', function() {
                            that._rowReorderRedoSeq(tableData[sectionId][v], v);
                            // that._rowReorderDatatableDataToObject(details, sectionId, fieldsetDatatable, v);
                            tableData[sectionId][v].draw();
                        });
                    }
                });

                //Assign button click
                $('#' + fieldsetDatatable + '-assign-button').click(function(e) {
                    e.preventDefault();

                    var datatable;

                    if ($(sectionsOptions[fieldsetDatatable + '-datatables']).length > 1) {
                        datatable = $('#' + sectionsOptions[fieldsetDatatable].dataTableSelector.id)[0].value;
                    } else {
                        datatable = sectionsOptions[fieldsetDatatable + '-datatables'][0];
                    }

                    $('#' + fieldsetDatatable + '-fields').siblings().find('.has-error').removeClass('has-error has-feedback');//remove previous validation
                    $('#' + fieldsetDatatable + '-fields').siblings().find('table').removeClass('border-danger').addClass('border-default');//remove previous validation
                    $('#' + fieldsetDatatable + '-fields').siblings().find('.help-block').remove();//remove previous validation

                    //Execute preExtraction script passed from the html(js script)
                    if (sectionsOptions[datatable].preExtraction) {
                        sectionsOptions[datatable].preExtraction(tableData[sectionId][datatable]);
                    }

                    extractDatatableFieldsData = that._extractDatatableFieldsData(fieldsetDatatable, datatable, false);

                    //Execute postExtraction script passed from the html(js script)
                    if (sectionsOptions[datatable].postExtraction) {
                        sectionsOptions[datatable].postExtraction(tableData[sectionId][datatable], extractDatatableFieldsData);
                    }

                    var validated = that._validateForm(false, 'section', false, fieldsetDatatable + '-form');

                    if (validated) {

                        var rowAdded =
                            that._addExtractFieldsToDatatable(null, extractDatatableFieldsData, fieldsetDatatable, datatable, false);

                        if (rowAdded) {
                            $('#' + fieldsetDatatable).find('.jstreevalidate').val('');

                            // that._validateForm(false, 'sections', true, null);

                            tableData[sectionId][datatable].responsive.recalc();

                            that._registerDatatableButtons(
                                tableData[sectionId][datatable],
                                $('#' + datatable + '-div'),
                                datatable,
                                sectionId,
                                fieldsetDatatable
                            );

                            // var table = tableData[sectionId][datatable];

                            tableData[sectionId][datatable].on('responsive-display', function (showHide) {
                                if (showHide) {
                                    that._registerDatatableButtons(
                                        tableData[sectionId][datatable],
                                        $('#' + datatable + '-div'),
                                        datatable,
                                        sectionId,
                                        fieldsetDatatable
                                    );
                                }
                            });

                            //Execute postSuccess script passed from the html(js script)
                            if (sectionsOptions[datatable].postSuccess) {
                                sectionsOptions[datatable].postSuccess(tableData[sectionId][datatable], extractDatatableFieldsData);
                            }

                            that._clearDatatableFormData(datatable, fieldsetDatatable);
                        }
                    }
                });
            };

            //Extract Fields Datatable data
            _proto._extractDatatableFieldsLabel = function(fieldsetDatatable, datatable) {
                var extractedLabel = null;
                extractedLabel = { };
                var counter = 0;
                dataTableFields[componentId][sectionId][datatable] = [];
                $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                    if ($(v).data('bazscantype')) {
                        if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
                            extractedLabel[counter] = { };
                            if ($(v).data('bazscantype') === 'jstree') {//jstree
                                extractedLabel[counter].labelName = $(v).parents('.form-group').children('label').text();
                            } else if ($(v).data('bazscantype') === 'radio') {// icheck-radio
                                extractedLabel[counter].labelName = $(v).children('label').text();
                            } else if ($(v).data('bazscantype') === 'checkbox') {// icheck-checkbox
                                if ($(v).siblings('label').text() === '') {
                                    extractedLabel[counter].labelName = $(v).parents('.form-group').children('label').text();
                                } else {
                                    extractedLabel[counter].labelName = $(v).siblings('label').text();
                                }
                            } else {
                                extractedLabel[counter].labelName = $(v).siblings('label').text();
                            }
                            dataTableFields[componentId][sectionId][datatable].push($(v)[0].id);
                        }
                    }
                    counter++;
                });
                //Add buttons
                if (sectionsOptions[datatable].bazdatatable && sectionsOptions[datatable].bazdatatable.rowButtons) {
                    extractedLabel[counter] = { };
                    extractedLabel[counter].labelName = 'ACTIONS';
                    dataTableFields[componentId][sectionId][datatable].push(fieldsetDatatable + '-actions');
                }
                return extractedLabel;
            };

            //Extract Fields Datatable data
            _proto._extractDatatableFieldsData = function(fieldsetDatatable, datatable, isEdit) {
                var extractedFieldsData = null;
                var extractedJstreeData = null;
                var finalExtractedData = null;
                extractedFieldsData = { };
                extractedJstreeData = { };
                finalExtractedData = { };
                var counter = 0;

                $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                    if ($(v).data('bazscantype')) {
                        if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
                            extractedFieldsData[counter] = { };
                            extractedFieldsData[counter].id = v.id;
                            // extractedFieldsData[counter].data = $('#' + v.id); //Enable if you need all data

                            if (v.tagName === 'INPUT' && v.type === 'checkbox') {
                                if ($(v)[0].checked === true) {
                                    extractedFieldsData[counter].extractedData = 'YES';
                                } else {
                                    extractedFieldsData[counter].extractedData = 'NO';
                                }
                            } else if (v.tagName === 'INPUT' || v.tagName === "TEXTAREA") {
                                if ($(v)[0].value === 'undefined') {//kill if incorrect Data
                                    that._error('data is undefined!');
                                    return;
                                } else {
                                    extractedFieldsData[counter].extractedData = $(v)[0].value;
                                }
                            }
                            if ($(v).data('bazscantype') === 'select2') {
                                extractedFieldsData[counter].extractedData = null;
                                $($(v)[0].selectedOptions).each(function(i,v){
                                    if (!extractedFieldsData[counter].extractedData) {
                                        extractedFieldsData[counter].extractedData = '<span id="' + $(v)[0].value + '">' + $(v)[0].text + '</span><br>';
                                    } else {
                                        extractedFieldsData[counter].extractedData = extractedFieldsData[counter].extractedData + '<span id="' + $(v)[0].value + '">' + $(v)[0].text + '</span><br>';
                                    }
                                });
                            }
                            if ($(v).data('bazscantype') === 'jstree') {//jstree
                                var treeData = that._getJsTreeSelectedNodePath(fieldsetDatatable + '-form', $(v));
                                extractedJstreeData[counter] = { };
                                for (i = 0; i < Object.keys(treeData).length; i++) {
                                    extractedJstreeData[counter][i] = { };
                                    extractedJstreeData[counter][i].id = v.id;
                                    extractedJstreeData[counter][i].extractedData = '<span id="' + treeData[i].id + '" data-jstreeId="' + treeData[i].jstreeId + '">' + treeData[i].path + '</span><br>';
                                    extractedJstreeData[counter][i].absolutePath = treeData[i].path;
                                    extractedJstreeData[counter][i].nodeName = treeData[i].nodeName;
                                }
                            }
                            if ($(v).data('bazscantype') === 'radio') {// icheck-radio
                                extractedFieldsData[counter].extractedData = $(v).find('input:checked').siblings('label').text();
                            }
                            if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
                                extractedFieldsData[counter].extractedData = $(v).trumbowyg('html')
                            }
                            if ($(v).data('bazscantype') === 'html') {//HTML (as-is data)
                                extractedFieldsData[counter].extractedData = $(v).html();
                            }
                            if ($(v).data('bazscantype') === 'seq') {//sequence
                                extractedFieldsData[counter].extractedData = $(v).html();
                            }
                        }
                        if ($(v).data('bazscantype') === 'tableSelector') {
                            selectedTable = $(v).val();
                            multiTable = true;
                        }
                    }
                    counter++;
                });

                var rowId = 0;

                if (isEdit && multiTable) {
                    datatable = selectedTable;
                }

                if (datatable) {
                    if (tableData[sectionId][datatable].row().count() >= 0) {
                        rowId = tableData[sectionId][datatable].row().count() + 1;
                    }

                    if (Object.keys(extractedJstreeData).length > 0) {
                        for (var jstreesData in extractedJstreeData) {
                            for (var jstreeData in extractedJstreeData[jstreesData]) {
                                finalExtractedData[jstreeData] = { };
                                for (var fieldsData in extractedFieldsData) {
                                    finalExtractedData[jstreeData][fieldsData] = extractedFieldsData[fieldsData];
                                    if (fieldsData === jstreesData) {
                                        finalExtractedData[jstreeData][fieldsData] = extractedJstreeData[jstreesData][jstreeData];
                                    }
                                    if (sectionsOptions[datatable].bazdatatable.rowButtons) {
                                        //Add Action Buttons
                                        finalExtractedData[jstreeData][counter] = { };
                                        finalExtractedData[jstreeData][counter].extractedData = rowId;
                                        if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && !sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                            finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                            finalExtractedData[jstreeData][counter].extractedData =
                                                '<button data-row-id="' + rowId +
                                                '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>';
                                        } else if (!sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                            finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                            finalExtractedData[jstreeData][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                        } else if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                            finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                            finalExtractedData[jstreeData][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>' +
                                                                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                        }
                                    }
                                }
                                rowId++;
                            }
                        }
                    } else {//No JS Tree data extraction
                        finalExtractedData[0] = { };
                        for (var noJstree in extractedFieldsData) {
                            finalExtractedData[0][noJstree] = extractedFieldsData[noJstree];
                            if (sectionsOptions[datatable].bazdatatable && sectionsOptions[datatable].bazdatatable.rowButtons) {
                                //Add Action Buttons
                                finalExtractedData[0][counter] = { };
                                finalExtractedData[0][counter].extractedData = rowId;
                                if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && !sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                    finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                    finalExtractedData[0][counter].extractedData =
                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>';
                                } else if (!sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                    finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                    finalExtractedData[0][counter].extractedData =
                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                } else if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
                                    finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
                                    finalExtractedData[0][counter].extractedData =
                                    '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger float-right ml-1 tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button>' +
                                                                            '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa fas fa-fw text-xs fa-edit"></i></button>';
                                }
                            }
                        }
                        rowId++;
                    }
                    return finalExtractedData;
                } else {
                    return false;
                }
            };

            //Add extracted fields data to datatable
            _proto._addExtractFieldsToDatatable = function(rowIndex, extractDatatableFieldsData, fieldsetDatatable, datatable, isEdit) {
                var migrateData = false;
                var oldDataTable;
                // Need to convert to array to add to datatable to merge them later to object and add values to datatable
                var rowExtractedId = [];
                var rowExtractedData = [];
                var found = false;

                if (isEdit && multiTable) {
                    if (datatable !== selectedTable ){
                        migrateData = true;
                    }
                    oldDataTable = datatable;
                    datatable = selectedTable;
                }

                if (!isEdit && sectionsOptions[datatable].datatable.rowReorder) {
                    var seq = tableData[sectionId][datatable].rows().count();
                    if (seq === 0) {
                        seq = 1;
                    } else {
                        seq = seq + 1;
                    }
                    for (var dataRows in extractDatatableFieldsData) {
                        var oldId = extractDatatableFieldsData[dataRows][0].id;
                        extractDatatableFieldsData[dataRows][0] = { };
                        extractDatatableFieldsData[dataRows][0].id = oldId;
                        extractDatatableFieldsData[dataRows][0].extractedData = seq;
                        seq++;
                    }
                }

                for (var rows in extractDatatableFieldsData) {
                    rowExtractedData[rows] = [];
                    rowExtractedId[rows] = [];
                    for (var row in extractDatatableFieldsData[rows]) {
                        rowExtractedData[rows].push(extractDatatableFieldsData[rows][row].extractedData);//to datatable
                        rowExtractedId[rows].push(extractDatatableFieldsData[rows][row].id);
                    }
                }

                if (sectionsOptions[datatable].bazdatatable && sectionsOptions[datatable].bazdatatable.compareData) {
                    if (tableData[sectionId][datatable].rows().count() > 0) {

                        $('#' + datatable).children().find('tbody tr').removeClass('animated fadeIn bg-warning');
                        $('#' + datatable).children().find('tbody tr').children().removeClass('animated fadeIn bg-warning');

                        found =
                            that._compareData(
                                sectionsOptions[datatable].bazdatatable.compareData,
                                extractDatatableFieldsData,
                                tableData[sectionId][datatable].rows().data(),
                                rowIndex,
                                datatable
                            );
                    }
                }

                if (found) {
                    PNotify.notice({
                        title: 'Input data already exists in table!'
                    });

                    pnotifySound.play();

                    return false;
                } else {
                    if (rowIndex !== null) {//rowIndex is from editDatatableRow
                        if (!migrateData) {
                            $(rowExtractedData).each(function(i,v) {
                                tableData[sectionId][datatable].row(rowIndex).data(v).draw();
                            });
                        } else {
                            tableData[oldDataTable].row(rowIndex).remove().draw();
                            $(rowExtractedData).each(function(i,v) {
                                var drawnRow = tableData[sectionId][datatable].row.add(v).draw().node();
                                $(drawnRow).children('td').addClass('pb-1 pt-1');
                            });
                            // that._deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, sectionId, oldDataTable);
                            that._registerDatatableButtons(
                               tableData[oldDataTable],
                               $('#' + datatable + '-div'),
                               datatable,
                               sectionId,
                               fieldsetDatatable
                            );
                        }
                        that._registerDatatableButtons(
                           tableData[sectionId][datatable],
                           $('#' + datatable + '-div'),
                           datatable,
                           sectionId,
                           fieldsetDatatable
                        );
                        rowIndex = null;
                    } else {//add new row
                        $(rowExtractedData).each(function(i,v) {
                            var drawnRow = tableData[sectionId][datatable].row.add(v).draw().node();
                            $(drawnRow).children('td').addClass('pb-1 pt-1');
                        });
                        that._registerDatatableButtons(
                           tableData[sectionId][datatable],
                           $('#' + datatable + '-div'),
                           datatable,
                           sectionId,
                           fieldsetDatatable
                        );
                    }
                    //Add data to object
                    // this._addEditDatatableDataToObject(rowIndex, rowExtractedId, rowExtractedData, fieldsetDatatable, sectionId, datatable);

                    return true;
                }
            };

            //Edit table Row
            _proto._editDatatableRow = function(fieldsetDatatable, rowIndex, rowData, datatable) {
                var fieldsetFields = [];
                if ($(sectionsOptions[fieldsetDatatable + '-datatables']).length > 1) {
                    $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                        if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
                            fieldsetFields.push($(v));
                        }
                    });
                    $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {// Selector is always in the end.
                        if (($(v).data('bazscantype') === 'tableSelector')) {
                            fieldsetFields.push($(v));
                        }
                    });
                } else {
                    fieldsetFields = $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]');
                }

                $(fieldsetFields).each(function(i,v) {
                    if ($(v).data('bazscantype')) {
                        if ($(v).data('bazscantype') === 'seq') {
                            $(v).html(rowData[i]);
                        } else if ($(v).data('bazscantype') !== 'html') {
                            if (v.tagName === 'INPUT' && v.type === 'checkbox') {
                                if (rowData[i] === 'YES') {
                                    $(v).prop('checked', true);
                                } else if (rowData[i] === 'NO') {
                                    $(v).prop('checked', false);
                                }
                            } else if (v.tagName === 'INPUT' || v.tagName === 'TEXTAREA') {
                                $(v).val(rowData[i]);
                            }
                            if (v.tagName === "SELECT" && $(v).data('bazscantype') !== 'tableSelector') {//Select2
                                if (rowData[i]) {
                                    var selectarr = rowData[i].split('<br>');
                                    var selectArr = [];
                                    $(selectarr).each(function(i,v) {
                                        if (v !== "") {
                                            var extractIds = v.match(/(["'])(?:(?=(\\?))\2.)*?\1/g); //match double or single quotes
                                            selectArr.push(extractIds[0].replace(/"/g, ''));
                                        }
                                    });
                                    $(v).val(selectArr);
                                    $(v).trigger('change');
                                }
                            }
                            if (v.tagName === "SELECT" && $(v).data('bazscantype') === 'tableSelector') {
                                $(v).val(datatable);
                                $(v).trigger('change');
                            }
                            if (v.tagName === 'DIV') {
                                if ($(v).data('bazscantype') === 'jstree') {//jstree
                                    if (rowData[i]) {
                                        var jstreearr = rowData[i].split('<br>');
                                        var jstreeArr = [];
                                        $(jstreearr).each(function(i,v) {
                                            if (v !== "") {
                                                var extractJstreeId = v.match(/data-jstreeId=".*"/g);
                                                var extractIds = extractJstreeId[0].match(/(["'])(?:(?=(\\?))\2.)*?\1/g); //match double or single quotes
                                                jstreeArr.push(extractIds[0].replace(/"/g, ''));
                                            }
                                        });
                                        $(v).jstree('select_node', jstreeArr);
                                    }
                                }
                                if ($(v).data('bazscantype') === 'radio') {//radio
                                    $(v).find('input').each(function() {
                                        if (rowData[i] === $(this).siblings('label').text()) {
                                            $(this).prop('checked', true);
                                        } else {
                                            $(this).prop('checked', false);
                                        }
                                    });
                                }
                                if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
                                    $(v).trumbowyg('html', rowData[i]);
                                }
                            }
                        }
                    }
                });

                // Enable cancel/update button.
                $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', false);
                $('#' + fieldsetDatatable + '-update-button').attr('hidden', false);
                $('#' + fieldsetDatatable + '-assign-button').attr('hidden', true);
                // Then we extract data again, Compare again, Update data
                $('#' + fieldsetDatatable + '-update-button').off();
                $('#' + fieldsetDatatable + '-update-button').click(function(e) {
                    e.preventDefault();

                    var validated = that._validateForm(false, 'section', false, fieldsetDatatable + '-form');

                    if (validated) {
                        extractDatatableFieldsData = that._extractDatatableFieldsData(fieldsetDatatable, datatable, true);
                        var rowAdded =
                            that._addExtractFieldsToDatatable(
                                rowIndex,
                                extractDatatableFieldsData,
                                fieldsetDatatable,
                                datatable,
                                true
                            );
                        if (rowAdded) {
                            //Execute postSuccess script passed from the html(js script)
                            if (sectionsOptions[datatable].postSuccess) {
                                sectionsOptions[datatable].postSuccess(tableData[sectionId][datatable], extractDatatableFieldsData);
                            }
                        }
                        that._clearDatatableFormData(datatable, fieldsetDatatable);
                    }
                    // Hide cancel/update button.
                    $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-update-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-assign-button').attr('hidden', false);
                });
                $('#' + fieldsetDatatable + '-cancel-button').off();
                $('#' + fieldsetDatatable + '-cancel-button').click(function() {
                    // Hide cancel/update button.
                    $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-update-button').attr('hidden', true);
                    $('#' + fieldsetDatatable + '-assign-button').attr('hidden', false);
                    that._clearDatatableFormData(datatable, fieldsetDatatable);
                });
            };

            //Compare extracted fields data with data already in table
            _proto._compareData = function(compareData, inputData, currentTableData, rowIndexToExcl, datatable) {

                var foundRow, foundColumn;
                var excludeActions = false;
                var excludeSeqAndSort = false;

                if ((sectionsOptions[datatable].bazdatatable.rowButtons.canDelete === true) ||
                    (sectionsOptions[datatable].bazdatatable.rowButtons.canEdit === true)) {
                    excludeActions = true;
                }

                if (sectionsOptions[datatable].datatable.rowReorder === true) {
                    excludeSeqAndSort = true;
                }

                if (!compareData.inclIds &&
                    !compareData.exclIds &&
                    (compareData === 'rows' || compareData === 'columns')
                   ) {
                    for (var a = 0; a < currentTableData.length; a++) {
                        if (a !== rowIndexToExcl) {
                            for (var aInputData in inputData) {
                                var compareAllData = compareAll(compareData, inputData[aInputData], currentTableData[a]);

                                if (compareAllData !== false) {
                                    if (compareData === 'rows') {
                                        foundRow = $('#' + datatable).find('tbody tr')[a];
                                        $(foundRow).addClass('animated fadeIn bg-warning');
                                    } else if (compareData === 'columns') {
                                        foundColumn =
                                            $($('#' + datatable).find('tbody tr')[a]).find('td')[compareAllData];
                                        $(foundColumn).addClass('animated fadeIn bg-warning');
                                    }
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                } else if (compareData.inclIds && Object.keys(compareData.inclIds).length > 0) {
                    for (var b = 0; b < currentTableData.length; b++) {
                        if (b !== rowIndexToExcl) {
                            for (var bInputData in inputData) {
                                if (compareOnlyInclIds(compareData.inclIds, inputData[bInputData], currentTableData[b])) {
                                    foundRow = $('#' + datatable).find('tbody tr')[b];
                                    $(foundRow).addClass('animated fadeIn bg-warning');
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                } else if (compareData.exclIds && Object.keys(compareData.exclIds).length > 0) {
                    for (var c = 0; c < currentTableData.length; c++) {
                        if (c !== rowIndexToExcl) {
                            for (var cInputData in inputData) {
                                if (compareAllMinusExclIds(compareData.exclIds, inputData[cInputData], currentTableData[c])) {
                                    foundRow = $('#' + datatable).find('tbody tr')[c];
                                    $(foundRow).addClass('animated fadeIn bg-warning');
                                    return true;
                                }
                            }
                        }
                    }
                    return false;
                } else {
                    return false;
                }

                function compareAll(compareData, inputData, currentTableData) {
                    var currentTableDataLength = 0;
                    var startAt = 0;

                    if (excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 3;
                        startAt = 2;
                    } else if (!excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 1;
                    } else if (excludeSeqAndSort && !excludeActions) {
                        currentTableDataLength = currentTableData.length - 2;
                        startAt = 2;
                    }

                    if (compareData === 'rows') {
                        var match = [];
                        for (var i = 0; i < currentTableDataLength; i++) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                match[i] = 'true';
                            } else {
                                match[i] = 'false';
                            }
                            startAt++;
                        }

                        if ($.inArray('false', match) === -1) {
                            return true;
                        } else {
                            return false;
                        }
                    } else if (compareData === 'columns') {
                        for (var j = 0; j < currentTableDataLength; j++) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                return startAt;
                            }
                            startAt++;
                        }
                        return false;
                    }
                }

                function compareOnlyInclIds(inclIds, inputData, currentTableData) {
                    var inclIdsArray = [];
                    var uniqueData = { };
                    var uniqueDataFound = [];
                    var found = [];
                    var currentTableDataLength = 0;
                    var startAt = 0;
                    if (excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 3;
                        startAt = 2;
                    } else if (!excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 1;
                    } else if (excludeSeqAndSort && !excludeActions) {
                        currentTableDataLength = currentTableData.length - 2;
                        startAt = 2;
                    }
                    for (var inclId in inclIds) {
                        for (var sweepInclIds in inputData) {
                            if (inclId === inputData[sweepInclIds].id) {
                                if (inclIds[inclId].length >= 0) {
                                    uniqueData[sweepInclIds] = [];
                                    for (var uniqueInclId in inclIds[inclId]) {
                                        uniqueData[sweepInclIds].push(inclIds[inclId][uniqueInclId]);
                                    }
                                }
                                inclIdsArray.push(Number(sweepInclIds));
                                break;
                            }
                        }
                    }
                    for (var i = 0; i < currentTableDataLength; i++) {
                        var foundInclId = null;
                        foundInclId = $.inArray(startAt,inclIdsArray);
                        if (foundInclId !== -1) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                if (uniqueData[startAt].length > 0 ) {
                                    for (var j = 0; j < uniqueData[startAt].length; j++) {
                                        if (uniqueData[startAt][j] === inputData[startAt].extractedData) {
                                            uniqueDataFound.push('true');
                                        }
                                    }
                                } else {
                                    found.push('true');
                                }
                            }
                        }
                        startAt++;
                    }
                    if (found.length === Object.keys(inclIds).length) {
                        return true;
                    } else if (uniqueDataFound.length > 0) {
                        return true;
                    } else {
                        return false;
                    }
                }

                function compareAllMinusExclIds(exclIds, inputData, currentTableData) {
                    var exclIdsArray = [];
                    var currentTableDataLength = 0;
                    var startAt = 0;
                    if (excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 3;
                        startAt = 2;
                    } else if (!excludeSeqAndSort && excludeActions) {
                        currentTableDataLength = currentTableData.length - 1;
                    } else if (excludeSeqAndSort && !excludeActions) {
                        currentTableDataLength = currentTableData.length - 2;
                        startAt = 2;
                    }
                    for (var exclId in exclIds) {
                        for (var sweepExclIds in inputData) {
                            if (exclId === inputData[sweepExclIds].id) {
                                exclIdsArray.push(Number(sweepExclIds));
                                break;
                            }
                        }
                    }
                    for (var i = 0; i < currentTableDataLength; i++) {
                        var foundExclId = null;
                        foundExclId = $.inArray(startAt,exclIdsArray);
                        if (foundExclId === -1) {
                            if (currentTableData[startAt] === inputData[startAt].extractedData) {
                                return true;
                            } else {
                                return false;
                            }
                        }
                        startAt++;
                    }
                }
            };

            _proto._rowReorderRedoSeq = function(table, datatable) {
                var redoSeq = 1;
                $('#' + datatable).find('td.reorder').each(function() {
                    $(this).html(redoSeq);
                    redoSeq++;
                });
            };

            //Register table row edit and delete buttons
            _proto._registerDatatableButtons = function(table, el, datatable, sectionId, fieldsetDatatable) {
                var rowIndex, rowData;
                $(el).find('table').each(function() {
                    $(this).find('.tableDeleteButton').each(function() {
                        $(this).off();
                        $(this).click(function() {
                            if ($(this).closest('tr').hasClass('child')) {
                                rowIndex = table.row($(this).closest('tr').prev('tr')).index();
                                table.row($(this).closest('tr').prev('tr')).remove().draw();
                                that._rowReorderRedoSeq(table, datatable);
                                // that._deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, sectionId, datatable);
                                that._clearDatatableFormData(datatable, fieldsetDatatable);
                            } else {
                                rowIndex = table.row($(this).closest('tr')).index();
                                table.row($(this).parents('tr')).remove().draw();
                                that._rowReorderRedoSeq(table, datatable);
                                // that._deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, sectionId, datatable);
                                that._clearDatatableFormData(datatable, fieldsetDatatable);
                            }
                            that._registerDatatableButtons(table, el, datatable, sectionId, fieldsetDatatable);
                            //Execute postSuccess script passed from the html(js script)
                            if (sectionsOptions[datatable].postSuccess) {
                                sectionsOptions[datatable].postSuccess(tableData[sectionId][datatable]);
                            }
                            // Hide cancel/update button.
                            $('#' + fieldsetDatatable + '-cancel-button').attr('hidden', true);
                            $('#' + fieldsetDatatable + '-update-button').attr('hidden', true);
                            $('#' + fieldsetDatatable + '-assign-button').attr('hidden', false);

                            $('body').trigger('formToDatatableTableRowDelete');

                            if ($('#' + fieldsetDatatable + '-table-data tbody tr td.dataTables_empty').length === 1) {
                                $('body').trigger('formToDatatableTableEmpty');
                            }
                        });
                    });
                    $(this).find('.tableEditButton').each(function() {
                        $(this).off();
                        $(this).click(function() {
                            if ($(this).closest('tr').hasClass('child')) {
                                rowIndex = table.row($(this).closest('tr').prev('tr')).index();
                                rowData = table.row($(this).closest('tr').prev('tr')).data();
                            } else {
                                rowIndex = table.row($(this).closest('tr')).index();
                                rowData = table.row($(this).closest('tr')).data();
                            }
                            // var popActions = rowData.pop();//get rid of actions
                            that._clearDatatableFormData(datatable, fieldsetDatatable);
                            that._editDatatableRow(fieldsetDatatable, rowIndex, rowData, datatable);
                            //Execute onEdit script passed from the html(js script)
                            if (sectionsOptions[datatable].onEdit) {
                                sectionsOptions[datatable].onEdit(tableData[sectionId][datatable]);
                            }
                            $('body').trigger('formToDatatableTableRowEdit');
                        });
                    });
                });
            };

            //Clear form data on success insertion
            _proto._clearDatatableFormData = function(datatable, fieldsetDatatable) {
                var fieldsToClear;
                if (sectionsOptions[datatable].bazdatatable.keepFieldsData) {
                    var fieldsToKeep = sectionsOptions[datatable].bazdatatable.keepFieldsData;
                }
                var allFields = [];
                $('#' + fieldsetDatatable + '-fieldset').find('[data-bazscantype]').each(function(i,v) {
                    allFields.push($(v)[0].id);
                });

                if (fieldsToKeep && fieldsToKeep.length > 0) {
                    fieldsToClear = $(allFields).not(sectionsOptions[datatable].bazdatatable.keepFieldsData).get();//diff array
                } else if (fieldsToKeep && fieldsToKeep.length === 0) {
                    fieldsToClear = null;
                } else {
                    fieldsToClear = allFields;
                }

                if (fieldsToClear) {
                    $.each(fieldsToClear, function(i,v) {
                        v = '#' + v;
                        if ($(v).data('bazscantype')) {
                            if ($(v)[0].tagName === 'INPUT' && $(v)[0].type === 'checkbox') {
                                $(v).prop('checked', $(v).prop('defaultChecked'));
                            } else if ($(v)[0].tagName === 'INPUT' || $(v)[0].tagName === 'TEXTAREA') {
                                $(v).val('');
                            }
                            if ($(v)[0].tagName === "SELECT") {//select2
                                $(v).val(null).trigger('change');
                            }
                            if ($(v)[0].tagName === 'DIV') {
                                if ($(v).data('bazscantype') === 'jstree') {//jstree
                                    $(v).jstree('deselect_all');
                                }
                                if ($(v).data('bazscantype') === 'radio') {//radio
                                    if ($(v).find('input[checked]').length !== 0) {
                                        $(v).find('input[checked]').prop('checked', true);
                                    } else {
                                        $(v).find('input').each(function(i,v) {
                                            $(v).prop('checked', false);
                                        });
                                    }
                                }
                                if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
                                    $(v).trumbowyg('empty');
                                }
                            }
                        }
                    });
                }
            };

            // Add tables data to dataCollection
            _proto._tableDataToObj = function()
            {
                //eslint-disable-next-line
                console.log(tableData);

                // for (var data in tableData[sectionId]) {
                //     var excludeActions = false;
                //     var excludeSeqAndSort = false;
                //     var currentTableDataLength = 0;
                //     if ((sectionsOptions[data].bazdatatable.rowButtons.canDelete === true) || (sectionsOptions[data].bazdatatable.rowButtons.canEdit === true)) {
                //         excludeActions = true;
                //     }
                //     if (sectionsOptions[data].datatable.rowReorder === true) {
                //         excludeSeqAndSort = true;
                //     }
                //     dataCollection[componentId][sectionId][data] = [];
                //     $.each(tableData[sectionId][data].rows().data(), function(i,v) {
                //         var startAt = 0;
                //         if (excludeSeqAndSort && excludeActions) {
                //             currentTableDataLength = v.length - 3;
                //             startAt = 2;
                //         } else if (!excludeSeqAndSort && excludeActions) {
                //             currentTableDataLength = v.length - 1;
                //         } else if (excludeSeqAndSort && !excludeActions) {
                //             currentTableDataLength = v.length - 2;
                //             startAt = 2;
                //         }
                //         var thatI = i;
                //         dataCollection[componentId][sectionId][data][i] = { };
                //         for (var j = 0; j < currentTableDataLength; j++) {
                //             var columnData;
                //             var columnDataHasId = v[startAt].match(/id="(.*?)"/g)
                //             if (columnDataHasId) {
                //                 columnData = (columnDataHasId.toString().match(/"(.*?)"/g)).toString().replace(/"/g, '');
                //             } else {
                //                 columnData = v[startAt];
                //             }
                //             dataCollection[componentId][sectionId][data][thatI][dataTableFields[componentId][sectionId][data][startAt]] = columnData;
                //             startAt++;
                //         }
                //     });
                // }
            }

            BazContentSectionWithFormToDatatable._jQueryInterface = function _jQueryInterface(options) {
                dataCollection = window['dataCollection'];
                componentId = $(this).parents('.component')[0].id;
                sectionId = $(this)[0].id;

                dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'] = $(this).data(DATA_KEY);
                options = $.extend({}, Default, options);

                if (!dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable']) {
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'] = new BazContentSectionWithFormToDatatable($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable']._init(options);
                } else {
                    delete dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'];
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable'] = new BazContentSectionWithFormToDatatable($(this), options);
                    $(this).data(DATA_KEY, typeof options === 'string' ? 'options need to be an object and not string' : options);
                    dataCollection[componentId][sectionId]['BazContentSectionWithFormToDatatable']._init(options);
                }
            };

        return BazContentSectionWithFormToDatatable;

        }();

    $(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
        $('body').find('.sectionWithFormToDatatable').each(function() {
            // if ($(this).data('bazdevmodetools') === 'false' ||
            //     $(this).data('bazdevmodetools') === false) {
                BazContentSectionWithFormToDatatable._jQueryInterface.call($(this));
            // }
        });
    });

    $.fn[NAME] = BazContentSectionWithFormToDatatable._jQueryInterface;
    $.fn[NAME].Constructor = BazContentSectionWithFormToDatatable;

    $.fn[NAME].noConflict = function () {
        $.fn[NAME] = JQUERY_NO_CONFLICT;
        return BazContentSectionWithFormToDatatable._jQueryInterface;
    };

    return BazContentSectionWithFormToDatatable;
}(jQuery);

exports.BazContentSectionWithFormToDatatable = BazContentSectionWithFormToDatatable;

Object.defineProperty(exports, '__esModule', { value: true });

}));