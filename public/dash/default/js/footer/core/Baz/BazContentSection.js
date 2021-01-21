/* exported BazContentSection */
/* globals BazContentFields */
/*
* @title                    : BazContentSection
* @description              : Baz Core Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazContentSection._function_(_options_);
* @functions                : BazHeader, BazFooter, BazUpdateBreadcrumb
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

var BazContentSection = function() {
    var BazContentSection = void 0;
    var dataCollection = window.dataCollection;
    var componentId, sectionId;

    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Header
    function init(options) {
        componentId = $('#' + options.sectionId).parents('.component')[0].id;
        sectionId = options.sectionId;

        if (!dataCollection[componentId]) {
            dataCollection[componentId] = { };
        }
        if (!dataCollection[componentId][sectionId]) {
            dataCollection[componentId][sectionId] = { };
        }
        BazContentFields.init({
            'componentId'   : componentId,
            'sectionId'     : sectionId
        });
    }

    function bazContentSection() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazContentSectionConstructor) {
        BazContentSection = BazContentSectionConstructor;
        BazContentSection.defaults = {
            wizardId : null
        };
        BazContentSection.init = function(options) {
            init(_extends(BazContentSection.defaults, options));
        }
    }

    setup(bazContentSection);


    return bazContentSection;
}();
$(document).on('libsLoadComplete bazContentLoaderAjaxComplete bazContentLoaderModalComplete bazContentWizardAjaxComplete', function() {
    if ($('.section').length > 0) {
        $('.section').each(function() {
            BazContentSection.init({'sectionId' : $(this)[0].id});
        });
    }
});
