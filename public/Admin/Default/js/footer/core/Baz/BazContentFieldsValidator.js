/* exported BazContentFieldsValidator */
/* globals BazContentFields */
/*
* @title                    : BazContentFieldsValidator
* @description              : Baz Content Fields Validator Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentFieldsValidator._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { 'use strict'; for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentFieldsValidator = function() {
    'use strict';
    var BazContentFieldsValidator = void 0;
    var componentId,
        sectionId,
        on,
        errorSound,
        hasErrorCount, //Error counts to show during validation.
        formLocation, //Location of form, either in section or in datatable.
        validateForms = { }, //Validation of form on section submit
        validateDatatableOnSections, //Validation of datatable on section submit
        validateFormsOnDatatable, //Validate datatable form on datable submit
        dataCollection,
        sectionsJsTreeSelector;
    var hasError = []; //Validation, list of fields that has errors
    // var tableData = { }; //Datatable Data

    // Error
    function error(errorMsg) {
        throw new Error(errorMsg);
    }

    //Init
    function init(options) {
        componentId = options.componentId;
        sectionId = options.sectionId;
        dataCollection = window['dataCollection'];
        errorSound = new Audio(dataCollection.env.soundPath + 'swal.mp3'); //Error Sound for Swal
        if (options.on === 'section') {
            on = sectionId;
        } else if (options.on === 'component'){
            on = componentId;
        } else if (options.on === 'datatable'){
            on = componentId;// Check
        } else {
            error('on option not set in BazContentFieldsValidator.')
        }
    }

    //Init Validator
    function initValidator() {
        dataCollection[componentId][sectionId]['initValidator'] = true;
        var formId, validateOptions;
        validateForms[componentId] = { };
        validateForms[componentId][sectionId] = [];
        validateDatatableOnSections = { };
        validateFormsOnDatatable = [];
        if (!$.fn.validate) {
            error('Validator not found!');
        } else {
            $('#' + on).find('form').each(function(index,form) {
                formId = $(form)[0].id;
                $.validator.setDefaults({
                    debug: false,
                    ignore: ":submit, :reset, :image, :disabled",
                    onkeyup: false,
                    onclick: false,
                    submitHandler: function() { },
                    focusInvalid: false
                });
                validateOptions = {
                    errorElement: 'div',
                    errorPlacement: function ( error, element ) {
                        element.parents('.form-group').append(error);
                        error.addClass('text-uppercase text-danger text-xs help-block');
                        $(element).closest('.form-group').addClass('has-feedback');
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    // unhighlight: function (element) { },
                    success: function (element) {
                        var type = $(element).parents('form').data('validateon');
                        var formId = $(element).parents('form')[0].id;
                        componentId = $(element).parents('.component')[0].id;
                        sectionId = $(element).parents('.sectionWithForm')[0].id;
                        $(element).closest('.form-group').removeClass('has-error');
                        $(element).closest('.help-block').remove();
                        validateForm(componentId, sectionId, true, type, true, formId);
                    }
                };
                if (dataCollection[componentId][sectionId][sectionId + '-form']) {
                    validateOptions = _extends(validateOptions, dataCollection[componentId][sectionId][sectionId + '-form']);
                }

                dataCollection[componentId][sectionId]['formValidator'] = $(form).validate(validateOptions);//init validate form

                if ($(form).data('validateon') === 'sections') {
                    validateForms[componentId][sectionId].push(formId);
                }
                if ($(form).data('validateon') === 'datatable') {
                    validateFormsOnDatatable.push(formId);
                }
            });
            if ($('div[data-validateon="sections"]').length !== 0) {
                $('div[data-validateon="sections"]').each(function (index, datatable) {
                    if (!validateDatatableOnSections[$(datatable).parents('section')[0].id]) {
                        validateDatatableOnSections[$(datatable).parents('section')[0].id] = [ ];
                        validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
                    } else {
                        validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
                    }
                });
            }
        }
    }

    //Validate Sections on Submit
    function validateForm(componentId, sectionId, onSuccess, type, preValidated, formId) {
        if (!preValidated) {
            if (type === 'component') {
                formLocation = componentId;
                // for (var component in validateForms[componentId]) {
                //     $.each(validateForms[componentId][sectionId], function(index, form) {
                //         $('#' + form).submit();
                //     });

                //     if (!($.isEmptyObject(validateDatatableOnSections))) {
                //         //Validating datatable if empty, throw error
                //         for (var sections in validateDatatableOnSections) {
                //             if (validateDatatableOnSections[sections].length > 0) {
                //                 $.each(validateDatatableOnSections[sections], function(index, datatable) {
                //                     if (!tableData[sections][datatable].data().any()) {
                //                         $('#' + datatable + '-table-div').addClass('form-group has-error has-feedback');
                //                         $('#' + datatable + '-table-data').removeClass('border-default').addClass('border-danger');
                //                         $('#' + datatable + '-table-error').remove();
                //                         $('#' + datatable).append(
                //                             '<div id="' + datatable + '-table-error" class="text-danger help-block">Table cannot be empty!</div>'
                //                         );
                //                     }
                //                 });
                //             }
                //         }
                //     }
                // }
            } else if (type === 'sections') {
                formLocation = sectionId;
                $.each(validateForms[componentId][sectionId], function(index, form) {
                    $('#' + form).submit();
                });
            } else if (type === 'datatable') {
                formLocation = formId;
                $('#' + formId).submit();
            }

            hasError = [];
            $('#' + formLocation).find('.has-error').each(function(index,errorId) {
                var id = $(errorId).find('label').html();
                hasError.push(id.toUpperCase());
            });
            hasErrorCount = hasError.length;
            if (!preValidated && hasErrorCount > 0) {
                $('#' + formLocation + '-alert').remove();
                $('#' + formLocation).before(
                '<div id="' + formLocation + '-alert" class="alert alert-danger alert-dismissible animated fadeIn rounded-0 mb-0">' +
                '   <button id="' + formLocation + '-alert-dismiss" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                '   <i class="icon fa fa-ban"></i>You have <strong>'+ hasErrorCount + '</strong> errors! ' +
                '   Please fix these errors before submitting the data' +
                '<div>'
                );
                errorSound.play();
                if (type === 'component') {
                    if (sectionsJsTreeSelector) {
                        // BazContentFields.fixHeight('fixedHeight');
                        $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
                            var word, words = [];
                            var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
                            if (searchFor.indexOf(',') >= 0) {
                                words = searchFor.split(',');
                            } else {
                                words = [searchFor];
                            }
                            for (var i = 0; i < words.length; i++) {
                                word = words[i];
                                if ((node.text || "").indexOf(word) >= 0) {
                                    if (node.text === word) {
                                        return true;
                                    }
                                }
                            }
                            return false;
                        }
                        $(sectionsJsTreeSelector).jstree(true).refresh();
                        $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-primary').addClass('box-danger');
                        $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Errors');
                        $('#' + formLocation + '-sections-tree').find('.card-tools').addClass('hidden');
                        $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-bars').addClass('fa-ban');
                        $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                        $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
                        $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').addClass('hidden');
                        $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').removeClass('hidden');
                        $('#' + formLocation + '-sections-fields-search').attr('disabled', true);
                        $('#' + formLocation + '-sections-fields-search-cancel').click(function() {
                            cancelValidatingForm(type, formLocation, false, formId);
                        });
                    }
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        cancelValidatingForm(type, formLocation, false, formId);
                    });
                    return false;
                } else if (type === 'sections') {
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        formLocation = $(this).parent().siblings('.sectionWithForm')[0].id;
                        cancelValidatingForm(type, formLocation, false, formId);
                    });
                    return false;
                } else if (type === 'datatable') {
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        formLocation = $(this).parent().siblings('.sectionWithForm')[0].id;
                        cancelValidatingForm(type, formLocation, false, formId);
                    });
                    return false;
                }
            } else {
                if (type === 'datatable') {
                    return true;
                }
                return true;
            }
        } else {
            if (type === 'component') {
                hasErrorCount = $('#' + formLocation).find('.has-error').length;
                hasError = [];
                $('#' + formLocation).find('.has-error').each(function(index,errorId) {
                    var id = $(errorId).children('label').html();
                    hasError.push(id.toUpperCase());
                });
                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    if (sectionsJsTreeSelector) {
                        $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
                        $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                    }
                    return false;
                } else {
                    if (!onSuccess) {
                        cancelValidatingForm(type, formLocation, false, formId);
                    } else {
                        cancelValidatingForm(type, formLocation, true, formId);
                    }
                    return true;
                }
            } else if (type === 'sections') {
                hasErrorCount = $('#' + sectionId).find('.has-error').length;
                hasError = [];
                $('#' + sectionId).find('.has-error').each(function(index,errorId) {
                    var id = $(errorId).children('label').html();
                    hasError.push(id.toUpperCase());
                });
                if (hasErrorCount > 0) {
                    $('#' + sectionId + '-alert').find('strong').html(hasErrorCount);
                    return false;
                } else {
                    if (!onSuccess) {
                        cancelValidatingForm(type, sectionId, false, formId);
                    } else {
                        cancelValidatingForm(type, sectionId, true, formId);
                    }
                    return true;
                }
            } else if (type === 'datatable') {
                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    return false;
                } else {
                    cancelValidatingForm(type, formLocation, false, formId);
                    return true;
                }
            }
        }
    }

    //Cancel validating form
    function cancelValidatingForm(type, formLocation, jstreeRefresh, formId) {
        $('#' + formLocation + '-alert').remove();
        if (type === 'component') {
            if (sectionsJsTreeSelector) {
                // BazContentFields.fixHeight('fixedHeight');
                $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-danger').addClass('box-primary');
                $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Sections');
                $('#' + formLocation + '-sections-tree').find('.card-tools').removeClass('hidden');
                $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-ban').addClass('fa-bars');
                $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');
                $('#' + formLocation + '-sections-fields-search').val('');
                $(sectionsJsTreeSelector).jstree(true).search('');
                $('#' + formLocation + '-sections-fields-search').attr('disabled', false);
                $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').removeClass('hidden');
                $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').addClass('hidden');
                $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
                    var word, words = [];
                    var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
                    if (searchFor.indexOf(',') >= 0) {
                        words = searchFor.split(',');
                    } else {
                        words = [searchFor];
                    }
                    for (var i = 0; i < words.length; i++) {
                        word = words[i];
                        if ((node.text || "").indexOf(word) >= 0) {
                            return true;
                        }
                    }
                    return false;
                }
                if (!jstreeRefresh && formId !== null) {
                    BazContentFields.redoSectionsJsTree();
                }
            }
        } else if (type === 'datatable') {
            if ($('#' + formLocation).find('div').is('[data-bazscantype="jstree"]')) {
                $('#' + formLocation).find('[data-bazscantype="jstree"]').removeClass('border-danger').addClass('border-default');
                $('#' + formLocation).find('[type="search"]').removeClass('border-danger');
                $('#' + formLocation).find('[type="search"]').siblings('.input-group-addon').removeClass('bg-danger').addClass('bg-default');
            }
        }
        $('#' + formLocation).find('.form-group').each(function(i,v) {
            $(v).removeClass('has-error has-feedback');
        });
        $('#' + formLocation).find('.help-block').each(function(i,v) {
            $(v).remove();
        });
        //Cancel Validating datatable
        for (var sections in validateDatatableOnSections) {
            if (validateDatatableOnSections[sections].length > 0) {
                $.each(validateDatatableOnSections[sections], function(index, datatable) {
                    $('#' + datatable + '-table-data').removeClass('border-danger').addClass('border-default');
                });
            }
        }
    }

    function bazContentFieldsValidatorConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentFieldsValidatorConstructor) {
        BazContentFieldsValidator = BazContentFieldsValidatorConstructor;
        BazContentFieldsValidator.defaults = { };
        BazContentFieldsValidator.initValidator = function(options) {
            init(_extends(BazContentFieldsValidator.defaults, options));
            initValidator();
        }
        BazContentFieldsValidator.validateForm = function(options) {
            init(_extends(BazContentFieldsValidator.defaults, options));
            var validate = validateForm(options.componentId, options.sectionId, options.onSuccess, options.type, options.preValidated, options.formId);
            return validate;
        }
        BazContentFieldsValidator.cancelValidatingForm = function(options) {
            init(_extends(BazContentFieldsValidator.defaults, options));
            cancelValidatingForm(options.type, options.formLocation, options.jstreeRefresh, options.formId);
        }


    }

    setup(bazContentFieldsValidatorConstructor);

    return bazContentFieldsValidatorConstructor;
}();




// (function ($) {
//     'use strict';

//     var that,
//         thisOptions,
//         hasErrorCount, //Error counts to show during validation.
//         formLocation, //Location of form, either in section or in datatable.
//         validateFormsOnSections, //Validation of form on section submit
//         validateDatatableOnSections, //Validation of datatable on section submit
//         validateFormsOnDatatable, //Validate datatable form on datable submit
//         rootPath,
//         soundPath,
//         errorSound,
//         dataCollection,
//         componentId,
//         sectionsJsTreeSelector;

//     var hasError = []; //Validation, list of fields that has errors
//     var tableData = { }; //Datatable Data
//     var DataKey = 'bb.bazvalidator';

//     var Default = { };

//     // BazValidator Class Definition
//     // =========================
//     var BazValidator = function (element, options) {
//         thisOptions = options;
//         componentId = element[0].id;
//         that = this;
//         dataCollection = window['dataCollection'];
//         rootPath = dataCollection.rootPath;
//         soundPath = 'assets/application/dashboard/default/sounds/';
//         errorSound = new Audio(rootPath + soundPath + 'swal.mp3'); //Error Sound for Swal

//         this.initValidator(componentId, thisOptions);

//     };

//     //Throw error
//     BazValidator.prototype.error = function(errorMsg) {
//         throw new Error(errorMsg);
//     };

//     //Init validator on form
//     BazValidator.prototype.initValidator = function(componentId) {
//         //eslint-disable-next-line
//         console.log('validator Init');
//         var formId, validateOptions;
//         validateFormsOnSections = [];
//         validateDatatableOnSections = { };
//         validateFormsOnDatatable = [];
//         validateFormsOnSections = [];
//         if (!$.fn.validate) {
//             that.error('Validator not found!');
//         } else {
//             $('#' + componentId).find('form').each(function(index,form) {
//                 formId = $(form)[0].id;
//                 $.validator.setDefaults({
//                     debug: false,
//                     ignore: ":submit, :reset, :image, :disabled",
//                     onkeyup: false,
//                     onclick: false,
//                     submitHandler: function() { },
//                     focusInvalid: false
//                 });
//                 validateOptions = {
//                     errorElement: 'div',
//                     errorPlacement: function ( error, element ) {
//                         element.parents('.form-group').append(error);
//                         error.addClass('help-block');
//                         $(element).closest('.form-group').addClass('has-feedback');
//                     },
//                     highlight: function (element) {
//                         $(element).closest('.form-group').addClass('has-error');
//                     },
//                     // unhighlight: function (element) { },
//                     success: function (element) {
//                         var type = $('#' + element[0].id).parents('form').data('validateon');
//                         var formId = $('#' + element[0].id).parents('form')[0].id;
//                         $(element).closest('.form-group').removeClass('has-error');
//                         $(element).closest('.help-block').remove();
//                         that.validateForm(true, type, true, formId);
//                     }
//                 };
//                 if (dataCollection[componentId].form) {
//                     validateOptions = $.extend(validateOptions, dataCollection[componentId].form);
//                 }
//                 $(form).validate(validateOptions);//init validate form
//                 if ($(form).data('validateon') === 'sections') {
//                     validateFormsOnSections.push(formId);
//                 }
//                 if ($(form).data('validateon') === 'datatable') {
//                     validateFormsOnDatatable.push(formId);
//                 }
//             });
//             if ($('div[data-validateon="sections"]').length !== 0) {
//                 $('div[data-validateon="sections"]').each(function (index, datatable) {
//                     if (!validateDatatableOnSections[$(datatable).parents('section')[0].id]) {
//                         validateDatatableOnSections[$(datatable).parents('section')[0].id] = [ ];
//                         validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
//                     } else {
//                         validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
//                     }
//                 });
//             }
//         }
//     };

//     //Validate Sections on Submit
//     BazValidator.prototype.validateForm = function(onSuccess, type, preValidated, formId) {
//         if (type === 'sections' || !type) {
//             formLocation = componentId;
//         } else if (type === 'datatable') {
//             formLocation = formId;
//         }
//         if (!preValidated) {
//             if (type === 'sections') {
//                 $.each(validateFormsOnSections, function(index, form) {
//                     $('#' + form).submit();
//                 });

//                 if (!($.isEmptyObject(validateDatatableOnSections))) {
//                     //Validating datatable if empty, throw error
//                     for (var sections in validateDatatableOnSections) {
//                         if (validateDatatableOnSections[sections].length > 0) {
//                             $.each(validateDatatableOnSections[sections], function(index, datatable) {
//                                 if (!tableData[sections][datatable].data().any()) {
//                                     $('#' + datatable + '-table-div').addClass('form-group has-error has-feedback');
//                                     $('#' + datatable + '-table-data').removeClass('border-default').addClass('border-danger');
//                                     $('#' + datatable + '-table-error').remove();
//                                     $('#' + datatable).append(
//                                         '<div id="' + datatable + '-table-error" class="text-danger help-block">Table cannot be empty!</div>'
//                                     );
//                                 }
//                             });
//                         }
//                     }
//                 }
//             } else if (type === 'datatable') {
//                 $('#' + formId).submit();
//             }
//             hasError = [];
//             $('#' + formLocation).find('.has-error').each(function(index,errorId) {
//                 var id = $(errorId).find('label').html();
//                 hasError.push(id.toUpperCase());
//             });
//             hasErrorCount = hasError.length;
//             if (!preValidated && hasErrorCount > 0) {
//                 $('#' + formLocation + '-alert').remove();
//                 $('#' + formLocation).before(
//                 '<div id="' + formLocation + '-alert" class="alert alert-danger alert-dismissible animated fadeIn">' +
//                 '   <button id="' + formLocation + '-alert-dismiss" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
//                 '   <i class="icon fa fa-ban"></i>You have <strong>'+ hasErrorCount + '</strong> errors! ' +
//                 '   Please fix these errors before submitting the data' +
//                 '<div>'
//                 );
//                 errorSound.play();
//                 if (type === 'sections') {
//                     if (sectionsJsTreeSelector) {
//                         that.fixHeight('fixedHeight');
//                         $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
//                             var word, words = [];
//                             var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
//                             if (searchFor.indexOf(',') >= 0) {
//                                 words = searchFor.split(',');
//                             } else {
//                                 words = [searchFor];
//                             }
//                             for (var i = 0; i < words.length; i++) {
//                                 word = words[i];
//                                 if ((node.text || "").indexOf(word) >= 0) {
//                                     if (node.text === word) {
//                                         return true;
//                                     }
//                                 }
//                             }
//                             return false;
//                         }
//                         $(sectionsJsTreeSelector).jstree(true).refresh();
//                         $('#' + formLocation + '-sections-tree').children('.box').removeClass('box-primary').addClass('box-danger');
//                         $('#' + formLocation + '-sections-tree').find('.box-header').children('strong').html(' Errors');
//                         $('#' + formLocation + '-sections-tree').find('.box-tools').addClass('hidden');
//                         $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-bars').addClass('fa-ban');
//                         $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
//                         $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
//                         $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
//                         $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').addClass('hidden');
//                         $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').removeClass('hidden');
//                         $('#' + formLocation + '-sections-fields-search').attr('disabled', true);
//                         $('#' + formLocation + '-sections-fields-search-cancel').click(function() {
//                             that.cancelValidatingForm(type, formLocation, false, formId);
//                         });
//                     }
//                     $('#' + formLocation + '-alert-dismiss').click(function() {
//                         that.cancelValidatingForm(type, formLocation, false, formId);
//                     });
//                 } else if (type === 'datatable') {
//                     $('#' + formLocation + '-alert-dismiss').click(function() {
//                         that.cancelValidatingForm(type, formLocation, false, formId);
//                     });
//                     return false;
//                 }
//             } else {
//                 if (type === 'datatable') {
//                     return true;
//                 }
//                 return true;
//             }
//         } else {
//             hasErrorCount = $('#' + formLocation).find('.has-error').length;
//             if (type === 'sections' || !type) {
//                 hasError = [];
//                 $('#' + formLocation).find('.has-error').each(function(index,errorId) {
//                     var id = $(errorId).children('label').html();
//                     hasError.push(id.toUpperCase());
//                 });
//                 if (hasErrorCount > 0) {
//                     $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
//                     if (sectionsJsTreeSelector) {
//                         $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
//                         $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
//                         $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
//                     }
//                 } else {
//                     if (!onSuccess) {
//                         that.cancelValidatingForm(type, formLocation, false, formId);
//                     } else {
//                         that.cancelValidatingForm(type, formLocation, true, formId);
//                     }
//                 }
//             } else if (type === 'datatable') {
//                 if (hasErrorCount > 0) {
//                     $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
//                     return false;
//                 } else {
//                     that.cancelValidatingForm(type, formLocation, false, formId);
//                     return true;
//                 }
//             }
//         }
//     };

//     //Cancel validating form
//     BazValidator.prototype.cancelValidatingForm = function (type, formLocation, jstreeRefresh, formId) {
//         $('#' + formLocation + '-alert').remove();
//         if (!type || type === 'sections') {
//             if (sectionsJsTreeSelector) {
//                 that.fixHeight('fixedHeight');
//                 $('#' + formLocation + '-sections-tree').children('.box').removeClass('box-danger').addClass('box-primary');
//                 $('#' + formLocation + '-sections-tree').find('.box-header').children('strong').html(' Sections');
//                 $('#' + formLocation + '-sections-tree').find('.box-tools').removeClass('hidden');
//                 $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-ban').addClass('fa-bars');
//                 $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');
//                 $('#' + formLocation + '-sections-fields-search').val('');
//                 $(sectionsJsTreeSelector).jstree(true).search('');
//                 $('#' + formLocation + '-sections-fields-search').attr('disabled', false);
//                 $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').removeClass('hidden');
//                 $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').addClass('hidden');
//                 $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
//                     var word, words = [];
//                     var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
//                     if (searchFor.indexOf(',') >= 0) {
//                         words = searchFor.split(',');
//                     } else {
//                         words = [searchFor];
//                     }
//                     for (var i = 0; i < words.length; i++) {
//                         word = words[i];
//                         if ((node.text || "").indexOf(word) >= 0) {
//                             return true;
//                         }
//                     }
//                     return false;
//                 }
//                 if (!jstreeRefresh && formId !== null) {
//                     that.redoSectionsJsTree();
//                 }
//             }
//         } else if (type === 'datatable') {
//             if ($('#' + formLocation).find('div').is('[data-bazscantype="jstree"]')) {
//                 $('#' + formLocation).find('[data-bazscantype="jstree"]').removeClass('border-danger').addClass('border-default');
//                 $('#' + formLocation).find('[type="search"]').removeClass('border-danger');
//                 $('#' + formLocation).find('[type="search"]').siblings('.input-group-addon').removeClass('bg-danger').addClass('bg-default');
//             }
//         }
//         $('#' + formLocation).find('.form-group').each(function(i,v) {
//             $(v).removeClass('has-error has-feedback');
//         });
//         $('#' + formLocation).find('.help-block').each(function(i,v) {
//             $(v).remove();
//         });
//         //Cancel Validating datatable
//         for (var sections in validateDatatableOnSections) {
//             if (validateDatatableOnSections[sections].length > 0) {
//                 $.each(validateDatatableOnSections[sections], function(index, datatable) {
//                     $('#' + datatable + '-table-data').removeClass('border-danger').addClass('border-default');
//                 });
//             }
//         }
//     };

//     // Plugin Definition
//     // =================
//     function Plugin(option) {
//         return this.each(function () {
//             var $this = $(this);
//             var data  = $this.data(DataKey);

//             if (!data) {
//                 var options = $.extend({}, Default, $this.data(), typeof option === 'object' && option);
//                 $this.data(DataKey, (data = new BazValidator($this, options)));
//             }

//             if (typeof data === 'string') {
//                 if (typeof data[option] === 'undefined') {
//                     throw new Error('Option for bazValidator needs to be object and not string');
//                 }
//                 data[option]();
//             }
//         });
//     }

//     var old = $.fn.bazValidator;

//     $.fn.bazValidator             = Plugin;
//     $.fn.bazValidator.Constructor = BazValidator;

//     // No Conflict Mode
//     // ================
//     $.fn.bazValidator.noConflict = function () {
//         $.fn.bazValidator = old;
//         return this;
//     };

// })(jQuery);
