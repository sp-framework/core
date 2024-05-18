/* exported BazContentFieldsValidator */
/* globals */
/*
* @title                    : BazContentFieldsValidator
* @description              : Baz Content Fields Validator Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentFieldsValidator._function_(_options_);
* @functions                :
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

$('body').on('libsLoadComplete', function() {
    if ($.validator && !$.validator.methods.json) {
        $.validator.addMethod("json", function(value) {
            try {
                JSON.parse(value);
                return true;
            } catch (error) {
                return false;
            }
        }, 'Please enter correct JSON format data.');
    }
});

// eslint-disable-next-line no-unused-vars
var BazContentFieldsValidator = function() {
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
        formJsTreeSelector = null;
    var formValid = false;
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
        errorSound = dataCollection.env.sounds.swalSound //Error Sound for Swal

        formJsTreeSelector = null;
        if (options.formJsTreeSelector) {
            formJsTreeSelector = options.formJsTreeSelector;
        }

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
                    ignore: ':submit, :reset, :image, :disabled, :hidden, .ignore, .cr-slider',
                    onkeyup: false,
                    onclick: false,
                    submitHandler: function() { },
                    focusInvalid: false
                });

                validateOptions = {
                    errorElement: 'div',
                    errorPlacement: function (error,element) {
                        if (!formValid) {
                            $(element).parents('.form-group').append(error);
                            $(error).addClass('text-uppercase text-danger text-xs help-block');
                            $(element).closest('.form-group').addClass('has-feedback');
                        }
                    },
                    highlight: function (element) {
                        $(element).closest('.form-group').addClass('has-error');
                    },
                    // unhighlight: function () { },
                    success: function (element) {
                        if (!formValid) {
                            var type = $(element).parents('form').data('validateon');
                            var formId = $(element).parents('form')[0].id;
                            componentId = $(element).parents('.component')[0].id;

                            if ($(element).parents('.sectionWithForm').length > 0) {
                                sectionId = $(element).parents('.sectionWithForm')[0].id;
                            } else if ($(element).parents('.sectionWithFormToDatatable').length > 0) {
                                sectionId = $(element).parents('.sectionWithFormToDatatable')[0].id;
                            }

                            $(element).closest('.form-group').removeClass('has-error');
                            $(element).closest('.help-block').remove();

                            validateForm(componentId, sectionId, true, type, true, formId);
                        }
                    }
                };
                if (dataCollection[componentId][sectionId][sectionId + '-form']) {
                    validateOptions = _extends(validateOptions, dataCollection[componentId][sectionId][sectionId + '-form']);
                }

                dataCollection[componentId][sectionId]['formValidator'] = $(form).validate(validateOptions);//init validate form

                if ($(form).data('validateon') === 'section') {
                    validateForms[componentId][sectionId].push(formId);
                }
                if ($(form).data('validateon') === 'datatable') {
                    validateFormsOnDatatable.push(formId);
                }
            });
            if ($('div[data-validateon="section"]').length !== 0) {
                $('div[data-validateon="section"]').each(function (index, datatable) {
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
            formValid = false;
            if (type === 'section') {
                // formLocation = componentId;
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
            // } else if (type === 'section') {
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
                if (type === 'section') {
                    if (formJsTreeSelector) {
                        // BazContentFields.fixHeight('fixedHeight');
                        $(formJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
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
                        $(formJsTreeSelector).jstree(true).refresh();
                        // $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-primary').addClass('box-danger');
                        // $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Errors');
                        // $('#' + formLocation + '-sections-tree').find('.card-tools').addClass('hidden');
                        // $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-bars').addClass('fa-ban');
                        $(formJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-form-fields').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                        // $('#' + formLocation + '-form-fields-search-input').val(hasError.toString());
                        // $('#' + formLocation + '-form-fields-search-input').siblings('.input-group-addon').addClass('hidden');
                        // $('#' + formLocation + '-form-fields-search-input').siblings('.input-group-btn').removeClass('hidden');
                        // $('#' + formLocation + '-form-fields-search-input').attr('disabled', true);
                        // $('#' + formLocation + '-form-fields-search-cancel').click(function() {
                        //     cancelValidatingForm(type, formLocation);
                        // });
                    }
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        cancelValidatingForm(type, formLocation);
                    });
                    return false;
                // } else if (type === 'section') {
                //     $('#' + formLocation + '-alert-dismiss').click(function() {
                //         if ($(this).parents('.sectionWithForm').length > 0) {
                //             formLocation = $(this).parent().siblings('.sectionWithForm')[0].id;
                //         } else if ($(this).parents('.sectionWithFormToDatatable').length > 0) {
                //             formLocation = $(this).parent().siblings('.sectionWithFormToDatatable')[0].id;
                //         }
                //         cancelValidatingForm(type, formLocation);
                //     });
                //     return false;
                } else if (type === 'datatable') {
                    $('#' + formLocation + '-alert-dismiss').click(function() {
                        formLocation = $(this).parent().siblings('.sectionWithFormToDatatable')[0].id;
                        cancelValidatingForm(type, formLocation);
                    });
                    return false;
                }
            } else {
                if (type === 'datatable') {
                    return true;
                }
                formValid = true;
                return true;
            }
        } else {
            if (type === 'section') {

                hasError = [];
                $('#' + formLocation).find('.has-error').each(function(index,errorId) {
                    var id = $(errorId).find('label').html();
                    hasError.push(id.toUpperCase());
                });
                hasErrorCount = hasError.length;

                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    if (formJsTreeSelector) {
                        $(formJsTreeSelector).jstree(true).search(hasError.toString());
                        $('#' + formLocation + '-form-fields-tree-search-input').val(hasError.toString());
                        $('#' + formLocation + '-form-fields').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
                    }
                    return false;
                } else {
                    if (!onSuccess) {
                        cancelValidatingForm(type, formLocation);
                    } else {
                        cancelValidatingForm(type, formLocation);
                    }
                    return true;
                }
            // } else if (type === 'section') {
            //     hasErrorCount = $('#' + sectionId).find('.has-error').length;
            //     hasError = [];

            //     $('#' + sectionId).find('.has-error').each(function(index,errorId) {
            //         var id = $(errorId).children('label').html();
            //         hasError.push(id.toUpperCase());
            //     });

            //     if (hasErrorCount > 0) {
            //         $('#' + sectionId + '-alert').find('strong').html(hasErrorCount);
            //         return false;
            //     } else {
            //         if (!onSuccess) {
            //             cancelValidatingForm(type, sectionId);
            //         } else {
            //             cancelValidatingForm(type, sectionId);
            //         }
            //         return true;
            //     }
            } else if (type === 'datatable') {
                if (hasErrorCount > 0) {
                    $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
                    return false;
                } else {
                    cancelValidatingForm(type, formLocation);
                    return true;
                }
            }
        }
    }

    //Cancel validating form
    function cancelValidatingForm(type, formLocation) {
        $('#' + formLocation + '-alert').remove();

        if (type === 'section') {
            if (formJsTreeSelector) {
                // BazContentFields.fixHeight('fixedHeight');
                // $('#' + formLocation + '-sections-tree').children('.card').removeClass('box-danger').addClass('box-primary');
                // $('#' + formLocation + '-sections-tree').find('.card-header').children('strong').html(' Sections');
                // $('#' + formLocation + '-sections-tree').find('.card-tools').removeClass('hidden');
                // $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-ban').addClass('fa-bars');
                // $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');
                $('#' + formLocation + '-form-fields-tree-search-input').val('');
                $(formJsTreeSelector).jstree(true).search('');
                // $('#' + formLocation + '-sections-fields-search').attr('disabled', false);
                // $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').removeClass('hidden');
                // $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').addClass('hidden');
                $(formJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
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
                    $('#' + datatable + '-data').removeClass('border-danger').addClass('border-default');
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
        BazContentFieldsValidator.initValidator = function(options) {
            BazContentFieldsValidator.defaults = { };
            init(_extends(BazContentFieldsValidator.defaults, options));
            initValidator();
        }
        BazContentFieldsValidator.validateForm = function(options) {
            BazContentFieldsValidator.defaults = { };
            init(_extends(BazContentFieldsValidator.defaults, options));
            var validate =
                validateForm(options.componentId, options.sectionId, options.onSuccess, options.type, options.preValidated, options.formId);
            return validate;
        }
        BazContentFieldsValidator.cancelValidatingForm = function(options) {
            BazContentFieldsValidator.defaults = { };
            init(_extends(BazContentFieldsValidator.defaults, options));
            cancelValidatingForm(options.type, options.formLocation);
        }
    }

    setup(bazContentFieldsValidatorConstructor);

    return bazContentFieldsValidatorConstructor;
}();