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
                input: input.parent().siblings().children('input'),
                container: input.parent(),
                base: 5,
                hierarchy: {
                    '0': 'progress-bar-striped bg-danger',
                    '20': 'progress-bar-striped bg-danger',
                    '40': 'progress-bar-striped bg-warning',
                    '60': 'progress-bar-striped bg-warning',
                    '80': 'progress-bar-striped bg-success',
                    '100': 'progress-bar-striped bg-success'
                }
            };

            var settings = $.extend(true, {}, defaults, options);

            if (typeof options === 'object' && 'hierarchy' in options) {
                settings.hierarchy = options.hierarchy;
            }

            var template = '<div class="progress progress-xs mb-3"><div class="progress-bar" role="progressbar"></div></div>';
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

                        var pwstrengthData = {
                            'pass'              : password
                        }

                        pwstrengthData[$('#security-token').attr('name')] = $('#security-token').val();

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
