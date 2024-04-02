/**
 * bootstrap-strength-meter.js
 * https://github.com/davidstutz/bootstrap-strength-meter
 *
 * Copyright 2013 - 2019 David Stutz
 */
!function($) {

    "use strict";// jshint ;_;

    var StrengthMeter = {

        progressBar: function(input, options) {
            var defaults = {
                input: input.parent().siblings().children().find('input'),
                smallText: $(input).parents('.form-group').siblings('.input-small-text').children(),
                container: input.parent(),
                base: 5,
                hierarchy: {
                    '0': 'progress-bar-striped bg-danger',
                    '20': 'progress-bar-striped bg-danger',
                    '40': 'progress-bar-striped bg-warning',
                    '60': 'progress-bar-striped bg-info',
                    '80': 'progress-bar-striped bg-success',
                    '100': 'progress-bar-striped bg-success'
                }
            };

            var settings = $.extend(true, {}, defaults, options);

            if (typeof options === 'object' && 'hierarchy' in options) {
                settings.hierarchy = options.hierarchy;
            }

            var margin = '3';
            if (input.parents('.form-group').siblings('.input-small-text').children()) {
                margin = '1';
            }

            var template = '<div class="progress progress-xs mb-' + margin + '"><div class="progress-bar" role="progressbar"></div></div>';
            var progress;
            var progressBar;
            var passcheckTimeout;
            var core = {

                /**
                 * Initialize the plugin.
                 */
                init: function() {
                    progress = settings.container.append($(template));
                    progressBar = $('.progress-bar', progress);

                    progressBar.attr('aria-valuemin', 0)
                            .attr('aria-valuemax', 100);

                    settings.input.on('keyup change', core.keyup)
                            .keyup().change();
                },
                queue: function(event){
                    var password = $(event.target).val();
                    var value = 0;

                    if (password.length > 0) {
                        var pwstrengthUrl = settings.url;
                        var pwstrengthData = { };
                        pwstrengthData = {
                            'pass'              : password
                        }

                        if (settings.postData) {
                            pwstrengthData = $.extend(pwstrengthData, settings.postData);
                        }

                        if ($('#security-token')) {
                            pwstrengthData[$('#security-token').attr('name')] = $('#security-token').val();
                        }

                        $.post(pwstrengthUrl, pwstrengthData, function(response) {
                            if (response.responseCode == 0) {
                                core.update(response.responseData);
                            } else {
                                PNotify.error(response.responseMessage);
                            }
                            if (response.tokenKey && response.token) {
                                $("#security-token").attr("name", response.tokenKey);
                                $("#security-token").val(response.token);
                            }
                        }, 'json');
                    } else if (password.length === 0) {
                        core.update(0);
                    }
                },

                /**
                 * Update progress bar.
                 *
                 * @param {string} value
                 */
                update: function(value) {
                    if (value.result) {
                        value = value.result;
                    }

                    if (defaults.smallText.length > 0) {
                        var strengthText = ' ';

                        if (input.parents('.form-group').siblings().find('input').attr('disabled') !== 'disabled') {
                            strengthText = 'Enter Password to test strength';
                        }

                        if (value == '1') {
                            strengthText = ' (Weak Strength)';
                        } else if (value == '2') {
                            strengthText = ' (Normal Strength)';
                        } else if (value == '3') {
                            strengthText = ' (Good Strength)';
                        } else if (value == '4') {
                            strengthText = ' (Best Strength)';
                        }
                        if (value > 0) {
                            $(defaults.smallText).html('<span class="text-info text-uppercase">Password strength : ' + value + strengthText + '</span');
                        } else {
                            $(defaults.smallText).html('<span class="text-info text-uppercase">' + strengthText + '</span');
                        }
                    }
                    var width = Math.floor((value/settings.base)*100);

                    if (width > 100 || width >= 80) {
                        width = 100;
                    }

                    progressBar
                            .attr('area-valuenow', width)
                            .css('width', width + '%');

                    for (var value in settings.hierarchy) {
                        if (width == value) {
                            progressBar
                                    .removeClass()
                                    .addClass('progress-bar')
                                    .addClass(settings.hierarchy[value]);
                        }
                    }
                },

                /**
                 * Event binding on password input.
                 *
                 * @param {Object} event
                 */
                keyup: function(event) {
                    if(passcheckTimeout)clearTimeout(passcheckTimeout);
                    passcheckTimeout = setTimeout( function(){
                        core.queue(event);
                    },200);
                }
            };

            core.init();
        }
    };

    $.fn.strengthMeter = function(options) {
        var instance = this.data('strengthMeter');
        var elem = this;

        return elem.each(function() {
            var strengthMeter;

            if (instance) {
                return;
            }

            strengthMeter = StrengthMeter['progressBar'](elem, options);
            elem.data('strengthMeter', strengthMeter);
        });
    };

}(window.jQuery);
