/* globals define exports BazContentLoader BazContentFields Swal PNotify */
/*
* @title                    : BazContentSectionWithListing
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ sectionId).BazContentSectionWithListing;
* @functions                :
* @options                  :
*/
(function (global, factory) {
    typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
    typeof define === 'function' && define.amd ? define(['exports'], factory) :
    (global = global || self, factory(global.BazLibs = {}));
}(this, function (exports) {

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
            task     : null
        };
        var dataCollection,
            componentId,
            sectionId,
            // pnotifySound,
            classes,
            that,
            thisOptions,
            datatableOptions,
            swalSound;
        var listColumns = { };
        var filter, query;

        var BazContentSectionWithListing = function () {
            function BazContentSectionWithListing(element, settings) {
                that = this;
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
                query = '';

                dataCollection = window['dataCollection'];
                // pnotifySound = dataCollection.env.sounds.pnotifySound
                swalSound = dataCollection.env.sounds.swalSound

                if (!dataCollection[componentId]) {
                    dataCollection[componentId] = { };
                }
                if (!dataCollection[componentId][sectionId]) {
                    dataCollection[componentId][sectionId] = { };
                }

                if ($(this._element).is('.sectionWithListingFilter')) {
                    this._buildListingFilters(options);
                    filter = true;
                }

                if ($(this._element).is('.sectionWithListingDatatable')) {
                    this._buildListingDatatable(options);
                    query = thisOptions.listOptions.postParams;
                    filter = false;
                }
            };

            //Build listing filters
            _proto._buildListingFilters = function() {
                BazContentFields.init({
                    'componentId'   : componentId,
                    'sectionId'     : sectionId
                });
                $(this._element).BazContentSectionWithFormToDatatable();

                function toggleFilterButtons(sectionId) {
                    if ($('#' + sectionId + '-filters option:selected').data()['filter_type'] == 0) {
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                        $('#' + sectionId + '-delete').addClass('disabled');
                    } else if ($('#' + sectionId + '-filters option:selected').data()['filter_type'] == 1) {
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", false);
                        $('#' + sectionId + '-delete').removeClass('disabled');
                    }
                    if ($('#' + sectionId + '-filters option:selected').data()['shared'] == 1) {
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                        $('#' + sectionId + '-delete').addClass('disabled');
                    }
                }
                        // $('#' + sectionId + '-filters option:selected').data()['filter_type'] === 1    //User

                toggleFilterButtons(sectionId);

                //Filter Buttons
                $('#' + sectionId + '-filters').change(function() {
                    toggleFilterButtons(sectionId + '-filter');

                    if ($('#' + sectionId + '-filter-alert').length === 1) {
                        $('#' + sectionId + '-filter-alert').remove();
                    }


                    if (thisOptions.listOptions.postParams.order) {
                        query = { };
                        query['conditions'] = $('#' + sectionId + '-filter-filters option:selected').data()['conditions'];
                        query['order'] = thisOptions.listOptions.postParams.order;
                    } else {
                        query = $('#' + sectionId + '-filter-filters option:selected').data()['conditions'];
                    }

                    that._filterRunAjax(
                        1,
                        datatableOptions.paginationCounters.limit,
                        query
                    );
                });

                //Open Sharing Modal
                $('#' + sectionId + '-share').click(function(e) {
                    e.preventDefault();

                    var filterUrl = $('#' + sectionId + '-filter-filters option:selected').data()['url'];
                    var sharedIds = $('#' + sectionId + '-filter-filters option:selected').data()['shared_ids'];

                    if (sharedIds && sharedIds.rids) {
                        for (var rid in sharedIds.rids) {
                            $('#' + sectionId + '-filter-sharing-rids').append(
                                '<option value="' + sharedIds.rids[rid]['id'] + '" selected>' + sharedIds.rids[rid]['name'] + '</option>'
                            );
                        }
                        $('#' + sectionId + '-filter-sharing-rids').trigger('select2:change');
                    }

                    if (sharedIds && sharedIds.eids) {
                        for (var eid in sharedIds.eids) {
                            $('#' + sectionId + '-filter-sharing-eids').append(
                                '<option value="' + sharedIds.eids[eid]['id'] + '" selected>' + sharedIds.eids[eid]['name'] + '</option>'
                            );
                        }
                        $('#' + sectionId + '-filter-sharing-eids').trigger('select2:change');
                    }

                    if (sharedIds && sharedIds.aids) {
                        for (var aid in sharedIds.aids) {
                            $('#' + sectionId + '-filter-sharing-aids').append(
                                '<option value="' + sharedIds.aids[aid]['id'] + '" selected>' + sharedIds.aids[aid]['name'] + '</option>'
                            );
                        }
                        $('#' + sectionId + '-filter-sharing-aids').trigger('select2:change');
                    }

                    $('#' + sectionId + '-filter-sharing-direct-url').val(filterUrl);

                    $('#' + sectionId + '-filter-sharing-modal').modal('show');

                    //Share Buttons
                    $('#' + sectionId + '-filter-share-filter, #' + sectionId + '-filter-sharing-modal-button-close').off();
                    $('#' + sectionId + '-filter-sharing-modal-button-close').click(function() {
                        $('#' + sectionId + '-filter-sharing-rids').empty().trigger('select2:change');
                        $('#' + sectionId + '-filter-sharing-eids').empty().trigger('select2:change');
                        $('#' + sectionId + '-filter-sharing-aids').empty().trigger('select2:change');
                    });
                    $('#' + sectionId + '-filter-share-filter').click(function(e) {
                        e.preventDefault();

                        var rids = $('#' + sectionId + '-filter-sharing-rids').select2('data');
                        var uids;
                        if ($('#' + sectionId + '-filter-sharing-eids').length > 0) {
                            uids = $('#' + sectionId + '-filter-sharing-eids').select2('data');
                        } else if ($('#' + sectionId + '-filter-sharing-aids').length > 0) {
                            uids = $('#' + sectionId + '-filter-sharing-aids').select2('data');
                        }

                        //Save To Db
                        var postData = { };
                        postData['id'] = $('#' + sectionId + '-filter-filters option:selected').data()['id'];
                        postData['component_id'] = $('#' + sectionId + '-filter-filters option:selected').data()['component_id'];
                        postData['filter_type'] = '1';
                        postData['shared_ids'] = { };
                        postData['shared_ids']['rids'] = [];
                        if (rids.length > 0) {
                            $(rids).each(function(index, rid) {
                                postData['shared_ids']['rids'].push(rid.id);
                            });
                        }
                        if ($('#' + sectionId + '-filter-sharing-eids').length > 0) {
                            postData['shared_ids']['eids'] = [];
                            if (uids.length > 0) {
                                $(uids).each(function(index, eid) {
                                    postData['shared_ids']['eids'].push(eid.id);
                                });
                            }
                        } else if ($('#' + sectionId + '-filter-sharing-aids').length > 0) {
                            postData['shared_ids']['aids'] = [];
                            if (uids.length > 0) {
                                $(uids).each(function(index, aid) {
                                    postData['shared_ids']['aids'].push(aid.id);
                                });
                            }
                        }
                        postData['shared_ids'] = JSON.stringify(postData['shared_ids']);

                        postData[$('#security-token').attr('name')] = $('#security-token').val();

                        var url = $(this).attr('href');

                        // //Update Filter
                        $.post(url, postData, function(response) {
                            if (response.responseCode === 0) {
                                PNotify.success({
                                    'title' : response.responseMessage
                                });
                                if (response.filters) {
                                    redoFiltersOptions(query, sectionId, response);
                                }
                            } else {
                                PNotify.error({
                                    'title' : response.responseMessage
                                });
                            }
                            if (response.tokenKey && response.token) {
                                $('#security-token').attr('name', response.tokenKey);
                                $('#security-token').val(response.token);
                            }
                            $('#' + sectionId + '-filter-sharing-rids').empty().trigger('select2:change');
                            $('#' + sectionId + '-filter-sharing-eids').empty().trigger('select2:change');
                            $('#' + sectionId + '-filter-sharing-aids').empty().trigger('select2:change');
                        }, 'json');

                        $('#' + sectionId + '-filter-sharing-modal').modal('hide');
                    });
                });

                //Reset
                $('#' + sectionId + '-reset').click(function(e) {
                    e.preventDefault();

                    resetFilters();
                });
                function resetFilters() {
                    query = '';
                    var defaultFilter = null;

                    $('#' + sectionId + '-filter-filters').children().each(function(index, filter) {
                        if ($(filter).data()['account_id'] != 0 &&
                            $(filter).data()['is_default'] == 1
                        ) {
                            query = $(filter).data()['conditions'];
                            defaultFilter = filter;
                            return false;
                        } else if ($(filter).data()['account_id'] == 0 &&
                                   $(filter).data()['is_default'] == 1
                        ) {
                            query = $(filter).data()['conditions'];
                            defaultFilter = filter;
                        }
                    });

                    that._filterRunAjax(
                        1,
                        datatableOptions.paginationCounters.limit,
                        query
                    );

                    toggleFilterButtons(sectionId + '-filter');

                    if (defaultFilter) {
                        $('#' + sectionId + '-filter-filters').val($(defaultFilter).val());
                        $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                        $('#' + sectionId + '-delete').addClass('disabled');
                    }
                }

                //Add / Open Modal
                $('#' + sectionId + '-add').click(function(e) {
                    e.preventDefault();

                    $('#' + sectionId + '-filter-save-add').attr('hidden', false);
                    $('#' + sectionId + '-filter-save-update').attr('hidden', true);

                    clearStoredData();

                    $('#' + sectionId + '-filter-modal').modal('show');
                });

                //Clone / Open Modal
                $('#' + sectionId + '-clone').click(function(e) {
                    e.preventDefault();

                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    if ($(selectedFilter).data()['conditions'] === '') {
                        PNotify.error({'title': 'Show All filter cannot be cloned'});
                        return;
                    }

                    var postData = { };
                    postData['id'] = selectedFilter.data()['id'];
                    postData['component_id'] = selectedFilter.data()['component_id'];
                    postData['clone'] = true;
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    var url = $(this).attr('href');

                    $.post(url, postData, function(response) {
                        if (response.responseCode === 0) {
                            PNotify.success({
                                'title'     : response.responseMessage
                            });
                            if (response.filters) {
                                redoFiltersOptions('', sectionId, response);
                            }
                            resetFilters();
                            toggleFilterButtons(sectionId + '-filter');
                        } else {
                            PNotify.error({
                                'title'     : response.responseMessage
                            });
                        }
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                    }, 'json');

                    toggleFilterButtons(sectionId + '-filter');
                });

                //Edit / Open Modal
                $('#' + sectionId + '-edit').click(function(e) {
                    e.preventDefault();

                    $('#' + sectionId + '-filter-save-add').attr('hidden', true);
                    $('#' + sectionId + '-filter-save-update').attr('hidden', false);

                    editFilter();

                    $('#' + sectionId + '-filter-modal').modal('show');
                });
                function editFilter() {
                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    var conditionsStr = $(selectedFilter).data().conditions;

                    if (conditionsStr === '') { //Empty string for show all condition
                        return;
                    }
                    var conditions = conditionsStr.substring(0, conditionsStr.length - 1);
                    var conditionsRows = conditions.split('&');
                    var conditionsColumns = [];

                    $.each(conditionsRows, function(index, row) {
                        conditionsColumns[index] = row.split('|');
                    });

                    var select2FieldData;

                    //Andor Object
                    select2FieldData = $('#' + sectionId + '-filter-andor option');
                    var andOrS2 = { };
                    select2FieldData.each(function(index, data) {
                        if ($(data).data().value) {
                            andOrS2[$(data).data().value] = $(data)[0].innerHTML;
                        }
                    });

                    if ($('#' + sectionId + '-filter-andor').data().disabledtext) {
                        andOrS2[$('#' + sectionId + '-filter-andor').data().disabledtext] =
                        $('#' + sectionId + '-filter-andor').data().disabledtext;
                    }

                    //Andor Object
                    select2FieldData = $('#' + sectionId + '-filter-field option');
                    var fieldS2 = { };
                    select2FieldData.each(function(index, data) {
                        if ($(data).data().value) {
                            fieldS2[$(data).data().value] = $(data)[0].innerHTML;
                        }
                    });

                    //Andor Object
                    select2FieldData = $('#' + sectionId + '-filter-operator option');
                    var operatorS2 = { };
                    select2FieldData.each(function(index, data) {
                        if ($(data).data().value) {
                            operatorS2[$(data).data().value] = $(data)[0].innerHTML;
                        }
                    });

                    var columns = { };

                    $.each(conditionsColumns, function(index, column) {

                        columns[index] = { };
                        columns[index][0] = { };
                        columns[index][0]['id'] = sectionId + '-filter-andor';

                        if (!andOrS2[column[0]]) {
                            columns[index][0]['extractedData'] = '<span class="' + column[0] + '"></span><br>';
                        } else {
                            columns[index][0]['extractedData'] = '<span class="' + column[0] + '">' + andOrS2[column[0]] + '</span><br>';
                        }

                        columns[index][1] = { };
                        columns[index][1]['id'] = sectionId + '-filter-field';
                        columns[index][1]['extractedData'] = '<span class="' + column[1] + '">' + fieldS2[column[1]] + '</span><br>';

                        columns[index][2] = { };
                        columns[index][2]['id'] = sectionId + '-filter-operator';
                        columns[index][2]['extractedData'] = '<span class="' + column[2] + '">' + operatorS2[column[2]] + '</span><br>';

                        columns[index][3] = { };
                        columns[index][3]['id'] = sectionId + '-filter-value';
                        columns[index][3]['extractedData'] = column[3];

                        columns[index][4] = { };
                        columns[index][4]['id'] = sectionId + '-filter-actions';
                        columns[index][4]['extractedData'] =
                            '<button data-row-id="' + (index + 1) + '" type="button" class="btn btn-xs btn-danger float-right ml-1' +
                            ' tableDeleteButton"><i class="fa fas fa-fw text-xs fa-trash"></i></button><button data-row-id="' +
                            (index + 1) + '" type="button" class="btn btn-xs btn-primary float-right tableEditButton"><i class="fa ' +
                            'fas fa-fw text-xs fa-edit"></i></button>';
                    });

                    dataCollection[componentId][sectionId + '-filter'][sectionId + '-filter-table']['data'] = columns;

                    dataCollection[componentId][sectionId + '-filter']['BazContentSectionWithFormToDatatable']._dataArrToTableData();

                    $('#' + sectionId + '-filter-id').val($(selectedFilter).data()['id']);
                    $('#' + sectionId + '-filter-name').val($(selectedFilter).data()['name']);

                    if ($(selectedFilter).data()['is_default'] === 1) {
                        $('#' + sectionId + '-filter-default')[0].checked = true;
                        $('#' + sectionId + '-filter-default').attr('disabled', true);
                    } else {
                        $('#' + sectionId + '-filter-default')[0].checked = false;
                        $('#' + sectionId + '-filter-default').attr('disabled', false);
                    }
                }
                $('body').on('formToDatatableTableImportComplete', function () {
                    dataCollection[componentId][sectionId + '-filter']['BazContentSectionWithFormToDatatable']._tableDataToObj();
                    $('#' + sectionId + '-filter-name').attr('disabled', false);
                });

                //Delete
                $('#' + sectionId + '-delete').click(function(e) {
                    e.preventDefault();

                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    Swal.fire({
                        title                       : '<span class="text-danger"> Delete Filter ' + selectedFilter.data()['name'] + '?</span>',
                        icon                        : 'question',
                        background                  : 'rgba(0,0,0,.8)',
                        backdrop                    : 'rgba(0,0,0,.6)',
                        buttonsStyling              : false,
                        confirmButtonText           : 'Delete',
                        customClass                 : {
                            'confirmButton'             : 'btn btn-danger text-uppercase',
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
                        if (result.value) {
                            if ($(selectedFilter).data().ns === true) {
                                $(selectedFilter).remove();
                                $('#' + sectionId + '-edit, #' + sectionId + '-share').attr("disabled", true);
                                $('#' + sectionId + '-delete').addClass('disabled');

                                PNotify.success({
                                    'title'     : selectedFilter.data()['name'] + ' deleted successfully.'
                                });
                                resetFilters();
                            } else {
                                var postData = { };
                                postData['id'] = selectedFilter.data()['id'];
                                postData['component_id'] = selectedFilter.data()['component_id'];
                                postData[$('#security-token').attr('name')] = $('#security-token').val();

                                var url = $(this).attr('href');

                                $.post(url, postData, function(response) {
                                    if (response.responseCode === 0) {
                                        PNotify.success({
                                            'title'     : selectedFilter.data()['name'] + ' deleted successfully.'
                                        });
                                        if (response.filters) {
                                            redoFiltersOptions('', sectionId, response);
                                        }
                                        resetFilters();
                                    } else {
                                        PNotify.error({
                                            'title'     : 'Cannot delete filter.'
                                        });
                                    }
                                    if (response.tokenKey && response.token) {
                                        $('#security-token').attr('name', response.tokenKey);
                                        $('#security-token').val(response.token);
                                    }
                                }, 'json');
                            }
                        }
                    });
                });

                // Add Numeric for numberic fields
                $('#' + sectionId + '-field').children().each(function(index, field) {
                    if ($(field).data()['number'] == true) {
                        var html = $(field).html();
                        $(field).html(html + ' (Numeric)');
                    }
                });

                //Enable/Disable Operators as per field type (numeric/alphanumeric)
                $('#' + sectionId + '-field').on('select2:select', function(e) {

                    var options = $('#' + sectionId + '-filter-operator').children();

                    if ($(e.params.data.element).data()['number'] == true) {
                        options.each(function(index, option) {
                            $(option).attr('disabled', false);
                        });
                        $('#' + sectionId + '-filter-value').attr('pattern', "([0-9]+.{0,1}[0-9]*,{0,1})*[0-9]");

                    } else if ($(e.params.data.element).data()['number'] == false) {
                        options.each(function(index, option) {
                            if ($(option).val() === 'lessthan' ||
                                $(option).val() === 'lessthanequals' ||
                                $(option).val() === 'greaterthan' ||
                                $(option).val() === 'greaterthanequals'
                            ) {
                                $(option).attr('disabled', true);
                            } else {
                                $(option).attr('disabled', false);
                            }
                        });
                        $('#' + sectionId + '-filter-value').attr('pattern', "");
                    }

                    $('#' + sectionId + '-filter-operator').trigger('change');
                });

                //Enable/Disable value field on empty/notempty operator
                $('#' + sectionId + '-operator').on('select2:select', function(e) {
                    if ($(e.params.data.element).val() === 'empty' ||
                        $(e.params.data.element).val() === 'notempty'
                    ) {
                        $('#' + sectionId + '-filter-value').attr('disabled', true);
                    } else {
                        $('#' + sectionId + '-filter-value').attr('disabled', false);
                    }
                });

                $('body').on('formToDatatableTableRowEdit', function(e) {
                    if ($('#' + sectionId + '-filter-operator').val() === 'empty' ||
                        $('#' + sectionId + '-filter-operator').val() === 'notempty'
                    ) {
                        $('#' + sectionId + '-filter-value').attr('disabled', true);
                    } else {
                        $('#' + sectionId + '-filter-value').attr('disabled', false);
                    }

                    $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                    $('#' + sectionId + '-filter-value').attr('disabled', false);

                    if (e.rowsCount === 1) {
                        $('#' + sectionId + '-filter-andor').attr('disabled', true);
                    } else {
                        if (e.rowIndex === 0) {
                            $('#' + sectionId + '-filter-andor').attr('disabled', true);
                        } else {
                            $('#' + sectionId + '-filter-andor').attr('disabled', false);
                        }
                    }
                });

                //Add Name
                $('#' + sectionId + '-name').keyup(function() {
                    if ($(this).val() !== '') {
                        $('#' + sectionId + '-filter-save-add').removeClass('disabled');
                        $('#' + sectionId + '-filter-save-update').removeClass('disabled');
                    } else {
                        $('#' + sectionId + '-filter-save-add').addClass('disabled');
                        $('#' + sectionId + '-filter-save-update').addClass('disabled');
                    }
                });

                //Adding to table
                $('#' + sectionId + '-assign-button').click(function(e) {
                    e.preventDefault();
                    onFormToDatatableTableUpdate(e);

                    // $('#' + sectionId + '-filter-name').attr('disabled', false);
                    // $('#' + sectionId + '-filter-value').attr('disabled', false);

                    // $('#' + sectionId + '-filter-andor').attr('disabled', false);
                    // $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                });

                $('body').on('formToDatatableTableUpdatedClicked', function(e) {
                    onFormToDatatableTableUpdate(e);
                });

                $('#' + sectionId + '-default').click(function() {

                    var postData = { };
                    postData['component_id'] = $('#' + sectionId + '-filter-filters option:selected').data()['component_id'];
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    var url = $(this).data('href');

                    $.post(url, postData, function(response) {
                        if (response.responseCode === 0) {
                            Swal.fire({
                                title                       : '<span class="text-danger"> Filter ' + response.defaultFilter[0].name + ' is already set as default. ' +
                                                              'Make this filter default instead?</span>',
                                icon                        : 'question',
                                background                  : 'rgba(0,0,0,.8)',
                                backdrop                    : 'rgba(0,0,0,.6)',
                                buttonsStyling              : false,
                                confirmButtonText           : 'Yes',
                                cancelButtonText            : 'No',
                                customClass                 : {
                                    'confirmButton'             : 'btn btn-info text-uppercase',
                                    'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                                },
                                showCancelButton            : true,
                                keydownListenerCapture      : true,
                                allowOutsideClick           : false,
                                allowEscapeKey              : false,
                                didOpen                     : function() {
                                    swalSound.play();
                                }
                            }).then((result) => {
                                if (result.isDismissed) {
                                    $('#' + sectionId + '-filter-default')[0].checked = false;
                                }
                            });
                        }
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                    }, 'json');

                    $('#' + sectionId + '-filter-save-add').removeClass('disabled');
                    $('#' + sectionId + '-filter-save-update').removeClass('disabled');
                });

                //If Only 1 row - Remove And/Or
                $('body').on('formToDatatableTableRowDelete', function(e) {
                    onFormToDatatableTableUpdate(e);
                });

                function onFormToDatatableTableUpdate(e) {
                    //Remove numeric from edit data
                    $('#' + sectionId + '-filter-table-data tbody tr').each(function(index, tr) {
                       var field = $(tr).find('td')[1];
                       if (field) {
                           field.innerHTML = field.innerHTML.replace(" (Numeric)", "");
                       }
                    });
                    $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                    $('#' + sectionId + '-filter-andor').attr('disabled', false);

                    if (e.rowsCount === 1) {
                        $($('#' + sectionId + '-filter-table-data tbody tr')[0]).find('td')[0].innerHTML = '-';
                    }
                    if (e.rowsCount < 1) {
                        $('#' + sectionId + '-filter-name').attr('disabled', true);
                        $('#' + sectionId + '-filter-save-add').addClass('disabled');
                        $('#' + sectionId + '-filter-save-update').addClass('disabled');
                    } else {
                        $('#' + sectionId + '-filter-name').attr('disabled', false);
                        $('#' + sectionId + '-filter-save-add').removeClass('disabled');
                        $('#' + sectionId + '-filter-save-update').removeClass('disabled');
                    }
                }

                //Save
                $('#' + sectionId + '-save-add, #' + sectionId + '-save-update').click(function(e) {
                    e.preventDefault();

                    query = '';

                    var selectedFilter = $('#' + sectionId + '-filter-filters option:selected');

                    var tableData =
                        dataCollection[componentId][sectionId + '-filter'][sectionId + '-filter-table']['data'];

                    $.each(tableData, function(index, data) {
                        if (index === 0) {
                            query +=
                                '-|' +
                                data[sectionId + '-filter-field'] + '|' +
                                data[sectionId + '-filter-operator'] + '|' +
                                data[sectionId + '-filter-value'] + '&';
                        } else {
                            query +=
                                data[sectionId + '-filter-andor'] + '|' +
                                data[sectionId + '-filter-field'] + '|' +
                                data[sectionId + '-filter-operator'] + '|' +
                                data[sectionId + '-filter-value']
                                + '&';
                        }
                    });

                    var filterName = $('#' + sectionId + '-filter-name').val();

                    if (filterName === '') {
                        $('#' + sectionId + '-filter-name').addClass('is-invalid');
                        $('#' + sectionId + '-filter-name').focus(function() {
                            $(this).removeClass('is-invalid');
                        });
                        return;
                    }

                    //Save To Db
                    var postData = { };
                    postData['id'] = $('#' + sectionId + '-filter-id').val();
                    postData['name'] = filterName;
                    postData['conditions'] = query;
                    postData['component_id'] = $(selectedFilter).data()['component_id'];
                    postData['filter_type'] = '1';
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    if ($('#' + sectionId + '-filter-default')[0].checked === true) {
                        postData['is_default'] = '1';
                        filterName = filterName + ' (Default)';
                    } else {
                        postData['is_default'] = '0';
                    }

                    var url = $(this).attr('href');

                    // if (postData['id'] !== '') {
                    //     url = 'filter/update';
                    // } else {
                    //     url = 'filter/add';
                    // }

                    //Update Filter
                    $.post(url, postData, function(response) {
                        if (response.responseCode === 0) {
                            PNotify.success({
                                'title' : response.responseMessage
                            });
                            if (response.filters) {
                                redoFiltersOptions(query, sectionId, response);
                            }
                        } else {
                            PNotify.error({
                                'title' : response.responseMessage
                            });
                        }
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                    }, 'json');

                    //Make Filter Call
                    $('#' + sectionId + '-filter-modal').modal('hide');
                    that._filterRunAjax(
                        1,
                        datatableOptions.paginationCounters.limit,
                        query
                    );

                    clearStoredData();
                });

                function redoFiltersOptions(query, sectionId, data) {
                    var filtersOptions = '';

                    $.each(data.filters, function(index, filter) {
                        filtersOptions += '<option ';
                        var filterName = filter['name'];

                        for (var filterColumn in filter) {
                            filtersOptions += 'data-' + filterColumn + '="' + filter[filterColumn] + '" ';
                        }

                        filtersOptions += 'data-value="' + filter['id'] + '" value="' + filter['id'] + '" ';

                        if (filter['is_default'] == '1') {
                            filterName = filter['name'] + ' (Default)';
                        }
                        if (filter['filter_type'] == '0') {
                            filterName = filter['name'] + ' (System)';
                        // } else if (filter['filter_type'] == '2') {
                        //     filterName = filter['name'] + ' (Shared)';
                        }
                        if (filter['shared_ids']) {
                            if (filter['employee_full_name']) {
                                filterName = filter['name'] + ' (Shared by ' + filter['employee_full_name'] + ')';
                            } else if (filter['account_email']) {
                                filterName = filter['name'] + ' (Shared by ' + filter['account_email'] + ')';
                            }
                        }

                        if (filter['conditions'] === query) {
                            filtersOptions += 'selected';
                        }

                        filtersOptions += '>' + filterName;
                        filtersOptions += '</option>';
                    });
                    $('#' + sectionId + '-filter-filters').empty().append(filtersOptions);
                    toggleFilterButtons(sectionId + '-filter');
                }

                function clearStoredData() {
                    $('#' + sectionId + '-filter-andor').val('and').trigger('change');
                    $('#' + sectionId + '-filter-field').val(null).trigger('change');
                    $('#' + sectionId + '-filter-field-error').remove();
                    $('#' + sectionId + '-filter-operator').val(null).trigger('change');
                    $('#' + sectionId + '-filter-operator-error').remove();
                    $('#' + sectionId + '-filter-value').val('');
                    $('#' + sectionId + '-filter-id').val('');
                    $('#' + sectionId + '-filter-name').val('');
                    $('#' + sectionId + '-filter-default')[0].checked = 0;

                    dataCollection[componentId][sectionId + '-filter']['datatables'][sectionId + '-filter-table']
                        .rows().clear().draw();

                    dataCollection[componentId][sectionId + '-filter'][sectionId + '-filter-table']['data'] = [];
                    $('#' + sectionId + '-filter-name').attr('disabled', true);
                    $('#' + sectionId + '-filter-name').val('New Filter');
                    $('#' + sectionId + '-filter-save-add').addClass('disabled');
                    $('#' + sectionId + '-filter-save-update').addClass('disabled');
                    $('#' + sectionId + '-filter-cancel-button').attr('hidden', true);
                    $('#' + sectionId + '-filter-update-button').attr('hidden', true);
                    $('#' + sectionId + '-filter-assign-button').attr('hidden', false);
                    $('#' + sectionId + '-filter-update-button').off();//Important
                }

                //Modal Close button
                $('#' + sectionId + '-modal .modal-close').click(function(e) {
                    e.preventDefault();

                    clearStoredData();
                });

                if ($('#listing-filters-quick').length === 1) {
                    var dataType, selectedId;

                    $('#listing-filters-quick a').click(function(e) {
                        e.preventDefault();

                        $('#' + sectionId + '-filter-quick').attr('disabled', false);
                        $('#' + sectionId + '-filter-quick').val('');
                        $('#' + sectionId + '-filter-search').attr('disabled', false);

                        $('#' + sectionId + '-filter-quick-prepend-dropdown-button span').text($(this).text().toUpperCase());

                        selectedId = $(this).data('id');

                        dataType = $('#' + sectionId + '-filter-field').find('[data-value="' + $(this).data('id') + '"]').data('datatype');
                    });

                    $('#' + sectionId + '-search').click(function(e) {
                        e.preventDefault();

                        if (dataType == 0) {
                            query = '-|' + selectedId + '|equals|' + $('#' + sectionId + '-filter-quick').val().trim() + '&';
                        } else {
                            query = '-|' + selectedId + '|like|%' + $('#' + sectionId + '-filter-quick').val().trim() + '%&';
                        }

                        that._filterRunAjax(
                            1,
                            datatableOptions.paginationCounters.limit,
                            query
                        );
                    });
                }
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

                thisOptions = dataCollection[componentId][sectionId];
                listColumns[thisOptions.listOptions.tableName] = [];

                datatableOptions = thisOptions.listOptions.datatable;
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

                    //Bug with responsive datatable plugin. If the column has mobile keyword, the column is automatically converted into responsive.
                    var columnid;
                    if (column.id.search(/mobile/g) !== -1) {
                        columnid = column.id.replace(/mobile/i, 'mobil');//Just change the keyword to something else.
                    } else {
                        columnid = column.id
                    }

                    if (datatableOptions.colTextTruncate) {
                        classes = 'data-' + columnid + ' text-truncate dt-colTextTruncate';
                    } else {
                        classes = 'data-' + columnid;
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
                    order           : datatableOptions.order,
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
                                        infoFiltered    : ' - filtered from _MAX_ shown entries',
                                        searchPlaceholder: 'Search shown ' + thisOptions.listOptions.componentName + '...',
                                        select          : {
                                            rows    : {
                                                    _   : 'Selected %d rows. Click the selected row again to deselect',
                                                    0   : '<i class="fas fa-fw fa-info-circle"></i>Click a row to select it',
                                                    1   : 'Selected 1 row. Click the selected row again to deselect'
                                                }
                                        },
                                        buttons         : {
                                            selectAll   : '<i class="fas fa-fw fa-check-double"></i>',
                                            selectNone  : '<span class="fa-stack">' +
                                                          '<i class="fas fa-fw fa-check-double fa-stack-1x"></i>' +
                                                          '<i class="fas fa-fw fa-ban fa-stack-2x text-danger"></i>' +
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
                                        that._drawCallback();
                                    }
                });

                if (thisOptions.listOptions.postUrl) {
                    that._runDatatableAjax(thisOptions.listOptions.postParams);
                } else {
                    // Enable paging if data is more than 20 on static datatable
                    if (datatableOptions.pagination && datatableOptions.paginationCounters.filtered_items > 20) {
                        $.extend(thisOptions.listOptions.datatable, {
                            paging : true,
                        });
                    }
                    $('#listing-data-loader').hide();
                    $('#listing-primary-buttons').attr('hidden', false);
                    $('#listing-secondary-buttons').attr('hidden', false);
                    $('#listing-filters').attr('hidden', false);
                    that._tableInit(false);
                    that._registerEvents();
                }
            }

            _proto._runDatatableAjax = function(postData, reDraw) {
                var url = thisOptions.listOptions.postUrl;
                $.ajax({
                    url         : url,
                    method      : 'post',
                    dataType    : 'json',
                    data        : postData,
                    success     : function(response) {
                        if (response.responseCode != 0) {
                            PNotify.error({
                                title   : response.responseMessage
                            });
                        }
                        $('#listing-data-loader').hide();
                        $('#listing-primary-buttons').attr('hidden', false);
                        $('#listing-secondary-buttons').attr('hidden', false);
                        $('#listing-filters').attr('hidden', false);
                        $.extend(thisOptions.listOptions.datatable, JSON.parse(response.rows));
                        $('body').trigger(
                            {
                                'type'     : 'sectionWithListingLoaded'
                            }
                        );
                    }
                }).done(function(response) {
                    if (response.tokenKey && response.token) {
                        $('#security-token').attr('name', response.tokenKey);
                        $('#security-token').val(response.token);
                    }
                    that._tableInit(reDraw);
                    that._registerEvents();
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
            _proto._tableInit = function(reDraw) {
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
                    if (datatableOptions.pagination && datatableOptions.paginationCounters.filtered_items > 20) {
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
                            data                : '__control',
                            title               : 'ACTIONS',
                            orderable           : false,
                            className           : classes,
                            responsivePriority  : -1
                        });
                    }

                    if (thisOptions.customFunctions.beforeTableInit) {
                        thisOptions.customFunctions.beforeTableInit();
                    }

                    thisOptions['datatable'] = $('#' + thisOptions.listOptions.tableName).DataTable(datatableOptions);

                    if (thisOptions.customFunctions.afterTableInit) {
                        if (datatableOptions.headerClass) {
                            var thead = $('#' + thisOptions.listOptions.tableName).children('thead').children('tr');

                            if (thead) {
                                thead.addClass(datatableOptions.headerClass).css({'position':'relative','z-index':999});
                            }
                        }

                        thisOptions.customFunctions.afterTableInit();
                    }

                    thisOptions['datatable'].columns.adjust().responsive.recalc();

                    that._updateCounters();

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

                            that._changeResponsiveLiWidths($($(row.node()).next('.child')));

                            $($(row.node()).next('.child')).find('.dtr-expand').click(function() {
                                if ($(this).parent().is('.text-truncate')) {
                                    $(this).parent().removeClass('text-truncate dt-colTextTruncate');
                                    $(this).removeClass('fa-plus-circle text-info').addClass('fa-minus-circle text-danger');
                                } else {
                                    $(this).parent().addClass('text-truncate dt-colTextTruncate');
                                    $(this).removeClass('fa-minus-circle text-danger').addClass('fa-plus-circle text-info');
                                    that._changeResponsiveLiWidths($($(row.node()).next('.child')));
                                }
                            });
                        }
                    });

                    //Search
                    $('.dataTables_filter').find('input').keyup(function() {
                        if ($(this).val() === '') {
                            that._updateCounters();
                        }
                    });

                    thisOptions['datatable'].on('draw', function () {
                        if ($('#' + sectionId + '-table tbody td.dataTables_empty').length === 1) {
                            $('.dataTables_empty').last().html('No entries found');
                        }
                    });

                    //Length Change
                    thisOptions['datatable'].on('length.dt', function (e, settings, len) {
                        if (len === -1) {
                            len = datatableOptions.paginationCounters.filtered_items;
                        }

                        that._filterRunAjax(
                            datatableOptions.paginationCounters.first,
                            len,
                            query
                        );
                    });

                } else { //redraw used on pagination prev and next

                    if (thisOptions.customFunctions.beforeRedraw) {
                        thisOptions.customFunctions.beforeRedraw();
                    }

                    thisOptions['datatable'].rows.add(datatableOptions.data).draw();

                    if (thisOptions.customFunctions.afterRedraw) {
                        thisOptions.customFunctions.afterRedraw();
                    }

                    that._updateCounters();
                }

                if (datatableOptions.rowControls) {
                    BazContentLoader.init({});
                }
            }

            //Update width for open child
            _proto._changeResponsiveLiWidths = function(child) {
                var width = child.width();

                var titleWidth = ((20 * width) / 100) / 16;//rem
                var liWidth = width/16;

                child.find('.dtr-title').css({"width" : titleWidth + 'rem'});
                child.find('.dtr-title').parent('li').removeClass('dt-colTextTruncate');
                child.find('.dtr-data').parent('li').css({"width" : liWidth + 'rem'});
            }

            //Register __control(Action buttons)
            _proto._registerEvents = function() {
                // customSwitch Toggle Function
                // $('#' + sectionId + '-table .custom-switch input').each(function(index,rowSwitchInput) {
                //     $(rowSwitchInput).click(function() {
                //         var rowSwitchInputId = $(rowSwitchInput)[0].id;
                //         var url = dataCollection.env.rootPath + 'index.php?route=' + $(rowSwitchInput).data('switchactionurl');
                //         var columnId = $(rowSwitchInput).data('columnid');
                //         var checked = $(rowSwitchInput).is('[checked]');
                //         var columnsDataToInclude = $(rowSwitchInput).data('switchactionincludecolumnsdata').split(',');
                //         var rowData;
                //         rowData = thisOptions['datatable'].row($(this).parents('tr')).data();
                //         if (checked) {
                //             rowData[columnId] = 0;
                //             $(rowSwitchInput).attr('checked', false);
                //             document.getElementById(rowSwitchInputId).checked = false;
                //         } else {
                //             rowData[columnId] = 1;
                //             $(rowSwitchInput).attr('checked', true);
                //             document.getElementById(rowSwitchInputId).checked = true;
                //         }
                //         var name = $(rowSwitchInput).parents('td').siblings('.data-' + $(rowSwitchInput).data('notificationtextfromcolumn')).html();
                //         var switchOnText = name + ' enabled';
                //         var switchOffText = name + ' disabled';
                //         if (checked) {
                //             Swal.fire({
                //                 title                       : '<span class="text-danger"> Disable ' + name + '?</span>',
                //                 icon                        : 'question',
                //                 background                  : 'rgba(0,0,0,.8)',
                //                 backdrop                    : 'rgba(0,0,0,.6)',
                //                 customClass                 : {
                //                     'confirmButton'             : 'btn btn-danger text-uppercase',
                //                     'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                //                 },
                //                 buttonsStyling              : false,
                //                 confirmButtonText           : 'Disable',
                //                 showCancelButton            : true,
                //                 keydownListenerCapture      : true,
                //                 allowOutsideClick           : false,
                //                 allowEscapeKey              : false,
                //                 onOpen                      : function() {
                //                     swalSound.play();
                //                 }
                //             }).then((result) => {
                //                 if (result.value) {
                //                     runAjax(false, switchOffText);
                //                 } else {
                //                     $(rowSwitchInput).attr('checked', true);
                //                     document.getElementById(rowSwitchInputId).checked = true;
                //                 }
                //             });
                //         } else {
                //             runAjax(true, switchOnText);
                //         }
                //         function runAjax(status, notificationText) {
                //             var dataToSubmit = { };
                //             for (var data in rowData) {
                //                 if (columnsDataToInclude.includes(data)) {
                //                     dataToSubmit[data] = rowData[data];
                //                 }
                //             }
                //             $.ajax({
                //                 url         : url,
                //                 method      : 'post',
                //                 data        : dataToSubmit,
                //                 dataType    : 'json',
                //                 success     : function(response) {
                //                     if (response.responseCode === 0) {
                //                         PNotify.success({
                //                             title           : notificationText,
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $(rowSwitchInput).attr('checked', status);
                //                         document.getElementById(rowSwitchInputId).checked = true;
                //                     } else {
                //                         PNotify.error({
                //                             title           : 'Error!',
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $(rowSwitchInput).attr('checked', false);
                //                         document.getElementById(rowSwitchInputId).checked = false;
                //                     }
                //                     pnotifySound.play();
                //                     if (response.tokenKey && response.token) {
                //                         $('#security-token').attr('name', response.tokenKey);
                //                         $('#security-token').val(response.token);
                //                     }
                //                 }
                //             });
                //         }
                //     });
                // });

                // RadioButtons
                // $('#' + sectionId + '-table .btn-group-toggle label').each(function(index,radioButtonsLabel) {
                //     $(radioButtonsLabel).click(function() {
                //         var currentCheckedId, currentCheckedLabel;
                //         $(this).siblings('label').children('input').each(function(index,sibling) {
                //             if (sibling.checked) {
                //                 currentCheckedId = sibling.id;
                //                 currentCheckedLabel = sibling.parentElement;
                //             } else if (sibling.defaultChecked) {
                //                 currentCheckedId = sibling.id;
                //                 currentCheckedLabel = sibling.parentElement;
                //             }
                //         });
                //         var thisId = $(this).children('input')[0].id;
                //         var url = dataCollection.env.rootPath + 'index.php?route=' + $(this).children('input').data('radiobuttonsactionurl');
                //         var columnId = $(this).children('input').data('columnid');
                //         var dataValue = $(this).children('input').data('value');
                //         var checked = false;
                //         if ($(this).children('input').is('[checked]') || $(this).children('input')[0].defaultChecked) {
                //             checked = true;
                //         }
                //         var radioChangeText = $(this).parents('td').siblings('.data-' + $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                //                                 $(this).children('input').data('columnid') + ' changed';
                //         if (!checked) {
                //             Swal.fire({
                //                 title                       : '<span class="text-danger"> Change ' +
                //                                                 $(this).parents('td').siblings('.data-' +
                //                                                 $(this).children('input').data('notificationtextfromcolumn')).html() + ' ' +
                //                                                 $(this).children('input').data('columnid') +
                //                                                 '?</span>',
                //                 icon                        : 'question',
                //                 background                  : 'rgba(0,0,0,.8)',
                //                 backdrop                    : 'rgba(0,0,0,.6)',
                //                 buttonsStyling              : false,
                //                 confirmButtonText           : 'Change',
                //                 customClass                 : {
                //                     'confirmButton'             : 'btn btn-danger text-uppercase',
                //                     'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                //                 },
                //                 showCancelButton            : true,
                //                 keydownListenerCapture      : true,
                //                 allowOutsideClick           : false,
                //                 allowEscapeKey              : false,
                //                 onOpen                      : function() {
                //                     swalSound.play();
                //                 }
                //             }).then((result) => {
                //                 if (result.value) {
                //                     runAjax(false, radioChangeText);
                //                 } else {
                //                     $(this).removeClass('focus active');
                //                     $('#' + currentCheckedId).attr('checked', true);
                //                     document.getElementById(currentCheckedId).checked = true;
                //                     $(currentCheckedLabel).addClass('focus active');
                //                 }
                //             });
                //         }

                //         function runAjax(status, notificationText) {
                //             var columnsDataToInclude = $('#' + thisId).data('radiobuttonsactionincludecolumnsdata').split(',');
                //             var rowData = thisOptions['datatable'].row($('#' + thisId).parents('tr')).data();
                //             var dataToSubmit = { };
                //             for (var data in rowData) {
                //                 if (columnsDataToInclude.includes(data)) {
                //                     if (columnId === data) {
                //                         dataToSubmit[data] = dataValue;
                //                     } else {
                //                         dataToSubmit[data] = rowData[data];
                //                     }
                //                 }
                //             }
                //             $.ajax({
                //                 url         : url,
                //                 method      : 'post',
                //                 data        : dataToSubmit,
                //                 dataType    : 'json',
                //                 success     : function(response) {
                //                     if (response.responseCode === 1) {
                //                         PNotify.removeAll()
                //                         PNotify.success({
                //                             title           : notificationText,
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $('#' + currentCheckedId).attr('checked', false);
                //                         document.getElementById(currentCheckedId).checked = false;
                //                         $('#' + thisId).attr('checked', true);
                //                         document.getElementById(thisId).checked = true;
                //                     } else {
                //                         PNotify.error({
                //                             title           : 'Error!',
                //                             cornerClass     : 'ui-pnotify-sharp'
                //                         });
                //                         $('#' + thisId).parent('label').removeClass('focus active');
                //                         $('#' + thisId).attr('checked', false);
                //                         document.getElementById(thisId).checked = false;
                //                         $('#' + currentCheckedId).attr('checked', true);
                //                         document.getElementById(currentCheckedId).checked = true;
                //                         $(currentCheckedLabel).addClass('focus active');
                //                     }
                //                     pnotifySound.play();
                //                     if (response.tokenKey && response.token) {
                //                         $('#security-token').attr('name', response.tokenKey);
                //                         $('#security-token').val(response.token);
                //                     }
                //                 }
                //             });
                //         }
                //     });
                // });

                // Deleting Row (element .rowRemove)
                $('#' + sectionId + '-table .rowRemove').each(function(index,rowRemove) {
                    $(rowRemove).off();
                    $(rowRemove).click(function(e) {
                        e.preventDefault();
                        var thisButton = this;
                        var url = $(this).attr('href');
                        var deleteText = $(this).parents('td').siblings('.data-' + $(this).data('notificationtextfromcolumn')).text();
                        var dataToSend = { };
                        dataToSend.id = thisOptions['datatable'].row($(thisButton).parents('tr')).id();
                        Swal.fire({
                            title                       : '<span class="text-danger"> Delete ' + deleteText + '?</span>',
                            icon                        : 'question',
                            background                  : 'rgba(0,0,0,.8)',
                            backdrop                    : 'rgba(0,0,0,.6)',
                            buttonsStyling              : false,
                            confirmButtonText           : 'Delete',
                            customClass                 : {
                                'confirmButton'             : 'btn btn-danger text-uppercase',
                                'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                            },
                            showCancelButton            : true,
                            keydownListenerCapture      : true,
                            allowOutsideClick           : false,
                            allowEscapeKey              : false,
                            didOpen                      : function() {
                                swalSound.play();
                            }
                        }).then((result) => {
                            if (result.value) {
                                if (datatableOptions.sendConfirmRemove === 'true' || datatableOptions.sendConfirmRemove === '1') {
                                    dataToSend.confirm = '1';
                                }
                                dataToSend[$('#security-token').attr('name')] = $('#security-token').val();

                                $.ajax({
                                    url         : url,
                                    method      : 'post',
                                    dataType    : 'json',
                                    data        : dataToSend,
                                    success     : function(response) {
                                        if (response.tokenKey && response.token) {
                                            $('#security-token').attr('name', response.tokenKey);
                                            $('#security-token').val(response.token);
                                        }
                                        if (response.responseCode != 0) {
                                            PNotify.error({
                                                title           : response.responseMessage,
                                            });
                                        }
                                    }
                                }).done(function(response) {
                                    if (response.responseCode === 0) {
                                        that._filterRunAjax(
                                            datatableOptions.paginationCounters.current,
                                            datatableOptions.paginationCounters.limit,
                                            query
                                        );

                                        if (dataCollection[componentId][sectionId]['afterRowRemove']) {
                                            dataCollection[componentId][sectionId]['afterRowRemove']();
                                        }
                                    }
                                });
                            }
                        });
                    });
                });

                if ($('.btn-tool-reset-cache').length === 1) {
                    $('.btn-tool-reset-cache').off();
                    $('.btn-tool-reset-cache').click(function(e) {
                        e.preventDefault();

                        that._filterRunAjax(
                            1,
                            datatableOptions.paginationCounters.limit,
                            query,
                            true
                        );
                    });
                }
                $('body').trigger('sectionWithListingEventsRegistered');
            }

            _proto._updateCounters = function() {
                var counters = { };

                counters.total = datatableOptions.paginationCounters.total_items;
                counters.filtered_total = datatableOptions.paginationCounters.filtered_items;
                counters.end = datatableOptions.paginationCounters.limit * datatableOptions.paginationCounters.current;
                counters.start = (counters.end - datatableOptions.paginationCounters.limit) + 1;

                if (datatableOptions.paginationCounters.current === datatableOptions.paginationCounters.last) {
                    counters.end = datatableOptions.paginationCounters.filtered_items;
                }
                if (query && filter) {
                    $('#' + sectionId + '-table_info').empty().html(
                        "Showing " + counters.start + " to " + counters.end +
                        " of " + counters.filtered_total + " filtered entries (Total entries: " + counters.total + ")"
                    );
                } else {
                    $('#' + sectionId + '-table_info').empty().html(
                        "Showing " + counters.start + " to " + counters.end + " of " + counters.filtered_total + " entries"
                    );
                }
            }

            _proto._filterRunAjax = function(page = null, limit = null, filterQuery = null, resetCache = false) {
                if (!page) {
                    page = datatableOptions.paginationCounters.current;
                }
                if (!limit) {
                    limit = datatableOptions.paginationCounters.limit;
                }
                if (!filterQuery) {
                    filterQuery = query;
                }

                thisOptions['datatable'].rows().clear().draw();
                $('.dataTables_empty').last().html('<i class="fas fa-cog fa-spin"></i> Loading...');

                var postData = { };
                postData['page'] = page;
                postData['limit'] = limit;

                if (filterQuery.conditions || filterQuery.order) {
                    postData['conditions'] = filterQuery.conditions;
                    postData['order'] = filterQuery.order;
                } else {
                    postData['conditions'] = filterQuery;
                }

                if (resetCache) {
                    postData['resetCache'] = 'true';
                }

                that._runDatatableAjax(postData, true);
            }

            _proto._drawCallback = function() {
                if (datatableOptions.pagination &&
                    datatableOptions.paginationCounters.filtered_items > 20 &&
                    (datatableOptions.paginationCounters.filtered_items !== datatableOptions.paginationCounters.limit)
                ) {
                    if (datatableOptions.paginationCounters.current !== datatableOptions.paginationCounters.first) {
                        $('.paginate_button.previous').removeClass('disabled');
                        $('.paginate_button.previous').click(function() {
                            that._filterRunAjax(
                                datatableOptions.paginationCounters.previous,
                                datatableOptions.paginationCounters.limit,
                                query
                            );
                        });
                    }
                    if (datatableOptions.paginationCounters.current !== datatableOptions.paginationCounters.last) {
                        $('.paginate_button.next').removeClass('disabled');
                        $('.paginate_button.next').click(function() {
                            that._filterRunAjax(
                                datatableOptions.paginationCounters.next,
                                datatableOptions.paginationCounters.limit,
                                query
                            );
                        });
                    }
                }
            }

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