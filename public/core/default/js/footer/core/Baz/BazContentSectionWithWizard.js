/* exported BazContentSectionWithWizard */
/* globals paginatedPNotify */
/*
* @title                    : BazContentSectionWithWizard
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSectionWithWizard._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSectionWithWizard = function() {
    var BazContentSectionWithWizard = void 0;
    var dataCollection = window.dataCollection;
    var componentId, sectionId , wizardOptions, originalTitle, steps, lastStep, review;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function init(options) {
        componentId = $(options.wizardId).parents('.component')[0].id;
        sectionId = $(options.wizardId)[0].id;

        if (!dataCollection[componentId]) {
            dataCollection[componentId] = { };
        }
        if (!dataCollection[componentId][sectionId]) {
            dataCollection[componentId][sectionId] = { };
        }

        steps = dataCollection[componentId][sectionId]['steps'];
        dataCollection[componentId][sectionId]['reviewHtml'] = '';
        review = dataCollection[componentId][sectionId]['reviewHtml'];
        wizardOptions = dataCollection[componentId][sectionId];

        if (wizardOptions['startAtStep'] != "0") {
            wizardOptions['currentStep'] = Number(wizardOptions['startAtStep']);
        } else {
            wizardOptions['currentStep'] = 0;
        }

        lastStep = wizardOptions.steps.length - 1;

        // ReviewDiv
        if (wizardOptions.showReview == true) {
            review = '<div class="accordion" id="' + sectionId + '-review-accordion"></div>';
            $('#' + sectionId + '-' + lastStep + '-data').html(review);
        }

        // Lets grab the component Ids & section Ids of the steps
        for (var step in steps) {
            if ($('#' + sectionId + '-' + step + '-data .component').length > 0) {
                steps[step]['componentId'] = $('#' + sectionId + '-' + step + '-data .component')[0].id;
            }

            if ($('#' + sectionId + '-' + step + '-data .section').length > 0) {
                steps[step]['sectionId'] = $('#' + sectionId + '-' + step + '-data .section')[0].id;
                steps[step]['type'] = 'section';
            }

            if ($('#' + sectionId + '-' + step + '-data .sectionWithForm').length > 0) {
                steps[step]['sectionId'] = $('#' + sectionId + '-' + step + '-data .sectionWithForm')[0].id;
                steps[step]['type'] = 'form';
                steps[step]['validate'] = true;
                if (wizardOptions.showReview) {
                    buildReview(step);
                    $('#' + sectionId + '-review-accordion').append(review);
                }
            }

            if ($('#' + sectionId + '-' + step + '-data .sectionWithList').length > 0) {
                steps[step]['sectionId'] = $('#' + sectionId + '-' + step + '-data .sectionWithList')[0].id;
                steps[step]['type'] = 'datatable';
            }

            if (steps[step]['ajax']) {
                $('#' + sectionId + '-' + step + '-data').load(dataCollection.env.rootPath + steps[step]['ajax']);
            }
        }

        // Make all contentAjaxLink to contentModalLink if section is Datatable
        // $('#' + sectionId + '-data .contentAjaxLink').addClass('contentModalLink').removeClass('contentAjaxLink');
        // Change Modal Size to xl
        // BazContentLoader.init({'modalSize' : 'xl'});

        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-step').addClass('current');

        if ($('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').length > 0) {
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').attr('hidden', false);
        }

        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-data').attr('hidden', false);

        originalTitle = $('#' + sectionId + ' div.card-header span.title').first().html();

        if (wizardOptions['startAtStep'] != "0") {
            for (var i = 0; i < wizardOptions['currentStep']; i++) {
                $('#' + sectionId + '-' + i + '-step').addClass('visited');
                if (steps[i]['type'] == 'form') {
                    steps[i]['submitted'] = true;
                }
            }
        }

        updateTitle();
        initWizardStepsButtons();
        hideHeaderFooter();

        // var runFirstTime = true;
        // $(document).ajaxComplete(function(e, xhr, settings) {
        //     //eslint-disable-next-line
        //     console.log(runFirstTime);
        //     if (runFirstTime) {
        //         for (var ajaxStep in steps) {
        //             var reviewBeforeId, reviewAfterId;
        //             var url = dataCollection.env.rootPath + steps[ajaxStep]['ajax'];
        //             if (url === settings.url) {
        //                 //eslint-disable-next-line
        //                 console.log(ajaxStep);
        //                 reviewBeforeId = Number(ajaxStep) - 1;
        //                 reviewAfterId = Number(ajaxStep) + 1;
        //                 if ($('#' + sectionId + '-' + ajaxStep + '-data .component').length > 0) {
        //                     steps[ajaxStep]['componentId'] = $('#' + sectionId + '-' + ajaxStep + '-data .component')[0].id;
        //                 }
        //                 if ($('#' + sectionId + '-' + ajaxStep + '-data .sectionWithForm').length > 0) {
        //                     steps[ajaxStep]['sectionId'] = $('#' + sectionId + '-' + ajaxStep + '-data .sectionWithForm')[0].id;
        //                     steps[ajaxStep]['type'] = 'form';
        //                     steps[ajaxStep]['validate'] = true;
        //                     if (wizardOptions.showReview) {
        //                         buildReview(ajaxStep);
        //                         if ($('#' + sectionId + '-' + reviewBeforeId + '-review').length > 0) {
        //                             $('#' + sectionId + '-' + reviewBeforeId + '-review').after(review);
        //                         } else if ($('#' + sectionId + '-' + reviewAfterId + '-review').length > 0) {
        //                             $('#' + sectionId + '-' + reviewAfterId + '-review').before(review);
        //                         }
        //                     }
        //                 }
        //                 hideHeaderFooter();
        //                 $('body').trigger('bazContentWizardAjaxComplete');
        //             }
        //         }
        //         runFirstTime = false;
        //     }
        // });
    }

    function updateTitle() {
        $('#' + sectionId + ' div.card-header span.title').addClass('text-uppercase');
        var title = originalTitle + ' : ' + steps[wizardOptions['currentStep']].title;
        $('#' + sectionId + ' div.card-header span.title').first().html(title);
    }

    function hideHeaderFooter() {
        $('#' + sectionId + '-data').children().each(function(index, child) {
            $('#' + child.id + ' .card-header').each(function() {
                if (!$(this).parents().hasClass('accordion')) {
                    $(this).attr('hidden', true);
                }
                if ($(this).children('ul').hasClass('nav-tabs')) {
                    $(this).attr('hidden', false);
                }
            });
            $('#' + child.id + ' .card-footer').each(function() {
                if (!$(this).parents().hasClass('accordion')) {
                    $(this).attr('hidden', true);
                }
            });
        });
    }

    function buildReview(step) {
        var stripComponentId;
        wizardOptions['steps'][step]['dataToSubmit'] = [];
        $('#' + sectionId + '-' + step + '-data .form-group').each(function(index, field) {
            if ($(field).find('[data-bazpostoncreate=true]').length > 0) {
                stripComponentId = $(field).find('[data-bazpostoncreate=true]')[0].id.split('-');
                wizardOptions['steps'][step]['dataToSubmit'].push({
                    id : stripComponentId[1],
                    title : $(field).children('label').text()
                });
            }
            // For Radio Buttons
            if ($(field).data('bazpostoncreate')) {
                stripComponentId = $(field)[0].id.split('-');
                wizardOptions['steps'][step]['dataToSubmit'].push({
                    id : stripComponentId[1],
                    title : $(field).children('label').text()
                });
            }
        });
        review =
            '<div class="card mb-0" id="' + sectionId + '-' + step + '-review">' +
                '<div class="card-header" id="' + sectionId + '-' + step + '-accordioncard-header">' +
                    '<h4 class="card-title">' +
                        '<button class="btn btn-link text-uppercase" type="button" data-toggle="collapse" data-target="#' +
                            sectionId + '-' + step + '-accordioncard" aria-control="' + sectionId + '-' + step + '-accordioncard">';
                            if (wizardOptions['steps'][step]['icon']) {
                                review += '<i class="fas fa-fw fa-' + wizardOptions['steps'][step]['icon'] + ' mr-1"></i>' + wizardOptions['steps'][step]['title']
                            } else {
                                review += wizardOptions['steps'][step]['title'];
                            }
                            review +=
                        '</button>' +
                    '</h4>' +
                '</div>' +
                '<div id="' + sectionId + '-' + step + '-accordioncard" class="collapse" area-labelledby="' +
                    sectionId + '-' + step + '-accordioncard-header" data-parent="#' + sectionId + '-review-accordion">' +
                    '<div class="card-body">' +
                    '</div>' +
                '</div>' +
            '</div>';
    }

    function initWizardStepsButtons() {
        $('#' + sectionId + '-previous').off();
        $('#' + sectionId + '-next').off();

        var nextDisabled = false;
        var nextHidden = false;

        if (wizardOptions.canCancel) {
            $('#' + sectionId + '-cancel').attr('hidden', false);
        }

        if (wizardOptions['steps'][wizardOptions['currentStep']]['nextDisabled'] == true) {
            nextDisabled = true;
        } else {
            nextDisabled = false;
        }
        if (wizardOptions['steps'][wizardOptions['currentStep']]['nextHidden'] == true) {
            nextHidden = true;
        } else {
            nextHidden = false;
        }

        if (wizardOptions['currentStep'] === 0) {
            $('#' + sectionId + '-previous').attr('hidden', true);
            $('#' + sectionId + '-next').attr('hidden', nextHidden);
            $('#' + sectionId + '-next').attr('disabled', nextDisabled);
            $('#' + sectionId + '-done').attr('hidden', true);
            $('#' + sectionId + '-submit').attr('hidden', true);
        } else if (wizardOptions['currentStep'] === lastStep) {
            if (wizardOptions['steps'][wizardOptions['currentStep']]['goBack'] != false) {
                $('#' + sectionId + '-previous').attr('hidden', false);
                $('#' + sectionId + '-previous').attr('disabled', false);
            } else {
                $('#' + sectionId + '-previous').attr('hidden', true);
            }
            $('#' + sectionId + '-next').attr('hidden', true);
        } else {
            if (wizardOptions['steps'][wizardOptions['currentStep']]['goBack'] != false) {
                $('#' + sectionId + '-previous').attr('hidden', false);
                $('#' + sectionId + '-previous').attr('disabled', false);
            } else {
                $('#' + sectionId + '-previous').attr('hidden', true);
            }
            $('#' + sectionId + '-next').attr('hidden', nextHidden);
            $('#' + sectionId + '-next').attr('disabled', nextDisabled);
            $('#' + sectionId + '-done').attr('hidden', true);
            $('#' + sectionId + '-submit').attr('hidden', true);
        }

        // Previous Button
        $('#' + sectionId + '-previous').click(function() {
            $('#' + sectionId + '-submit').off();
            var previousStep = wizardOptions['currentStep'] - 1;
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-step').addClass('visited').removeClass('current');
            $('#' + sectionId + '-' + previousStep + '-step').addClass('current');
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').attr('hidden', true);
            $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-data').attr('hidden', true);
            $('#' + sectionId + '-' + previousStep + '-description').attr('hidden', false);
            $('#' + sectionId + '-' + previousStep + '-data').attr('hidden', false);
            if (wizardOptions['steps'][wizardOptions['currentStep']]['onPrevious']) {
                wizardOptions['steps'][wizardOptions['currentStep']]['onPrevious']();
            }
            wizardOptions['currentStep'] = previousStep;
            updateTitle();
            initWizardStepsButtons();
        });

        // Next Button
        $('#' + sectionId + '-next').click(function() {
            $(this).children('i').attr('hidden', false);
            $(this).attr('disabled', true);
            // Validate form & extract data on successful validation
            if (steps[wizardOptions['currentStep']]['validate']) {
                $('#' + steps[wizardOptions['currentStep']]['sectionId']).BazContentSectionWithForm({
                    'task'      : 'validateForm'
                });

                if (dataCollection[steps[wizardOptions['currentStep']]['componentId']][steps[wizardOptions['currentStep']]['sectionId']]['formValidator'].numberOfInvalids() === 0) {
                    $('#' + steps[wizardOptions['currentStep']]['sectionId']).BazContentSectionWithForm({
                        'task'      : 'sectionToObj'
                    });

                    // Create Review Body for this step
                    var fields = '';
                    $.each(steps[wizardOptions['currentStep']]['dataToSubmit'], function(index, field) {
                        fields += '<div class="row"><div class="col text-bold">' + field.title + '</div><div class="col">: ';

                        if ($('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id).data('bazscantype') === 'select2') {
                            fields += $('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id + ' option:selected').html();
                        } else if ($('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id).data('bazscantype') === 'radio') {
                            fields += $('#' + steps[wizardOptions['currentStep']]['componentId'] + '-' + field.id + ' :checked').parent('label').text().trim();
                        } else {
                            fields += dataCollection[steps[wizardOptions['currentStep']]['componentId']][steps[wizardOptions['currentStep']]['sectionId']]['data'][field.id];
                        }
                        fields += '</div></div>';
                    });
                    $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-accordioncard .card-body').html(fields);

                    // Submit form if submitOnNext
                    if (steps[wizardOptions['currentStep']]['submitOnNext']) {
                        var formIdVal = $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-id').val();
                        if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').length > 0 &&
                            formIdVal === ''
                        ) {
                            doAjax(
                                $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').attr('actionurl'),
                                steps[wizardOptions['currentStep']]['componentId'],
                                steps[wizardOptions['currentStep']]['sectionId'],
                                wizardOptions['currentStep'],
                                false
                                );
                        } else if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').length > 0 &&
                                   formIdVal !== ''
                        ) {
                            doAjax(
                                $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').attr('actionurl'),
                                steps[wizardOptions['currentStep']]['componentId'],
                                steps[wizardOptions['currentStep']]['sectionId'],
                                wizardOptions['currentStep'],
                                false
                                );
                        }
                    } else {
                        goNext();
                    }
                } else {
                    $('#' + sectionId + '-next').children('i').attr('hidden', true);
                    $('#' + sectionId + '-next').attr('disabled', false);
                }
            } else {
                goNext();
            }
        });
        $('[data-toggle="tooltip"]').tooltip();
        $('[data-toggle="popover"]').popover('enable');
    }

    function goNext() {
        if (wizardOptions['steps'][wizardOptions['currentStep']]['onNext']) {
            var onNext = new Promise(wizardOptions['steps'][wizardOptions['currentStep']]['onNext']);

            onNext.then(function(success) {
                $('#' + sectionId + '-next').children('i').attr('hidden', true);
                $('#' + sectionId + '-next').attr('disabled', false);

                if (!success) {
                    return;
                }

                hideHeaderFooter();

                processGoNext();
            });
        } else {
            processGoNext();
        }
    }

    function processGoNext() {
        $('#' + sectionId + '-next').children('i').attr('hidden', true);
        $('#' + sectionId + '-next').attr('disabled', false);

        var nextStep = wizardOptions['currentStep'] + 1;
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-step').addClass('visited').removeClass('current');
        $('#' + sectionId + '-' + nextStep + '-step').addClass('current');
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-description').attr('hidden', true);
        $('#' + sectionId + '-' + wizardOptions['currentStep'] + '-data').attr('hidden', true);
        $('#' + sectionId + '-' + nextStep + '-description').attr('hidden', false);
        $('#' + sectionId + '-' + nextStep + '-data').attr('hidden', false);

        wizardOptions['currentStep'] = nextStep;
        if (wizardOptions['currentStep'] === lastStep) {
            if (wizardOptions.showReview) {
                $('#' + sectionId + '-review-accordion button').first().removeClass('collapsed');
                $('#' + sectionId + '-review-accordion .collapse').first().addClass('show');
            }
            $('#' + sectionId + '-next').attr('hidden', true);
            var submitButtonActive = null;
            for (var step in steps) {
                if (steps[step].type === 'form') {
                    if (!steps[step]['submitted']) {
                        $('#' + sectionId + '-submit').attr('hidden', false);//show submit if form has been previous submitted
                        submitButtonActive = true;
                        break;
                    }
                }
            }
            if (submitButtonActive) {
                $('#' + sectionId + '-submit').click(function() {
                    for (var step in steps) {
                        if (steps[step].type === 'form') {
                            if (!steps[step].submitted) {
                                var formIdVal = $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-id').val();
                                if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').length > 0 &&
                                    formIdVal === ''
                                ) {
                                    doAjax(
                                        $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-addData').attr('actionurl'),
                                        steps[wizardOptions['currentStep']]['componentId'],
                                        steps[wizardOptions['currentStep']]['sectionId'],
                                        wizardOptions['currentStep'],
                                        false
                                        );
                                } else if ($('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').length > 0 &&
                                           formIdVal !== ''
                                ) {
                                    doAjax(
                                        $('#' + steps[wizardOptions['currentStep']]['sectionId'] + '-updateData').attr('actionurl'),
                                        steps[wizardOptions['currentStep']]['componentId'],
                                        steps[wizardOptions['currentStep']]['sectionId'],
                                        wizardOptions['currentStep'],
                                        false
                                        );
                                }
                            }
                        }
                    }
                });
            } else {
                $('#' + sectionId + '-done').attr('hidden', false);//if all forms are submitted, then show done.
                $('#' + sectionId + '-done').click(function() {
                    $('#' + sectionId + '-review-accordion').collapse('dispose');
                });
            }
        }
        updateTitle();
        initWizardStepsButtons();
    }

    function doAjax(formUrl, formComponentId, formSectionId, step, lastStep) {
        wizardOptions['steps'][step]['submittedData'] = $.param(dataCollection[formComponentId][formSectionId].dataToSubmit);

        $.post(formUrl, $.param(dataCollection[formComponentId][formSectionId].dataToSubmit), function(response) {
            var success = false;

            wizardOptions['steps'][step]['responseCode'] = response.responseCode;
            wizardOptions['steps'][step]['responseMessage'] = response.responseMessage;

            if (response.responseCode == 0) {
                if (response.responseData) {
                    wizardOptions['steps'][step]['responseData'] = response.responseData;
                }
                wizardOptions['steps'][step]['submitted'] = true;
                success = true;
                $('#' + sectionId + '-' + step + '-accordioncard-header').removeClass('bg-danger').addClass('bg-success');
            } else {
                paginatedPNotify('error', {
                    title   : response.responseMessage,
                });
                $('#' + sectionId + '-next').children('i').attr('hidden', true);
                $('#' + sectionId + '-next').attr('disabled', false);
                $('#' + sectionId + '-' + step + '-accordioncard-header').removeClass('bg-success').addClass('bg-danger');
            }
            if (lastStep) {
                if ($('#' + sectionId + '-review-accordion .bg-danger').length === 0) {
                    $('#' + sectionId + '-submit').off();
                    $('#' + sectionId + '-previous').attr('hidden', true);
                    $('#' + sectionId + '-submit').attr('hidden', true);
                    $('#' + sectionId + '-done').attr('hidden', false);
                }
            } else if (!lastStep && success === true) {
                goNext();
            }
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
        }, 'json');
    }

    function bazContentSectionWithWizard() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentSectionWithWizardConstructor) {
        BazContentSectionWithWizard = BazContentSectionWithWizardConstructor;
        BazContentSectionWithWizard.defaults = { };
        BazContentSectionWithWizard.init = function(options) {
            init(_extends(BazContentSectionWithWizard.defaults, options));
        }
    }

    setup(bazContentSectionWithWizard);


    return bazContentSectionWithWizard;
}();
$(document).on('libsLoadComplete bazContentLoaderAjaxComplete', function() {
    if ($('.sectionWithWizard').length > 0) {
        $('.sectionWithWizard').each(function() {
            BazContentSectionWithWizard.init({'wizardId' : $(this)});
        });
    }
});
