/* exported BazContentFields */
/* globals paginatedPNotify flatpickr */
/*
* @title                    : BazContentFields
* @description              : Baz Lib for Content (Sections With Form)
* @developer                : guru@bazaari.com.au
* @usage                    : ('#'+ section/componentID).BazContentFields;
* @functions                :
* @options                  :
*/
// (function (global, factory) {
//     typeof exports === 'object' && typeof module !== 'undefined' ? factory(exports) :
//     typeof define === 'function' && define.amd ? define(['exports'], factory) :
//     (global = global || self, factory(global.BazLibs = {}));
// }(this, function (exports) {

//     var BazContentFields = function ($) {

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazContentFields = function() {
    var BazContentFields = void 0;
        // var NAME                    = 'BazContentFields';
        // var DATA_KEY                = 'baz.contentfields';
        // var EVENT_KEY               = "." + DATA_KEY;
        // var JQUERY_NO_CONFLICT      = $.fn[NAME];
        // var Event = {
        // };
        // var ClassName = {
        // };
        // var Selector = {
        // };
        // var Default = {
        // };
    var componentId,
        sectionId,
        pnotifySound,
        dataCollection;

    var minValText, maxValText, minLengthText, maxLengthText, thisFieldId;

    // Error
    function error(errorMsg) {
        throw new Error(errorMsg);
    }

    //Init
    function init(options) {
        componentId = options.componentId;
        sectionId = options.sectionId;
        dataCollection = window['dataCollection'];
        pnotifySound = dataCollection.env.sounds.pnotifySound;

        if ($('body').find('.flatpickr-calendar').length > 0) {
            $('body').find('.flatpickr-calendar').remove();
        }
        if ($('body').find('.dz-hidden-input').length > 0) {
            $('body').find('.dz-hidden-input').remove();
        }
    }

        // var BazContentFields = function () {
        //     function BazContentFields(element, settings) {
        //         that = this;
        //         this._element = element;
        //         this._settings = $.extend({}, Default, settings);

        //         if ($('body').find('.flatpickr-calendar').length > 0) {
        //             $('body').find('.flatpickr-calendar').remove();
        //         }
        //         if ($('body').find('.dz-hidden-input').length > 0) {
        //             $('body').find('.dz-hidden-input').remove();
        //         }

        //         this._init();
        //         if (this._settings.fieldId) {
        //             this._bazInitField();
        //         } else {
        //             this._bazInitFields();
        //         }
        //     }

        //     var _proto = BazContentFields.prototype;

        //     _proto._error = function(message) {
        //         throw new Error(message);
        //     };

        //     _proto._init = function _init() {
        //         componentId = $(this._element).parents('.component')[0].id;
        //         sectionId = $(this._element)[0].id;
        //         dataCollection = window['dataCollection'];
        //         pnotifySound = dataCollection.env.sounds.pnotifySound

        //         // Grab Components HTML Code (future use)
        //         // if (!dataCollection[componentId].html){
        //         //     dataCollection[componentId].html = $('#' + componentId).parents('.container-fluid').html();
        //         // }
        //         // dataCollection[componentId][sectionId].html = $('#' + sectionId).html();

        //         // TODO Decide what to do with section without any fields.
        //         // I can only think of tabs being made available via section, which needs to be initialized.
        //         // ALSO TABS CAN HAVE OPTION TO ENABLE A PARTICULAR TABID or FIRST TAB ID
        //     };

        //     _proto._bazInitField = function _bazInitField() {
        //     };

            // _proto._bazInitFields = function _bazInitFields() {
                // tableData[sectionId] = { };//building object used during save
    function initFields(options = null) {
        var fields;
        if (options && options.fieldId) {
            componentId = options.componentId;
            sectionId = options.sectionId;
            fields = $('#' + sectionId).find('#' + options.fieldId);
        } else {
            fields = $('#' + sectionId).find('[data-bazscantype]');
        }

        // Iterate through the component
        $(fields).each(function(index,bazScanField) {
            // if (bazScanField.tagName !== 'FIELDSET' && $(bazScanField).parents('fieldset').data('bazscantype') !== 'datatable') {
                if (dataCollection[componentId][sectionId][bazScanField.id]) {
                    dataCollection[componentId][sectionId][bazScanField.id].bazScanType = bazScanField.dataset.bazscantype;
                    if (bazScanField.dataset.bazscantype === 'input') {
                        initInput(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'select2') {
                        initSelect2(bazScanField.id, sectionId, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'checkbox') {
                        initCheckbox(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'radio' || bazScanField.dataset.bazscantype === 'radio-button-group') {
                        initRadio(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'flatpickr') {
                        initFlatpickr(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'colorpicker') {
                        initColorpicker(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'textarea') {
                        initTextarea(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'json') {
                        initJson(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'trumbowyg') {
                        initTrumbowyg(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'counters') {
                        initCounters(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'jstree') {
                        initJstree(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'html') {
                        initHTML(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'dropzone') {
                        initDropzone(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    } else if (bazScanField.dataset.bazscantype === 'datatable') {
                        initDatatable(bazScanField.id, dataCollection[componentId][sectionId][bazScanField.id]);
                    }
                } else {
                    error('Individual sections parameters missing for ' + bazScanField.id);
                }
            // }
        });
        dataCollection[componentId][sectionId]['initFields'] = true;
    }

    function maxLength(fieldId, options) {
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (fieldId.hasAttribute('minlength') ||
            fieldId.hasAttribute('maxlength') ||
            fieldId.hasAttribute('max')) {
            if (fieldId.hasAttribute('maxlength')) {
                maxLengthText = ' UsedChar: %charsTyped% MaxChar: %charsTotal%';
            } else {
                maxLengthText = '';
            }
            if (fieldId.hasAttribute('minlength')) {
                minLengthText = 'MinChar: ' + fieldId.attributes.minlength.value + ' ';
            } else {
                minLengthText = '';
            }
            if (fieldId.hasAttribute('min')) {
                minValText = 'MinVal: ' + fieldId.attributes.min.value + ' ';
                options.customMaxAttribute = 'min';
            } else {
                minValText = '';
            }
            if (fieldId.hasAttribute('max')) {
                maxValText = 'MaxVal: ' + fieldId.attributes.max.value + ' ';
                options.customMaxAttribute = 'max';
            } else {
                maxValText = '';
            }
            options = $.extend({
                currentInput            : $(fieldId),
                alwaysShow              : true,
                allowOverMax            : false,
                thresholdAmount         : 5,
                thresholdPercent        : 20,
                message                 : minValText + maxValText + minLengthText + maxLengthText,
                placement               : 'top-right-inside'
            }, options);
            dataCollection[componentId][sectionId][thisFieldId]['maxlength'] = $(fieldId).maxlength(options);
        }
    }

    // Restricts input for each element in the set of matched elements to the given fieldInputTypeTextFilter.
    function applyInputFilter(field, filter) {
        if (!$.fn.inputFilter) {
            (function($) {
                $.fn.inputFilter = function(inputFilter) {
                    return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function() {
                        if (inputFilter(this.value)) {
                            this.oldValue = this.value;
                            this.oldSelectionStart = this.selectionStart;
                            this.oldSelectionEnd = this.selectionEnd;
                        } else if (this.hasOwnProperty("oldValue")) {
                            this.value = this.oldValue;
                            this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                        } else {
                            this.value = "";
                        }
                    });
                };
            }(jQuery));
        }

        if (filter === 'int') {
            $(field).inputFilter(function(value) {
              return /^-?\d*$/.test(value);
            });
        } else if (filter === 'positiveInt') {
            $(field).inputFilter(function(value) {
              return /^\d*$/.test(value);
            });
        } else if (filter === 'positiveIntMax') {
            $(field).inputFilter(function(value) {
              return /^\d*$/.test(value) && (value === "" || parseInt(value) <= $(field).attr('max'));
            });
        } else if (filter === 'float') {
            $(field).inputFilter(function(value) {
              return /^-?\d*[.]?\d*$/.test(value);
            });
        } else if (filter === 'positiveFloat') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d*$/.test(value);
            });
        } else if (filter === 'positiveFloatMax') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d*$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
            });
        } else if (filter === 'percent') {
            $(field).inputFilter(function(value) {
              return /^-?\d*[.]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positivePercent') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positivePercentMax') {
            $(field).inputFilter(function(value) {
              return /^\d*[.]?\d{0,2}$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
            });
        } else if (filter === 'currency') {
            $(field).inputFilter(function(value) {
              return /^-?\d*[.,]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positiveCurrency') {
            $(field).inputFilter(function(value) {
              return /^\d*[.,]?\d{0,2}$/.test(value);
            });
        } else if (filter === 'positiveCurrencyMax') {
            $(field).inputFilter(function(value) {
              return /^\d*[.,]?\d{0,2}$/.test(value) && (value === "" || parseFloat(value) <= $(field).attr('max'));
            });
        } else if (filter === 'char') {
            $(field).inputFilter(function(value) {
              return /^[a-z]*$/i.test(value);
            });
        } else if (filter === 'hex') {
            $(field).inputFilter(function(value) {
              return /^[0-9a-f]*$/i.test(value);
            });
        }
    }

    function initInput(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        var buttonId, button, buttonArr;

        if (fieldId.previousElementSibling && fieldId.previousElementSibling.children[0]) {
            if (fieldId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
                buttonArr = fieldId.previousElementSibling.children[1].querySelectorAll('a');
                for (button = buttonArr.length - 1; button >= 0; button--) {
                    buttonId = buttonArr[button].id;
                    if (options[buttonId]) {
                        buttonArr[button].addEventListener('click', function(buttonId) {
                            options[buttonId.target.id]();//call function
                        }, false);
                    }
                }
            } else if (!fieldId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
                if (fieldId.previousElementSibling.children[0].tagName === 'BUTTON') {
                    buttonId = fieldId.previousElementSibling.children[0].id;
                    if (options[buttonId]) {
                        buttonId.addEventListener('click', function(buttonId) {
                            options[buttonId]();//call function
                        }, false);
                    }
                }
            }
        }
        if (fieldId.nextElementSibling && fieldId.nextElementSibling.children[0]) {
            if (fieldId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
                buttonArr = fieldId.nextElementSibling.children[1].querySelectorAll('a');
                for (button = buttonArr.length - 1; button >= 0; button--) {
                    buttonId = buttonArr[button].id;
                    if (options[buttonId]) {
                        buttonArr[button].addEventListener('click', function(buttonId) {
                            options[buttonId.target.id]();//call function
                        }, false);
                    }
                }
            } else if (!fieldId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
                if (fieldId.nextElementSibling.children[0].tagName === 'BUTTON') {
                    buttonId = fieldId.nextElementSibling.children[0].id;
                    if (options[buttonId]) {
                        buttonId.addEventListener('click', function(buttonId) {
                            options[buttonId]();//call function
                        }, false);
                    }
                }
            }
        }
        if ($(fieldId).attr('type') === 'text' && $(fieldId).data('fieldinputfilter')) {
            applyInputFilter($(fieldId), $(fieldId).data('fieldinputfilter'));
        }

        if ($(fieldId).attr('type') === 'password') {
            if ($(fieldId).parents('.form-group').siblings().children().length > 0 &&
                $(fieldId).parents('.form-group').siblings().children()[0].id ===
                    fieldId.id + '-strength-meter'
            ) {
                $('#' + fieldId.id + '-strength-meter').strengthMeter({
                    "url" : dataCollection.env.httpScheme + '://' + dataCollection.env.httpHost + '/' + dataCollection.env.appRoute + '/auth/checkPwStrength'
                });
            }

            if ($('#' + fieldId.id + '-visibility').length > 0) {
                $('#' + fieldId.id + '-visibility').off();
                $('#' + fieldId.id + '-visibility').click(function(e) {
                    e.preventDefault();

                    if ($(fieldId).attr('type') === 'password') {
                        $(fieldId).attr('type', 'text');
                        $('#' + fieldId.id + '-visibility').children('i').removeClass('fa-eye-slash').addClass('fa-eye');
                    } else if ($(fieldId).attr('type') === 'text') {
                        $(fieldId).attr('type', 'password');
                        $('#' + fieldId.id + '-visibility').children('i').removeClass('fa-eye').addClass('fa-eye-slash');
                    }
                });
            }

            if ($('#' + fieldId.id + '-password_generate').length > 0) {
                $('#' + fieldId.id + '-password_generate').off();
                $('#' + fieldId.id + '-password_generate').click(function(e) {
                    e.preventDefault();

                    var postData = { };
                    postData[$('#security-token').attr('name')] = $('#security-token').val();

                    if ($('#' + sectionId + '-password_policy_complexity').val() === 'simple' ||
                        $('#' + sectionId + '-password-policy-simple').length > 0
                    ) {
                        postData['passwordpolicycomplexity'] = 'simple';
                        if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '1' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '1'
                        ) {
                            postData['passwordpolicylengthmin'] = '4';
                        } else if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '2' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '2'
                        ) {
                            postData['passwordpolicylengthmin'] = '8';
                        } else if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '3' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '3'
                        ) {
                            postData['passwordpolicylengthmin'] = '10';
                        } else if ($('#' + sectionId + '-password_policy_simple_acceptable_level').val() == '4' ||
                            $('#' + sectionId + '-password-policy-simple').data('simple_acceptable_level') == '4'
                        ) {
                            postData['passwordpolicylengthmin'] = '12';
                        }
                        postData = $.extend(postData, $('#' + sectionId + '-password-policy-simple').data());
                    } else if ($('#' + sectionId + '-password_policy_complexity').val() === 'complex' ||
                               $('#' + sectionId + '-password-policy-complex').length > 0
                    ) {
                        postData['passwordpolicycomplexity'] = 'complex';
                        if ($('#' + sectionId + '-password_policy_complexity').val() === 'complex') {
                            postData['passwordpolicylengthmin'] = $('#' + sectionId + '-password_policy_length_min').val();
                            if (postData['passwordpolicylengthmin'] === '' || postData['passwordpolicylengthmin'] === '0') {
                                postData['passwordpolicylengthmin'] = 8;
                            }
                            postData['passwordpolicylengthmax'] = $('#' + sectionId + '-password_policy_length_max').val();
                            if (postData['passwordpolicylengthmax'] === '' || postData['passwordpolicylengthmax'] === '0') {
                                postData['passwordpolicylengthmax'] = 8;
                            }
                            postData['passwordpolicyuppercase'] = $('#' + sectionId + '-password_policy_uppercase')[0].checked;
                            if ($('#' + sectionId + '-password_policy_uppercase')[0].checked === true) {
                                postData['passwordpolicyuppercasemincount'] = $('#' + sectionId + '-password_policy_uppercase_min_count').val();
                                postData['passwordpolicyuppercasemaxcount'] = $('#' + sectionId + '-password_policy_uppercase_max_count').val();
                                postData['passwordpolicyuppercaseinclude'] = $('#' + sectionId + '-password_policy_uppercase_include').val();
                            }
                            postData['passwordpolicylowercase'] = $('#' + sectionId + '-password_policy_lowercase')[0].checked;
                            if ($('#' + sectionId + '-password_policy_lowercase')[0].checked === true) {
                                postData['passwordpolicylowercasemincount'] = $('#' + sectionId + '-password_policy_lowercase_min_count').val();
                                postData['passwordpolicylowercasemaxcount'] = $('#' + sectionId + '-password_policy_lowercase_max_count').val();
                                postData['passwordpolicylowercaseinclude'] = $('#' + sectionId + '-password_policy_lowercase_include').val();
                            }
                            postData['passwordpolicynumbers'] = $('#' + sectionId + '-password_policy_numbers')[0].checked;
                            if ($('#' + sectionId + '-password_policy_numbers')[0].checked === true) {
                                postData['passwordpolicynumbersmincount'] = $('#' + sectionId + '-password_policy_numbers_min_count').val();
                                postData['passwordpolicynumbersmaxcount'] = $('#' + sectionId + '-password_policy_numbers_max_count').val();
                                postData['passwordpolicynumbersinclude'] = $('#' + sectionId + '-password_policy_numbers_include').val();
                            }
                            postData['passwordpolicysymbols'] = $('#' + sectionId + '-password_policy_symbols')[0].checked;
                            if ($('#' + sectionId + '-password_policy_symbols')[0].checked === true) {
                                postData['passwordpolicysymbolsmincount'] = $('#' + sectionId + '-password_policy_symbols_min_count').val();
                                postData['passwordpolicysymbolsmaxcount'] = $('#' + sectionId + '-password_policy_symbols_max_count').val();
                                postData['passwordpolicysymbolsinclude'] = $('#' + sectionId + '-password_policy_symbols_include').val();

                            }
                            postData['passwordpolicyavoidsimilar'] = $('#' + sectionId + '-password_policy_avoid_similar')[0].checked;
                            if ($('#' + sectionId + '-password_policy_avoid_similar')[0].checked === true) {
                                postData['passwordpolicyavoidsimilarcharacters'] = $('#' + sectionId + '-password_policy_avoid_similar_characters').val();
                            }
                        } else if ($('#' + sectionId + '-password-policy-complex').length > 0) {
                            postData = $.extend(postData, $('#' + sectionId + '-password-policy-complex').data());
                        }
                    }

                    var url = dataCollection.env.httpScheme + '://' + dataCollection.env.httpHost + '/' + dataCollection.env.appRoute + '/auth/generatePw'
                    $.post(url, postData, function(response) {
                        if (response.responseCode == 0) {
                            paginatedPNotify('success',{
                                title   : response.responseMessage
                            });
                        } else {
                            paginatedPNotify('error',{
                                title   : response.responseMessage
                            });
                        }
                        if (response.responseData.password) {
                            $(fieldId).val(response.responseData.password).trigger('change');
                            if ($(fieldId).attr('type') === 'password') {
                                $(fieldId).attr('type', 'text');
                                $('#' + fieldId.id + '-visibility').children('i').removeClass('fa-eye-slash').addClass('fa-eye');
                            }
                        }
                        if (response.tokenKey && response.token) {
                            $("#security-token").attr("name", response.tokenKey);
                            $("#security-token").val(response.token);
                        }
                    }, 'json');
                });
            }
        }

        if ($('#' + fieldId.id + '-fieldEdit').length > 0) {
            $('#' + fieldId.id + '-fieldEdit').off();
            $('#' + fieldId.id + '-fieldEdit').click(function(e) {
                e.preventDefault();

                if ($(fieldId).attr('disabled') === 'disabled') {
                    $(fieldId).attr('disabled', false);
                } else {
                    $(fieldId).attr('disabled', true);
                }
            });
        }

        maxLength(thisFieldId, options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initSelect2(fieldId, sectionId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        options = $.extend({
            placeholder: 'MISSING PLACEHOLDER'
        }, options);

        if ($(fieldId).data('create') && $(fieldId).data('create') === true) {
            options = $.extend({
                tags: true,
                createTag: function(params) {
                    var term = $.trim(params.term);

                    if (term === '') {
                        return null;
                    }

                    return {
                        id: term.toLowerCase(),
                        text: term,
                        newTag: true
                    }
                }
            }, options);
        }

        dataCollection[componentId][sectionId][thisFieldId]['select2'] = $(fieldId).select2(options);
        // validation
        if (dataCollection[componentId][sectionId][sectionId + '-form'] &&
            dataCollection[componentId][sectionId][sectionId + '-form'].rules[thisFieldId] === 'required') {
            $(fieldId).on('select2:select', function() {
                $(this).valid();
            });
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initCheckbox(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initRadio(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        // Remove checked radio bg on toggle
        if ($(fieldId).find('label.btn').length > 0) {
            $(fieldId).find('label.btn').each(function() {
                $(this).click(function() {
                    $(this).siblings('label.btn').each(function() {
                        $(this).removeClass(function(index, css) {
                            return (css.match(/\bbg-\S+/g) || []).join(' ');
                        });
                    });
                });
            });
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initFlatpickr(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        // thisFieldId = fieldId;
        // fieldId = $('#' + fieldId).parent();
        options = $.extend({
            mode            : 'single',
            enableTime      : false,
            altInput        : true,
            altFormat       : "d-m-Y",
            dateFormat      : "d-m-Y",
        }, options);

        dataCollection[componentId][sectionId][fieldId]['flatpickr'] = flatpickr('#' + fieldId, options);

        if ($('#' + fieldId + '-clear').length > 0) {
            $('#' + fieldId + '-clear').click(function() {
                dataCollection[componentId][sectionId][fieldId]['flatpickr'].clear();
                dataCollection[componentId][sectionId][fieldId]['flatpickr'].close();
            });
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initColorpicker(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }

        dataCollection[componentId][sectionId][fieldId]['colorpicker'] = $('#' + fieldId).colorpicker(options);

        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initTextarea(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        maxLength(thisFieldId, options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initJson(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        maxLength(thisFieldId, options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initTrumbowyg(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        options = $.extend({
            imageWidthModalEdit: true,
            urlProtocol: true,
            tagsToRemove: ['script', 'link'],
            btnsDef: {
                image: {
                    dropdown: ['insertImage', 'base64', 'upload'],
                    ico: 'insertImage'
                },
                justifyFull: {
                    dropdown: ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ico: 'justifyFull'
                },
                unorderedList: {
                    dropdown: ['unorderedList', 'orderedList'],
                    ico: 'unorderedList'
                }
            },
            btns: [
                ['viewHTML', 'formatting', 'historyUndo', 'historyRedo'],
                ['fontfamily', 'fontsize', 'superscript', 'subscript'],
                ['strong', 'em', 'del', 'lineheight', 'preformatted', 'specialChars'],
                ['foreColor', 'backColor'],
                ['justifyFull'],
                ['unorderedList'],
                ['table', 'horizontalRule'],
                ['removeformat', 'fullscreen', 'template']
            ],
            plugins: {
                table: {
                    rows: 4,
                    columns: 4
                },
                resizimg: {
                    minSize: 64,
                    step: 16,
                }
            }
        }, options);
        dataCollection[componentId][sectionId][thisFieldId]['trumbowyg'] =
            $(fieldId).trumbowyg(options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initCounters(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initJstree(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        options = $.extend({ }, options);
        // Init
        dataCollection[componentId][sectionId][thisFieldId]['jstree'] = $(fieldId).jstree(options);

        var jstreeElements = $(fieldId).jstree().get_json('#',{'flat':true});
        if (jstreeElements.length === 0) {
            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().addClass('d-none');
            $('#' + thisFieldId + '-tree-search-input').val('');
            $('#' + thisFieldId + '-tools-add').attr('hidden', true);
            $('#' + thisFieldId + '-tools-edit').attr('hidden', true);
            $('#' + thisFieldId + '-tools-collapse').attr('hidden', true);
            $('#' + thisFieldId + '-tools-expand').attr('hidden', true);

            var emptyTreeText = 'NO ' + $('#' + thisFieldId + '-tree-div').siblings('label').html() + '!';
            if (options.bazJstreeOptions.treeEmptyText && options.bazJstreeOptions.treeEmptyText !== '') {
                emptyTreeText = options.bazJstreeOptions.treeEmptyText;
            }
            $('#' + thisFieldId + '-tree-div').append(
                '<span id="' + thisFieldId + '-tree-empty">' + emptyTreeText + '</span>'
            );

            if (options.afterInit) {
                options.afterInit(dataCollection, options);
            }
            return;
        }

        // Search
        if (options.bazJstreeOptions.search == null || options.bazJstreeOptions.search) {
            $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
            $('#' + thisFieldId + '-tree-search-input').off();
            $('#' + thisFieldId + '-tree-search-input').on('keyup', function() {
                $(fieldId).jstree(true).search($(this).val());
            });
        }

        var selectedNode;
        // Add New Node
        if (options.bazJstreeOptions.add == null || options.bazJstreeOptions.add) {
            $('#' + thisFieldId + '-tools-add').attr('hidden', false);
            $('#' + thisFieldId + '-tools-add').off();
            $('#' + thisFieldId + '-tools-add').click(function(e) {
                e.preventDefault();
                var thisFieldId = $(fieldId)[0].id;
                selectedNode = $(fieldId).jstree('get_selected', true);
                // Check if node are selected and only 1 is selected
                if ($(selectedNode).length !== 1) {
                    $('.ui-pnotify').remove();
                    paginatedPNotify('notice', {
                        title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                        text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to create a new node under it'
                    });
                    pnotifySound.play();
                    return false;
                } else {
                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().removeClass('d-none');
                    $('#' + thisFieldId + '-tree-add-input').focus();
                    $('#' + thisFieldId + '-tree-add-input-cancel').click(function() {
                        $('#' + thisFieldId + '-tree-add-input').val(null);
                        $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                        $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().addClass('d-none');
                        $('#' + thisFieldId + '-tree-add-input-success').off();
                    });
                    $('#' + thisFieldId + '-tree-add-input-success').click(function() {
                        modifyJsTree($(fieldId), thisFieldId, 'addNode', this, $('#' + thisFieldId + '-tree-add-input'), selectedNode, options.bazJstreeOptions.addFunction);
                    });
                    $('#' + thisFieldId + '-tree-add-input').keypress(function() {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if(keycode == '13'){
                            modifyJsTree($(fieldId), thisFieldId, 'addNode', this, $('#' + thisFieldId + '-tree-add-input-success'), selectedNode, options.bazJstreeOptions.addFunction);
                        }
                    });
                }
            });
        }
        // Edit Selected Node
        if (options.bazJstreeOptions.edit == null || options.bazJstreeOptions.edit) {
            $('#' + thisFieldId + '-tools-edit').attr('hidden', false);
            $('#' + thisFieldId + '-tools-edit').off();
            $('#' + thisFieldId + '-tools-edit').click(function(e) {
                e.preventDefault();
                selectedNode = $(fieldId).jstree('get_selected', true);
                var thisFieldId = $(fieldId)[0].id;
            // Check if node are selected and only 1 is selected
                if ($(selectedNode).length !== 1) {
                    $('.ui-pnotify').remove();
                    paginatedPNotify('notice', {
                        title: 'None or Multiple ' + options.bazJstreeOptions.treeName + ' selected!',
                        text: 'Please select only 1 ' + options.bazJstreeOptions.treeName + ' to rename',
                    });
                    pnotifySound.play();
                    return false;
                } else {
                    $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().addClass('d-none');
                    $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().removeClass('d-none');
                    $('#' + thisFieldId + '-tree-edit-input').val(selectedNode[0].text).focus();
                    $('#' + thisFieldId + '-tree-edit-input-cancel').click(function() {
                        $('#' + thisFieldId + '-tree-edit-input').val(null);
                        $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                        $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().addClass('d-none');
                        $('#' + thisFieldId + '-tree-edit-input-success').off();
                    });
                    $('#' + thisFieldId + '-tree-edit-input-success').click(function() {
                        modifyJsTree($(fieldId), thisFieldId, 'editNode', this, $('#' + thisFieldId + '-tree-edit-input'), selectedNode, options.bazJstreeOptions.editFunction);
                    });
                    $('#' + thisFieldId + '-tree-edit-input').keypress(function() {
                        var keycode = (event.keyCode ? event.keyCode : event.which);
                        if(keycode == '13'){
                            modifyJsTree($(fieldId), thisFieldId, 'editNode', this, $('#' + thisFieldId + '-tree-edit--input-success'), selectedNode, options.bazJstreeOptions.editFunction);
                        }
                    });
                }
            });
        }
        // Collapse all Nodes
        if (options.bazJstreeOptions.collapse == null || options.bazJstreeOptions.collapse) {
            $('#' + thisFieldId + '-tools-collapse').attr('hidden', false);
            $('#' + thisFieldId + '-tools-collapse').off();
            $('#' + thisFieldId + '-tools-collapse').click(function(e) {
                e.preventDefault();
                $(fieldId).jstree('deselect_all');
                $(fieldId).jstree('close_all');
            });
        }
        // Expand all Nodes
        if (options.bazJstreeOptions.expand == null || options.bazJstreeOptions.expand) {
            $('#' + thisFieldId + '-tools-expand').attr('hidden', false);
            $('#' + thisFieldId + '-tools-expand').off();
            $('#' + thisFieldId + '-tools-expand').click(function(e) {
                e.preventDefault();
                $(fieldId).jstree('deselect_all');
                $(fieldId).jstree('open_all');
            });
        }
        // First Open
        if (options.bazJstreeOptions.firstOpen == null || options.bazJstreeOptions.firstOpen) {
            var firstId = $(fieldId)[0].children[0].children[0].id;
            $(fieldId).jstree('open_node', firstId);
        }
        //All Open
        if (options.bazJstreeOptions.allOpen == null || options.bazJstreeOptions.allOpen) {
            $(fieldId).jstree('open_all');
        }
        //All Checked
        if (options.bazJstreeOptions.allChecked == null || options.bazJstreeOptions.allChecked) {
            $(fieldId).jstree('check_all');
        }
        // Show all children if root is clicked
        if (options.bazJstreeOptions.toggleAllChildren == null || options.bazJstreeOptions.toggleAllChildren) {
            $(fieldId).on('select_node.jstree', function(e, data) {
                if (data.node.children.length > 0) {
                    $(fieldId).jstree('open_all', data.node.id);
                }
            });
            $(fieldId).on('close_node.jstree', function(e, data) {
                $(fieldId).jstree('deselect_node', data.node.id);
            });
        }
        // Select only EndNode to perform actions
        if (options.bazJstreeOptions.selectEndNodeOnly == null || options.bazJstreeOptions.selectEndNodeOnly) {
            $(fieldId).on('select_node.jstree', function (e,data) {
                if (data.node.children.length > 0) {
                    $(fieldId).jstree('deselect_node', data.node.id);
                }
            });
        }
        //HideAll Jstree default icons (only works if fieldJstreeDoubleClickToggle is set to true)
        if (options.bazJstreeOptions.hideJstreeIcon == null || options.bazJstreeOptions.hideJstreeIcon) {
            $(fieldId).find('.jstree-ocl').hide();
            $(fieldId).on('open_node.jstree close_node.jstree', function() {
                $(fieldId).find('.jstree-ocl').hide();
            });
        }
        if ($(fieldId).parents('form').length !== 0) {
            if (options[$(fieldId).parents('form')[0].id] && options[$(fieldId).parents('form')[0].id].rules[$(fieldId)[0].id + '-validate'] === 'required') {
                $(fieldId).on('select_node.jstree', function() {
                    $('#' + $(this)[0].id + '-validate').val(null);
                    if ($(fieldId).jstree('get_selected', true).length > 0 ) {
                        $('#' + $(this)[0].id + '-validate').val('selected');
                        $('#' + $(this)[0].id + '-validate').valid();
                        $(fieldId).removeClass('border-danger').addClass('border-default');
                        $(fieldId).siblings('#' + $(this)[0].id + '-tree-search').find('.border-danger').removeClass('border-danger').addClass('border-default');
                        $(fieldId).siblings('#' + $(this)[0].id + '-tree-search').find('.bg-danger').removeClass('bg-danger').addClass('bg-default');
                    }
                });
            }
        }
        // ModifyJsTree
        function modifyJsTree(tree, optionsId, task, elthis, elthat, selectedNode, runFunction) {
            var thisFieldId = optionsId;
            if (task === 'addNode') {
                tree.jstree('create_node',
                    $('#' + selectedNode[0].id),
                    $('#' + optionsId + '-tree-add-input').val(),
                    'last',
                    function() {
                        tree.jstree('open_node', $('#' + selectedNode[0].id));
                    }
                );
                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                $('#' + thisFieldId + '-tree-add-input').parents('.form-group').first().addClass('d-none');
                $('#' + optionsId + '-tree-add-input').val(null);
                $(elthis).off();
                $(elthat).off();
                runFunction();
            } else if (task === 'editNode') {
                tree.jstree('rename_node',
                    $('#' + selectedNode[0].id),
                    $('#' + optionsId + '-tree-edit-input').val()
                );
                $('#' + thisFieldId + '-tree-search-input').parents('.form-group').first().removeClass('d-none');
                $('#' + thisFieldId + '-tree-edit-input').parents('.form-group').first().addClass('d-none');
                $('#' + optionsId + '-tree-edit-input').val(null);
                $(elthis).off();
                $(elthat).off();
                runFunction();
            }
        }

        if (options.bazJstreeOptions.replaceIdWithDataField && options.bazJstreeOptions.replaceIdWithDataField !== '') {
            var allNodes = $(fieldId).jstree().get_json('#',{'flat':true});

            for (var i = 0; i < allNodes.length; i++) {
                var newId = null;
                if (allNodes[i].data[options.bazJstreeOptions.replaceIdWithDataField]) {
                    newId = allNodes[i].data[options.bazJstreeOptions.replaceIdWithDataField];
                }
                if (newId) {
                    $(fieldId).jstree().set_id(allNodes[i], newId);
                }
            }
        }

        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initHTML(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit(dataCollection, options);
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initDropzone(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        dataCollection[componentId][sectionId][thisFieldId]['dropzone'] = $(fieldId).dropzone(options);
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

    function initDatatable(fieldId, options) {
        if (options.beforeInit) {
            options.beforeInit();
        }
        thisFieldId = fieldId;
        fieldId = document.getElementById(fieldId);
        if (options.dataTables) {
            for (var datatable in options.dataTables) {
                var datatableTable = options.dataTables[datatable];
                var datatableConfig = dataCollection[componentId][sectionId][datatableTable];
                if (datatableConfig) {
                    if (datatableConfig.bazdatatable && datatableConfig.bazdatatable.compareData) {
                        if (datatableConfig.bazdatatable.compareData.inclIds) {
                            var datatableIncludes = datatableConfig.bazdatatable.compareData.inclIds;
                            for (var datatableInclude in datatableIncludes) {
                                var toolTipTitle = $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title');
                                toolTipTitle += '<br><span>NOTE: Field should be unique</span>';
                                if (datatableIncludes[datatableInclude].length > 0) {
                                    toolTipTitle += '<br><span>UNIQUE KEYWORDS: ' + datatableIncludes[datatableInclude].toString() + '</span>';
                                }
                                $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title', toolTipTitle).addClass('text-warning');
                            }
                        }
                        // NOTE: exclude is very difficult to narrow. Avoid using excludes and use twig template {{fieldUnique}}
                    }
                } else {
                    error('Datatable ' + datatableTable + ' is defined, but no configuration assigned to it!')
                }
            }
            // this._fieldsToDatatable(fieldId);
        } else {
            error('Tables not assigned to ' + thisFieldId + '. They need to be assigned in an array, please see documentation');
        }
        if (options.afterInit) {
            options.afterInit(dataCollection, options);
        }
    }

            // BazContentFields._jQueryInterface = function _jQueryInterface(options) {
            //     var data = $(this).data(DATA_KEY);
            //     var _options = $.extend({}, Default, options);

            //     if (!data) {
            //         data = new BazContentFields($(this), _options);
            //         $(this).data(DATA_KEY, typeof _options === 'string' ? data : _options);
            //     }
            // };
    // }
    function bazContentFieldsConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentFieldsConstructor) {
        BazContentFields = BazContentFieldsConstructor;
        BazContentFields.defaults = { };
        BazContentFields.init = function(options) {
            if (options.fieldId) {
                initFields(_extends(BazContentFields.defaults, options));
            } else {
                init(_extends(BazContentFields.defaults, options));
                initFields();
            }
        }
    }

    setup(bazContentFieldsConstructor);

    return bazContentFieldsConstructor;

}();
//         return BazContentFields;

//         }();

//     $.fn[NAME] = BazContentFields._jQueryInterface;
//     $.fn[NAME].Constructor = BazContentFields;

//     $.fn[NAME].noConflict = function () {
//         $.fn[NAME] = JQUERY_NO_CONFLICT;
//         return BazContentFields._jQueryInterface;
//     };

//     return BazContentFields;
// }(jQuery);

// exports.BazContentFields = BazContentFields;

// Object.defineProperty(exports, '__esModule', { value: true });

// }));