// /* globals jsondiffpatch PNotify MobileDetect screenfull Ladda BootstrapDialog */
// (function ($){
//     'use strict';

//     //Global Variables
//     //Root Path
//     var rootPath = window.location.pathname; 
//     rootPath = window.location.origin + rootPath.match(/.*\//g);

//     //Sound Path
//     var soundPath = 'assets/application/dashboard/default/sounds/';
//     var pnotifySound = new Audio(rootPath + soundPath + 'pnotify.mp3'); //Notification sound
//     var errorSound = new Audio(rootPath + soundPath + 'swal.mp3'); //Error Sound for Swal

//     var componentId, //Component ID
//         that, //Alias for this
//         thatV, //Alias for Parent Loop (v)
//         thisElement, //Alias for Element
//         thisOptions, //Alias for Options
//         sectionId, //Section Id inside the component. Can have multiple section ID
//         sectionName, //Section Name inside the component
//         selectedTable, //To select which particular datatable, datatable form should submit.
//         fieldsetSectionId, //@TODO - Check if required
//         expandedBoxCount, //For Sections Group, check the amount of boxes that are expanded
//         expandedBoxes, //Array of expanded boxes
//         md, //Mobile Detect variable
//         checkIsMobile, //Boolean - to check if the device is mobile or not.
//         sectionsJsTreeSelector, //Sections Group JS Tree Selector ID
//         extractDatatableFieldsData, //Datatable extracted fields data array
//         hasErrorCount, //Error counts to show during validation.
//         // formLocation, //Location of form, either in section or in datatable.
//         // validateFormsOnSections, //Validation of form on section submit
//         // validateDatatableOnSections, //Validation of datatable on section submit
//         // validateFormsOnDatatable; //Validate datatable form on datable submit

//     var heights = { }; //For height calculation
//     var types = { }; //JSTree types
//     var dataTableFields = { }; //Datatable fields
//     var bazDataCollection = { }; //For all data collection. Section Data collection, has list of fields, whole section HTML, section fields data
//     // var scanOnElements = []; //Sections fields structure elements array, Used for scanning the fields type.
//     var sectionsOptions = { }; //Fields Options that are defined via the HTML JS script section.
//     var sectionIds = []; //@TODO - Array of section IDs. Do we need it?
//     var oldSectionsDataCollection = { }; //@TODO - Required by DIFF, do we need it?
//     var tableData = { }; //Datatable Data
//     var allData = { }; //@TODO - All Data from all fields, do we need it?
//     var multiTable = false;
//     var hasError = []; //Validation, list of fields that has errors 

//     var DataKey = 'bb.content'; //DataKey

//     //Default options. @TODO, we need to define any default options?
//     var Default = {
//         'contentType'    : 'sections',
//         'sectionWithForm': { },
//         'dataArray'      : [],
//         'dataId'         : '',
//         'dataURL'        : ''
//     };

//     // BazContent Class Definition
//     // =========================
//     var BazContent = function (element, options) {
//         that = this;
//         thisElement = element;
//         thisOptions = options;
        
//         componentId = element[0].id;
//         // Dropzone.autoDiscover = false;//disable globally so script can pick it up
//         // $.trumbowyg.svgPath = false;
//         // $('body').append(trumbowygSvgPath);
//         this.init(thisOptions.dataId, thisOptions.dataURL);

//     };

//     //Throw error
//     BazContent.prototype.error = function(errorMsg) {
//         throw new Error(errorMsg);
//     };

//     //Get all data from anywhere
//     BazContent.prototype.getAllData = function(dataType) {
//         if (dataType === 'bazDataCollection') {
//             allData = bazDataCollection;
//             return allData;
//         } else if (dataType === 'tableData') {
//             allData = tableData;
//             return allData;
//         } else if (dataType === 'sectionsOptions') {
//             allData = sectionsOptions;
//             return allData;
//         } else if (dataType === 'all') {
//             allData = Object.assign(bazDataCollection, tableData, sectionsOptions);
//             return allData;
//         }
//     };

//     //Init
//     BazContent.prototype.init = function(dataId, dataURL) {
//         // Helper function to capitalize first letter
//         // @todo - move to helper functions lib.
//         // function capitalizeFirstLetter(string) {
//         //  return string.charAt(0).toUpperCase() + string.slice(1);
//         // }
//         //Clean old flatpickr & dropzone
//         if ($('body').find('.flatpickr-calendar').length > 0) {
//             $('body').find('.flatpickr-calendar').remove();
//         }
//         if ($('body').find('.dz-hidden-input').length > 0) {
//             $('body').find('.dz-hidden-input').remove();
//         }

//         // if (top['baz-' + (componentId)]) {
//         //     sectionsOptions = top['baz-' + (componentId)];
//         // } else {
//         //     that.error('Individual sections parameters missing!');
//         // }
//         // Build the whole structure
//         that.buildBazDataCollectionObject();
//         that.initSectionFields();

//         dataTableFields[componentId] = { };//@TODO move to datatable lib

//         that.initValidator();
//         if (thisOptions.contentType === 'sectionsGroup') {
//             that.buildSectionsJsTree();
//             that.initSectionsJsTree();
//             that.sectionsButtons();
//             that.buildSectionsNavigators();
//             // Loop through all sections
//             $('.sections').each(function() {
//                 sectionId = $(this).attr('id');
//                 dataTableFields[componentId][sectionId] = { };
//                 bazDataCollection[componentId][sectionId] = { };
//                 that.initSectionFields(null, sectionId);
//             });
//             that.fixHeight('init');
//             if (dataId && dataURL) {
//                 that.dataArrayToSections(dataId, dataURL);
//             }
//         } else if (thisOptions.contentType === 'sections') {
//             $('.sectionWithForm').each(function() {
//                 sectionId = $(this).attr('id');
//                 dataTableFields[componentId][sectionId] = { }; //Check after adding datatbale to single section
//                 that.initSectionWithFormButtonsAndAction(sectionId,thisOptions.sectionWithForm.buttons);
//                 // devModeTools is true
//                 if (thisOptions.sectionWithForm.devModeTools) {
//                     that.initSectionWithFormDevModeTools(sectionId);
//                 }                
//             });
//             if (dataId && dataURL) {
//                 that.dataArrayToSections(dataId, dataURL);
//             }           
//         }
//         that.registerCustomEvents();
//         $(window).resize(function() {
//             that.fixHeight('fixedHeight');
//         });
//     }

//     //Collect all BazScan fields and generate object for later use
//     BazContent.prototype.buildBazDataCollectionObject = function() {
//         bazDataCollection[componentId] = { };
//         $('.sections').each(function() {
//             sectionId = $(this).attr('id');
//             bazDataCollection[componentId][sectionId] = { };
//             //Grab HTML of Section
//             // bazDataCollection[componentId][sectionId].html = $('#' + sectionId).html();
//             //Grab any object in form
//             if (top['baz-' + componentId][sectionId + '-form']) {
//                 bazDataCollection[componentId][sectionId].form = top['baz-' + componentId][sectionId + '-form'];
//             }
//             $('#' + sectionId).find('[data-bazscan]').each(function(index,bazScanFields) {
//                 if (bazScanFields.tagName !== 'FIELDSET' && $(bazScanFields).parents('fieldset').data('bazscantype') !== 'datatable') {
//                     bazDataCollection[componentId][bazScanFields.id] = { };
//                     bazDataCollection[componentId][bazScanFields.id].options = { };
//                     if (top['baz-' + componentId][bazScanFields.id]) {
//                         bazDataCollection[componentId][bazScanFields.id].options = top['baz-' + componentId][bazScanFields.id];
//                     } else {
//                         that.error('Individual sections parameters missing for ' + bazScanFields.id);
//                     }
//                     bazDataCollection[componentId][bazScanFields.id].type = $(bazScanFields).data('bazscantype');
//                 }
//             });
//             delete top['baz-' + componentId];
//         });
//         //eslint-disable-next-line
//         console.log(bazDataCollection);
//     }

//     // //Init all fields inside the section
//     BazContent.prototype.initSectionFields = function() {
//         // tableData[sectionId] = { };//building object used during save
//         var minValText, maxValText, minLengthText, maxLengthText;

//         // REVIEW THIS COMMENTED SECTION
//         // if (fieldId === null && sectionId) {
//         // //     scanOn = $('#' + sectionId).find('[data-bazscan]');
//         // } else if (fieldId && sectionId === null) {
//         // //     scanOn.push('#' + fieldId);
//         // }

//         for (var fieldId in bazDataCollection[componentId]) {
//             if (bazDataCollection[componentId][fieldId].type === 'inputTextNumber') {
//                 scanOnElementInputTextNumber(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'inputGroupTextNumber') {
//                 // scanOnElementInputGroupTextNumber(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'select2' || 
//                        bazDataCollection[componentId][fieldId].type === 'tableSelector') {
//                 scanOnElementSelect2(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'toggleCheckbox') {
//                 // scanOnElementToggleCheckbox(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'radio') {
//                 // scanOnElementRadio(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'trumbowyg') {
//                 // scanOnElementTrumbowyg(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'sunEditor') {
//                 // scanOnElementSunEditor(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'flatpickr') {
//                 // scanOnElementFlatpickr(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'jstree') {
//                 // scanOnElementJstree(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'datatable') {
//                 // scanOnElementDatatable(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             } else if (bazDataCollection[componentId][fieldId].type === 'dropzone') {
//                 // scanOnElementDropzone(bazDataCollection[componentId][fieldId], bazDataCollection[componentId][fieldId].options);
//             }
//         }        

//         function maxLength(fieldId, options) {
//             if (fieldId.hasAttribute('minlength') ||
//                 fieldId.hasAttribute('maxlength') ||
//                 fieldId.hasAttribute('max')) {
//                 if (fieldId.hasAttribute('maxlength')) {
//                     maxLengthText = ' UsedChar: %charsTyped% MaxChar: %charsTotal%';
//                 } else {
//                     maxLengthText = '';
//                 }                       
//                 if (fieldId.hasAttribute('minlength')) {
//                     minLengthText = 'MinChar: ' + fieldId.attributes.minlength.value + ' ';
//                 } else {
//                     minLengthText = '';
//                 }
//                 if (fieldId.hasAttribute('min')) {
//                     minValText = 'MinVal: ' + fieldId.attributes.min.value + ' ';
//                 } else {
//                     minValText = '';
//                 }
//                 if (fieldId.hasAttribute('max')) {
//                     maxValText = 'MaxVal: ' + fieldId.attributes.max.value + ' ';
//                 } else {
//                     maxValText = '';
//                 }                                               
//                 options = $.extend({
//                     alwaysShow: true,
//                     allowOverMax: false,
//                     warningClass: "label label-info",
//                     limitReachedClass: "label label-danger",
//                     message: minValText + maxValText + minLengthText + maxLengthText,
//                     placement: 'top-right-inside'
//                 }, options);
//                 $('#' + fieldId).maxlength(options);  
//             }
//         }

//         function scanOnElementInputTextNumber(fieldId, options) {
//             maxLength(fieldId, options);
//             if (options) {
//                 if (options.function) {
//                     options.function(that);
//                 }
//             }                
//         }

//         // function scanOnElementInputGroupTextNumber(fieldId, options) {
//         //     for (var i = array.length - 1; i >= 0; i--) {
//         //         var thisId = document.getElementById(array[i]);
//         //         var buttonId, button, buttonArr;
//         //         if (thisId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
//         //             buttonArr = thisId.previousElementSibling.children[1].querySelectorAll('a');
//         //             for (button = buttonArr.length - 1; button >= 0; button--) {
//         //                 buttonId = buttonArr[button].id;
//         //                 if (sectionsOptions[buttonId]) {
//         //                     buttonArr[button].addEventListener('click', function(buttonId) {
//         //                         sectionsOptions[buttonId.target.id]();//call function
//         //                     }, false);
//         //                 }
//         //             }
//         //         } else if (!thisId.previousElementSibling.children[0].classList.contains('dropdown-toggle')) {
//         //             if (thisId.previousElementSibling.children[0].tagName === 'BUTTON') {
//         //                 buttonId = thisId.previousElementSibling.children[0].id;
//         //                 if (sectionsOptions[buttonId]) {
//         //                     buttonId.addEventListener('click', function(buttonId) {
//         //                         sectionsOptions[buttonId]();//call function
//         //                     }, false);
//         //                 }
//         //             }
//         //         }
//         //         if (thisId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
//         //             buttonArr = thisId.nextElementSibling.children[1].querySelectorAll('a');
//         //             for (button = buttonArr.length - 1; button >= 0; button--) {
//         //                 buttonId = buttonArr[button].id;
//         //                 if (sectionsOptions[buttonId]) {
//         //                     buttonArr[button].addEventListener('click', function(buttonId) {
//         //                         sectionsOptions[buttonId.target.id]();//call function
//         //                     }, false);
//         //                 }
//         //             }
//         //         } else if (!thisId.nextElementSibling.children[0].classList.contains('dropdown-toggle')) {
//         //             if (thisId.nextElementSibling.children[0].tagName === 'BUTTON') {
//         //                 buttonId = thisId.nextElementSibling.children[0].id;
//         //                 if (sectionsOptions[buttonId]) {
//         //                     buttonId.addEventListener('click', function(buttonId) {
//         //                         sectionsOptions[buttonId]();//call function
//         //                     }, false);
//         //                 }
//         //             }
//         //         }
//         //         if (sectionsOptions[array[i]]) {
//         //             if (sectionsOptions[array[i]].function) {
//         //                 sectionsOptions[array[i]].function(that);
//         //             }
//         //         }
//         //         maxLength(thisId, array[i]);
//         //     }
//         // }

//         function scanOnElementSelect2(fieldId, options) {
//             options = $.extend({
//                 theme: 'default', 
//                 allowClear: true, 
//                 placeholder: 'MISSING PLACEHOLDER'
//             }, options);//Assign default them !important
//             $('#' + fieldId).select2(options);
//             //validation
//             if (sectionsOptions[thisId.form.id] && sectionsOptions[thisId.form.id].rules[fieldId] === 'required') {
//                 $('#' + fieldId).on('select2:select', function() {
//                     $(this).valid();
//                     //@todo - bug with validation counter. Change should trigger that.validateform again.
//                 });
//                 $('#' + fieldId).on('select2:unselect', function() {
//                     $(this).valid();
//                     //@todo - bug with validation counter. Change should trigger that.validateform again.
//                 });
//             }
//             if (options) {
//                 if (options.function) {
//                     options.function(that);
//                 }
//             }                
//         }

//         // function scanOnElementToggleCheckbox(fieldId, options) {
            
//         // }

//         // function scanOnElementRadio(fieldId, options) {
            
//         // }
//         // function scanOnElementDatatable(fieldId, options) {

//         // }
//         // function scanOnElementTrumbowyg(fieldId, options) {

//         // }
//         // function scanOnElementSunEditor(fieldId, options) {

//         // } 
//         // function scanOnElementFlatpickr(fieldId, options) {

//         // }
//         // function scanOnElementDropzone(fieldId, options) {

//         // }
//         // function scanOnElementJstree(fieldId, options) {

//         // }
//         // @TODO - Custom Functions

//         // for (var i=0, len=scanOn.length; i < len; i++) {
//         //     var optionsId = $(scanOn[i])[0].id;
//         //     if ($(scanOn[i]).data('bazscan')) {
//         //         if ($(scanOn[i]).data('bazscantype') === 'toggleCheckbox') {
//         //             //
//         //         } else if ($(scanOn[i]).data('bazscantype') === 'radio') {
//         //             //
//         //         } else if ($(scanOn[i]).parents().is('.input-group') || $(scanOn[i]).parents().is('.input-group-sm') || $(scanOn[i]).parents().is('.input-group-lg')) {

//         //         } else if ($(scanOn[i]).data('bazscantype') === 'flatpickr') {
//         //             $(scanOn[i]).flatpickr(sectionsOptions[optionsId]);
//         //         } else if ($(scanOn[i]).data('bazscantype') === 'dropzone') {
//         //             $(scanOn[i]).dropzone(sectionsOptions[optionsId]);
//         //         } else if ($(scanOn[i]).data('bazscantype') === 'select2' || $(scanOn[i]).data('bazscantype') === 'tableSelector') {

//         //         } else if ($(scanOn[i]).data('bazscantype') === 'trumbowyg') {
//         //             sectionsOptions[optionsId] = $.extend({
//         //                 imageWidthModalEdit: true,
//         //                 urlProtocol: true,
//         //                 tagsToRemove: ['script', 'link'],
//         //                 btnsDef: {
//         //                     image: {
//         //                         dropdown: ['insertImage', 'upload'],
//         //                         ico: 'insertImage'
//         //                     }
//         //                 },                      
//         //                 btns: [
//         //                     ['viewHTML', 'formatting'],
//         //                     ['historyUndo', 'historyRedo'],
//         //                     ['fontfamily', 'fontsize'],
//         //                     ['strong', 'em', 'del', 'lineheight', 'preformatted'],
//         //                     ['superscript', 'subscript'],
//         //                     ['foreColor', 'backColor'],
//         //                     ['link'],
//         //                     ['image'],
//         //                     ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
//         //                     ['unorderedList', 'orderedList'],
//         //                     ['table', 'horizontalRule'],
//         //                     ['removeformat'],
//         //                     ['fullscreen']
//         //                 ]                       
//         //             }, sectionsOptions[optionsId]);
//         //             $(scanOn[i]).trumbowyg(sectionsOptions[optionsId]);
//         //         } else if ($(scanOn[i]).data('bazscantype') === 'jstree') {
//         //                 sectionsOptions[optionsId] = $.extend({
//         //                     'core': {
//         //                         'themes': {
//         //                             'name': 'default',
//         //                             'dots': false
//         //                         },
//         //                         'dblclick_toggle': false,
//         //                         'check_callback': true,
//         //                         'multiple': false,
//         //                     },
//         //                     'plugins': ['search', 'types'],
//         //                     'search': {
//         //                         'show_only_matches': true
//         //                     },
//         //                     'types': {
//         //                         'default': {
//         //                             'icon': 'fa fa-chevron-right text-sm'
//         //                         }
//         //                     }
//         //                 }, sectionsOptions[optionsId]);                 
//         //             $(scanOn[i]).jstree(sectionsOptions[optionsId]);
//         //             var selectedNode;
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.add == null || sectionsOptions[optionsId].bazJstreeOptions.add) {
//         //                 $('#' + optionsId + '-tools-add').removeClass('hidden');
//         //                 $('#' + optionsId + '-tools-add').click(function() {
//         //                     selectedNode = $(scanOn[i]).jstree('get_selected', true);
//         //                     // Check if node are selected and only 1 is selected
//         //                     if ($(selectedNode).length !== 1) {
//         //                         PNotify.removeAll();
//         //                         new PNotify({
//         //                             title: 'No/Multiple ' + sectionsOptions[optionsId].bazJstreeOptions.treeName + ' selected!',
//         //                             text: 'Please select only 1 ' + sectionsOptions[optionsId].bazJstreeOptions.treeName + ' to create a new node under it',
//         //                             type: 'warning'
//         //                         });
//         //                         pnotifySound.play();                                
//         //                         return false;
//         //                     } else {
//         //                         $('#' + optionsId + '-tree-search').addClass('hidden');
//         //                         $('#' + optionsId + '-tree-edit').addClass('hidden');
//         //                         $('#' + optionsId + '-tree-add').removeClass('hidden');
//         //                         $('#' + optionsId + '-tree-add-input').focus();
//         //                         $('#' + optionsId + '-tree-add-cancel').click(function() {
//         //                             $('#' + optionsId + '-tree-add-input').val('');
//         //                             $('#' + optionsId + '-tree-search').removeClass('hidden');
//         //                             $('#' + optionsId + '-tree-add').addClass('hidden');
//         //                         });
//         //                         $('#' + optionsId + '-tree-add-success').click(function() {
//         //                             that.modifyJsTree($(scanOn[i]), optionsId, 'addNode', this, $('#' + optionsId + '-tree-add-input'), selectedNode, sectionsOptions[optionsId].bazJstreeOptions.addFunction);
//         //                         });
//         //                         $('#' + optionsId + '-tree-add-input').keypress(function() {
//         //                             var keycode = (event.keyCode ? event.keyCode : event.which);
//         //                             if(keycode == '13'){
//         //                                 that.modifyJsTree($(scanOn[i]), optionsId, 'addNode', this, $('#' + optionsId + '-tree-add-success'), selectedNode, sectionsOptions[optionsId].bazJstreeOptions.addFunction);
//         //                             }
//         //                         });
//         //                     }
//         //                 });
//         //             }
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.edit == null || sectionsOptions[optionsId].bazJstreeOptions.edit) {
//         //                 $('#' + optionsId + '-tools-edit').removeClass('hidden');
//         //                 $('#' + optionsId + '-tools-edit').click(function() {
//         //                 selectedNode = $(scanOn[i]).jstree('get_selected', true);
//         //                 // Check if node are selected and only 1 is selected
//         //                     if ($(selectedNode).length !== 1) {
//         //                         PNotify.removeAll();
//         //                         new PNotify({
//         //                             title: 'No/Multiple ' + sectionsOptions[optionsId].bazJstreeOptions.treeName + ' selected!',
//         //                             text: 'Please select only 1 ' + sectionsOptions[optionsId].bazJstreeOptions.treeName + ' to rename',
//         //                             type: 'warning'
//         //                         });
//         //                         pnotifySound.play();                                
//         //                         return false;
//         //                     } else {
//         //                         $('#' + optionsId + '-tree-search').addClass('hidden');
//         //                         $('#' + optionsId + '-tree-add').addClass('hidden');
//         //                         $('#' + optionsId + '-tree-edit').removeClass('hidden');
//         //                         $('#' + optionsId + '-tree-edit-input').val(selectedNode[0].text).focus();
//         //                         $('#' + optionsId + '-tree-edit-cancel').click(function() {
//         //                             $('#' + optionsId + '-tree-edit-input').val('');
//         //                             $('#' + optionsId + '-tree-search').removeClass('hidden');
//         //                             $('#' + optionsId + '-tree-edit').addClass('hidden');
//         //                         });
//         //                         $('#' + optionsId + '-tree-edit-success').click(function() {
//         //                             that.modifyJsTree($(scanOn[i]), optionsId, 'editNode', this, $('#' + optionsId + '-tree-edit-input'), selectedNode, sectionsOptions[optionsId].bazJstreeOptions.editFunction);
//         //                         });
//         //                         $('#' + optionsId + '-tree-edit-input').keypress(function() {
//         //                             var keycode = (event.keyCode ? event.keyCode : event.which);
//         //                             if(keycode == '13'){
//         //                                 that.modifyJsTree($(scanOn[i]), optionsId, 'editNode', this, $('#' + optionsId + '-tree-edit-success'), selectedNode, sectionsOptions[optionsId].bazJstreeOptions.editFunction);
//         //                             }
//         //                         });
//         //                     }
//         //                 });
//         //             }
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.collapse == null || sectionsOptions[optionsId].bazJstreeOptions.collapse) {
//         //                 $('#' + optionsId + '-tools-collapse').removeClass('hidden');
//         //                 $('#' + optionsId + '-tools-collapse').click(function() {
//         //                     $(scanOn[i]).jstree('deselect_all');
//         //                     $(scanOn[i]).jstree('close_all');
//         //                 });             
//         //             }
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.expand == null || sectionsOptions[optionsId].bazJstreeOptions.expand) {
//         //                 $('#' + optionsId + '-tools-expand').removeClass('hidden');
//         //                 $('#' + optionsId + '-tools-expand').click(function() {
//         //                     $(scanOn[i]).jstree('deselect_all');
//         //                     $(scanOn[i]).jstree('open_all');
//         //                 });                     
//         //             }
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.search == null || sectionsOptions[optionsId].bazJstreeOptions.search) {
//         //                 $('#' + optionsId + '-tree-search').removeClass('hidden');
//         //                 $('#' + optionsId + '-tree-search-input').on('keyup', function() {
//         //                     $(scanOn[i]).jstree(true).search($(this).val());
//         //                 });                     
//         //             }
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.firstOpen == null || sectionsOptions[optionsId].bazJstreeOptions.firstOpen) {
//         //                 var firstId = $(scanOn[i])[0].children[0].children[0].id;
//         //                 $(scanOn[i]).jstree('open_node', firstId);                      
//         //             }
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.toggleAllChildren == null || sectionsOptions[optionsId].bazJstreeOptions.toggleAllChildren) {
//         //                 $(scanOn[i]).on('select_node.jstree', function(e, data) {
//         //                     if (data.node.children.length > 0) {
//         //                         $(scanOn[i]).jstree('open_all', data.node.id);
//         //                     }
//         //                 });
//         //                 $(scanOn[i]).on('close_node.jstree', function(e, data) {
//         //                     $(scanOn[i]).jstree('deselect_node', data.node.id);
//         //                 });                     
//         //             }
//         //             if (sectionsOptions[optionsId].bazJstreeOptions.selectEndNodeOnly == null || sectionsOptions[optionsId].bazJstreeOptions.selectEndNodeOnly) {
//         //                 $(scanOn[i]).on('select_node.jstree', function (e,data) {
//         //                     if (data.node.children.length > 0) {
//         //                         $(scanOn[i]).jstree('deselect_node', data.node.id);
//         //                     }
//         //                 });
//         //             }
//         //             if (sectionsOptions[$(scanOn[i]).parents('form')[0].id] && sectionsOptions[$(scanOn[i]).parents('form')[0].id].rules[$(scanOn[i])[0].id + '-validate'] === 'required') {
//         //                 $(scanOn[i]).on('select_node.jstree', function() {
//         //                     $('#' + $(this)[0].id + '-validate').val('');
//         //                     if ($(scanOn[i]).jstree('get_selected', true).length > 0 ) {
//         //                         $('#' + $(this)[0].id + '-validate').val('selected');
//         //                         $('#' + $(this)[0].id + '-validate').valid();
//         //                         $(scanOn[i]).removeClass('border-danger').addClass('border-default');
//         //                         $(scanOn[i]).siblings('#' + $(this)[0].id + '-tree-search').find('.border-danger').removeClass('border-danger').addClass('border-default');
//         //                         $(scanOn[i]).siblings('#' + $(this)[0].id + '-tree-search').find('.bg-danger').removeClass('bg-danger').addClass('bg-default');
//         //                     }
//         //                 });
//         //             }
//         //         } else if ($(scanOn[i]).data('bazscantype') === 'datatable') {
//         //             if (sectionsOptions[optionsId]) {
//         //                 for (var datatable in sectionsOptions[optionsId].dataTables) {
//         //                     var datatableTable = sectionsOptions[optionsId].dataTables[datatable]
//         //                     var datatableConfig = sectionsOptions[sectionsOptions[optionsId].dataTables[datatable]];
//         //                     if (datatableConfig) {
//         //                         if (datatableConfig.bazdatatable && datatableConfig.bazdatatable.compareData) {
//         //                             if (datatableConfig.bazdatatable.compareData.inclIds) {
//         //                                 var datatableIncludes = datatableConfig.bazdatatable.compareData.inclIds;
//         //                                 for (var datatableInclude in datatableIncludes) {
//         //                                     var toolTipTitle = $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title');
//         //                                     toolTipTitle += '<br><span>NOTE: Field should be unique</span>';
//         //                                     if (datatableIncludes[datatableInclude].length > 0) {
//         //                                         toolTipTitle += '<br><span>UNIQUE KEYWORDS: ' + datatableIncludes[datatableInclude].toString() + '</span>';
//         //                                     }
//         //                                     $('#' + datatableInclude).parents('.form-group').find('label').siblings('i').attr('title', toolTipTitle).addClass('text-warning');
//         //                                 }
//         //                             }
//         //                             // NOTE: exclude is very difficult to narrow. Avoid using excludes and use twig template {{fieldUnique}}
//         //                         }
//         //                     } else {
//         //                         that.error('Datatable ' + datatableTable + ' is defined, but no configuration assigned to it!')
//         //                     }
//         //                 }
//         //                 that.fieldsToDatatable(optionsId);
//         //             } else {
//         //                 that.error('Tables not assigned to ' + optionsId + '. They need to be assigned in an array, please see documentation');
//         //             }
//         //         } else if ($(scanOn[i]).data('bazscantype') === 'text' || $(scanOn[i]).data('bazscantype') === 'number') {

//         //         }               
//         //         //If optionId has function, then execute function and pass (that) to the function
//         //         if (sectionsOptions[optionsId]) {
//         //             if (sectionsOptions[optionsId].function) {
//         //                 sectionsOptions[optionsId].function(that);
//         //             }
//         //         }           
//         //     }
//         // }
//     };

//     // //Init validator on form
//     // BazContent.prototype.initValidator = function() {
//     //     var formId, validateOptions;
//     //     validateFormsOnSections = [];
//     //     validateDatatableOnSections = { };
//     //     validateFormsOnDatatable = [];      
//     //     validateFormsOnSections = [];
//     //     if (!$.fn.validate) {
//     //         that.error('Validator not found!');
//     //     } else {
//     //         $('#' + componentId).find('form').each(function(index,form) {
//     //             formId = $(form)[0].id;
//     //             $.validator.setDefaults({
//     //                 debug: false,
//     //                 ignore: ":submit, :reset, :image, :disabled",
//     //                 onkeyup: false,
//     //                 onclick: false,
//     //                 submitHandler: function() { },
//     //                 focusInvalid: false
//     //             });
//     //             validateOptions = {
//     //                 errorElement: 'div',
//     //                 errorPlacement: function ( error, element ) {
//     //                     element.parents('.form-group').append(error);
//     //                     error.addClass('help-block');
//     //                     $(element).closest('.form-group').addClass('has-feedback');
//     //                 },
//     //                 highlight: function (element) {
//     //                     $(element).closest('.form-group').addClass('has-error');
//     //                 },
//     //                 // unhighlight: function (element) { },
//     //                 success: function (element) {
//     //                     var type = $('#' + element[0].id).parents('form').data('validateon');
//     //                     var formId = $('#' + element[0].id).parents('form')[0].id;
//     //                     $(element).closest('.form-group').removeClass('has-error');
//     //                     $(element).closest('.help-block').remove();
//     //                     that.validateForm(true, type, true, formId);
//     //                 }
//     //             };
//     //             if (sectionsOptions[formId]) {
//     //                 validateOptions = $.extend(validateOptions, sectionsOptions[formId]);
//     //             }
//     //             $(form).validate(validateOptions);//init validate form
//     //             if ($(form).data('validateon') === 'sections') {
//     //                 validateFormsOnSections.push(formId);
//     //             }
//     //             if ($(form).data('validateon') === 'datatable') {
//     //                 validateFormsOnDatatable.push(formId);
//     //             }               
//     //         });
//     //         if ($('div[data-validateon="sections"]').length !== 0) {
//     //             $('div[data-validateon="sections"]').each(function (index, datatable) {
//     //                 if (!validateDatatableOnSections[$(datatable).parents('section')[0].id]) {
//     //                     validateDatatableOnSections[$(datatable).parents('section')[0].id] = [ ];
//     //                     validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
//     //                 } else {
//     //                     validateDatatableOnSections[$(datatable).parents('section')[0].id].push(datatable.id);
//     //                 }
//     //             });
//     //         }
//     //     }
//     // };

//     // //Validate Sections on Submit
//     // BazContent.prototype.validateForm = function(onSuccess, type, preValidated, formId) {
//     //     if (type === 'sections' || !type) {
//     //         formLocation = componentId;
//     //     } else if (type === 'datatable') {
//     //         formLocation = formId;
//     //     }
//     //     if (!preValidated) {
//     //         if (type === 'sections') {
//     //             $.each(validateFormsOnSections, function(index, form) {
//     //                 $('#' + form).submit();
//     //             });
                
//     //             if (!($.isEmptyObject(validateDatatableOnSections))) {
//     //                 //Validating datatable if empty, throw error
//     //                 for (var sections in validateDatatableOnSections) {
//     //                     if (validateDatatableOnSections[sections].length > 0) {
//     //                         $.each(validateDatatableOnSections[sections], function(index, datatable) {
//     //                             if (!tableData[sections][datatable].data().any()) {
//     //                                 $('#' + datatable + '-table-div').addClass('form-group has-error has-feedback');
//     //                                 $('#' + datatable + '-table-data').removeClass('border-default').addClass('border-danger');
//     //                                 $('#' + datatable + '-table-error').remove();
//     //                                 $('#' + datatable).append(
//     //                                     '<div id="' + datatable + '-table-error" class="text-danger help-block">Table cannot be empty!</div>'
//     //                                 );
//     //                             }
//     //                         });
//     //                     }
//     //                 }
//     //             }
//     //         } else if (type === 'datatable') {
//     //             $('#' + formId).submit();
//     //         }
//     //         hasError = [];
//     //         $('#' + formLocation).find('.has-error').each(function(index,errorId) {
//     //             var id = $(errorId).find('label').html();
//     //             hasError.push(id.toUpperCase());
//     //         });
//     //         hasErrorCount = hasError.length;
//     //         if (!preValidated && hasErrorCount > 0) {
//     //             $('#' + formLocation + '-alert').remove();
//     //             $('#' + formLocation).before(
//     //             '<div id="' + formLocation + '-alert" class="alert alert-danger alert-dismissible animated fadeIn">' +
//     //             '   <button id="' + formLocation + '-alert-dismiss" type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
//     //             '   <i class="icon fa fa-ban"></i>You have <strong>'+ hasErrorCount + '</strong> errors! ' +
//     //             '   Please fix these errors before submitting the data' +
//     //             '<div>'
//     //             );
//     //             errorSound.play();
//     //             if (type === 'sections') {
//     //                 if (sectionsJsTreeSelector) {
//     //                     that.fixHeight('fixedHeight');
//     //                     $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
//     //                         var word, words = [];
//     //                         var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
//     //                         if (searchFor.indexOf(',') >= 0) {
//     //                             words = searchFor.split(',');
//     //                         } else {
//     //                             words = [searchFor];
//     //                         }
//     //                         for (var i = 0; i < words.length; i++) {
//     //                             word = words[i];
//     //                             if ((node.text || "").indexOf(word) >= 0) {
//     //                                 if (node.text === word) {
//     //                                     return true;
//     //                                 }
//     //                             }
//     //                         }
//     //                         return false;
//     //                     }
//     //                     $(sectionsJsTreeSelector).jstree(true).refresh();
//     //                     $('#' + formLocation + '-sections-tree').children('.box').removeClass('box-primary').addClass('box-danger');
//     //                     $('#' + formLocation + '-sections-tree').find('.box-header').children('strong').html(' Errors');
//     //                     $('#' + formLocation + '-sections-tree').find('.box-tools').addClass('hidden');
//     //                     $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-bars').addClass('fa-ban');
//     //                     $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
//     //                     $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');
//     //                     $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
//     //                     $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').addClass('hidden');
//     //                     $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').removeClass('hidden');
//     //                     $('#' + formLocation + '-sections-fields-search').attr('disabled', true);
//     //                     $('#' + formLocation + '-sections-fields-search-cancel').click(function() {
//     //                         that.cancelValidatingForm(type, formLocation, false, formId);
//     //                     });
//     //                 }
//     //                 $('#' + formLocation + '-alert-dismiss').click(function() {
//     //                     that.cancelValidatingForm(type, formLocation, false, formId);                   
//     //                 }); 
//     //             } else if (type === 'datatable') {
//     //                 $('#' + formLocation + '-alert-dismiss').click(function() {
//     //                     that.cancelValidatingForm(type, formLocation, false, formId);                   
//     //                 });
//     //                 return false;
//     //             }
//     //         } else {
//     //             if (type === 'datatable') {
//     //                 return true;
//     //             }
//     //             return true;
//     //         }
//     //     } else {
//     //         hasErrorCount = $('#' + formLocation).find('.has-error').length;
//     //         if (type === 'sections' || !type) {
//     //             hasError = [];
//     //             $('#' + formLocation).find('.has-error').each(function(index,errorId) {
//     //                 var id = $(errorId).children('label').html();
//     //                 hasError.push(id.toUpperCase());                    
//     //             });         
//     //             if (hasErrorCount > 0) {
//     //                 $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
//     //                 if (sectionsJsTreeSelector) {
//     //                     $(sectionsJsTreeSelector).jstree(true).search(hasError.toString());
//     //                     $('#' + formLocation + '-sections-fields-search').val(hasError.toString());
//     //                     $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').addClass('text-danger').css("text-transform", 'uppercase');                        
//     //                 }
//     //             } else {
//     //                 if (!onSuccess) {
//     //                     that.cancelValidatingForm(type, formLocation, false, formId);
//     //                 } else {
//     //                     that.cancelValidatingForm(type, formLocation, true, formId);
//     //                 }
//     //             }
//     //         } else if (type === 'datatable') {
//     //             if (hasErrorCount > 0) {
//     //                 $('#' + formLocation + '-alert').find('strong').html(hasErrorCount);
//     //                 return false;
//     //             } else {
//     //                 that.cancelValidatingForm(type, formLocation, false, formId);
//     //                 return true;
//     //             }
//     //         }
//     //     }
//     // };

//     // //Cancel validating form
//     // BazContent.prototype.cancelValidatingForm = function (type, formLocation, jstreeRefresh, formId) {
//     //     $('#' + formLocation + '-alert').remove();
//     //     if (!type || type === 'sections') {
//     //         if (sectionsJsTreeSelector) {
//     //             that.fixHeight('fixedHeight');
//     //             $('#' + formLocation + '-sections-tree').children('.box').removeClass('box-danger').addClass('box-primary');
//     //             $('#' + formLocation + '-sections-tree').find('.box-header').children('strong').html(' Sections');
//     //             $('#' + formLocation + '-sections-tree').find('.box-tools').removeClass('hidden');
//     //             $('#' + formLocation + '-sections-tree').find('.widget-icon').children('i').removeClass('fa-ban').addClass('fa-bars');
//     //             $('#' + formLocation + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');           
//     //             $('#' + formLocation + '-sections-fields-search').val('');
//     //             $(sectionsJsTreeSelector).jstree(true).search('');
//     //             $('#' + formLocation + '-sections-fields-search').attr('disabled', false);
//     //             $('#' + formLocation + '-sections-fields-search').siblings('.input-group-addon').removeClass('hidden');
//     //             $('#' + formLocation + '-sections-fields-search').siblings('.input-group-btn').addClass('hidden');
//     //             $(sectionsJsTreeSelector).jstree(true).settings.search.search_callback = function(str, node) {
//     //                 var word, words = [];
//     //                 var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
//     //                 if (searchFor.indexOf(',') >= 0) {
//     //                     words = searchFor.split(',');
//     //                 } else {
//     //                     words = [searchFor];
//     //                 }
//     //                 for (var i = 0; i < words.length; i++) {
//     //                     word = words[i];
//     //                     if ((node.text || "").indexOf(word) >= 0) {
//     //                         return true;
//     //                     }
//     //                 }
//     //                 return false;                           
//     //             }
//     //             if (!jstreeRefresh && formId !== null) {
//     //                 that.redoSectionsJsTree();
//     //             }
//     //         }
//     //     } else if (type === 'datatable') {
//     //         if ($('#' + formLocation).find('div').is('[data-bazscantype="jstree"]')) {
//     //             $('#' + formLocation).find('[data-bazscantype="jstree"]').removeClass('border-danger').addClass('border-default');
//     //             $('#' + formLocation).find('[type="search"]').removeClass('border-danger');
//     //             $('#' + formLocation).find('[type="search"]').siblings('.input-group-addon').removeClass('bg-danger').addClass('bg-default');
//     //         }
//     //     }
//     //     $('#' + formLocation).find('.form-group').each(function(i,v) {
//     //         $(v).removeClass('has-error has-feedback');
//     //     });
//     //     $('#' + formLocation).find('.help-block').each(function(i,v) {
//     //         $(v).remove();
//     //     });
//     //     //Cancel Validating datatable
//     //     for (var sections in validateDatatableOnSections) {
//     //         if (validateDatatableOnSections[sections].length > 0) {
//     //             $.each(validateDatatableOnSections[sections], function(index, datatable) {
//     //                 $('#' + datatable + '-table-data').removeClass('border-danger').addClass('border-default');
//     //             });
//     //         }
//     //     }
//     // };

//     // //Fix height to disable scrolling
//     // BazContent.prototype.fixHeight = function(task) {
//     //     function isMobile() {
//     //         md = new MobileDetect(window.navigator.userAgent);
//     //         if (md.mobile()) {
//     //             return true;
//     //         }
//     //     }

//     //     // Fix Layout (without scrollbar)
//     //     function calculateHeight() {
//     //         var layoutHeader, contentHeader, contentPadding, sectionsAlert, layoutFooter, boxHeight;
//     //         layoutHeader = $('.main-header').height();
//     //         if ($('.content-header').height() === 0) {
//     //             contentHeader = 81;
//     //         } else {
//     //             contentHeader = $('.content-header').height() + 10;
//     //         }
//     //         contentPadding = 10;
//     //         if (!$('.main-footer').outerHeight()) {
//     //             layoutFooter = 36;
//     //         } else {
//     //             layoutFooter = ($('.main-footer').outerHeight() + 1);
//     //         }
//     //         if ($('.alert').length > 0) {
//     //             sectionsAlert = $('#' + componentId + '-alert').outerHeight();
//     //             heights.content = $(window).height() - (layoutHeader + layoutFooter + contentPadding + contentHeader + sectionsAlert + 10);
//     //         } else {
//     //             heights.content = $(window).height() - (layoutHeader + layoutFooter + contentPadding + contentHeader);
//     //         }
//     //         // heights.sectionsHeader = $('#' + componentId + '-header').height() + 10;
//     //         heights.sections = ($('.sections').length * 24);//each box closed with margin-bottom
//     //         boxHeight = heights.content - heights.sections;
//     //         heights = { };
//     //         return boxHeight;
//     //     }

//     //     checkIsMobile = isMobile();

//     //     if (task === 'init' || task === "fixedHeight") {
//     //         if (!checkIsMobile) {
//     //             var boxHeight = calculateHeight();
//     //             $('.sections .box-body').each(function() {
//     //                 $(this).css({
//     //                     "max-height": boxHeight
//     //                 });
//     //                 $(this).addClass('overflow-y-scroll');
//     //             });
//     //             $('.sections-tree .box-body').css({
//     //                 "max-height": boxHeight
//     //             });
//     //             $('.sections-tree .box-body').addClass('overflow-y-scroll');
//     //         }
//     //     } else if (task === "noFixedHeight") {
//     //         $('.sections .box-body').css({
//     //                 "max-height": '',
//     //         });
//     //         $('.sections-tree .box-body').css({
//     //                 "max-height": '',
//     //         });
//     //         $('.box-body').removeClass('overflow-y-scroll');            
//     //     }

//     //     // Fullscreen redo fixed height
//     //     $(function () {
//     //         screenfull.onchange(function () {
//     //             expandedBoxCount = that.findExpandedBoxCount();
//     //             if (expandedBoxCount !== 0) {
//     //                 that.fixHeight('fixedHeight');
//     //             }
//     //         });
//     //     });     
//     // };
    
//     // //Find expanded boxes (used by fix height and jstree)
//     // BazContent.prototype.findExpandedBoxCount = function() {
//     //     expandedBoxes = $('.sections').find('.collapsed-box').length;
//     //     return expandedBoxes;
//     // };

//     // //Generate Sections JS Tree
//     // BazContent.prototype.buildSectionsJsTree = function() {
//     //     sectionsJsTreeSelector = $('#' + componentId + '-sections-jstree');
//     //     // Create Tree
//     //     $(sectionsJsTreeSelector).append('<ul></ul>');

//     //     // Grab Icons from Controller
//     //     types.item = {"icon" : "fa fa-chevron-right"};

//     //     // Grab Sections and tree structure Note: attr "jstree-search" is used to populate tree
//     //     $('.sections').each(function() {
//     //         sectionId = $(this).attr('id');
//     //         sectionName = $.parseHTML($(this).find('.box-header').children('strong').html())[0].data;
//     //         sectionIds.push(sectionId);
//     //         types[sectionId] = {"icon" : $('#' + sectionId).find('i').first().attr('class')};
//     //         $(sectionsJsTreeSelector).find('ul').first().append('<li data-jstree=' + '{"type":"' + sectionId + '"}>' + sectionName + '</li>');
//     //         $('#' + componentId + '-sections-jstree' + ' li:contains("' + sectionName + '")').first().append('<ul></ul>');
//     //         $('#' + sectionId).find("[jstree-search]").each(function() {
//     //             if ($(this).attr('jstree-search') !== '') {
//     //                 $('#' + componentId + '-sections-jstree' + ' li:contains("' + sectionName + '")').find('ul').first().append(
//     //                     '<li data-jstree=' + '{"type":' + '"item"}>' + $(this).attr('jstree-search') + '</li>'
//     //                     );
//     //             }
//     //         });
//     //     });
//     // };

//     // // Function to grab Parent of Selected Node.
//     // BazContent.prototype.uiGetSectionsJsTreeParents = function(data) {
//     //     try {
//     //         var lnLevel = data.node.parents.length;
//     //         var lsSelectedID = data.node.id;
//     //         var loParent = $("#" + lsSelectedID);
//     //         var lsParents = [];
//     //         for (var ln = 0; ln <= lnLevel -1 ; ln++) {
//     //             loParent = loParent.parent().parent();
//     //             if (loParent.children()[1] != undefined) {
//     //                 lsParents.push(loParent.children()[1].text);
//     //             }
//     //         }
//     //         return lsParents;
//     //     }
//     //     catch (err) {
//     //         alert('Error in uiGetParents');
//     //     }
//     // };

//     // //Init Sections JS Tree
//     // BazContent.prototype.initSectionsJsTree = function(previousOpenSectionText) {
//     //     sectionsJsTreeSelector = $('#' + componentId + '-sections-jstree');
//     //     // Init jstree
//     //     $(sectionsJsTreeSelector).jstree({
//     //         "core": {
//     //             "themes": {
//     //                 'name': 'default',
//     //                 'dots': false
//     //             },
//     //             "dblclick_toggle": false
//     //         },
//     //         "types" : types,
//     //         "plugins": ["search","types"],
//     //         "search": {
//     //             'show_only_matches': true,
//     //             'search_callback'  : function(str, node) {
//     //                 var word, words = [];
//     //                 var searchFor = str.toUpperCase().replace(/^\s+/g, '').replace(/\s+$/g, '');
//     //                 if (searchFor.indexOf(',') >= 0) {
//     //                     words = searchFor.split(',');
//     //                 } else {
//     //                     words = [searchFor];
//     //                 }
//     //                 for (var i = 0; i < words.length; i++) {
//     //                     word = words[i];
//     //                     if ((node.text || "").indexOf(word) >= 0) {
//     //                         return true;                                
//     //                     }
//     //                 }
//     //                 return false;
//     //             }
//     //         }
//     //     });

//     //     if (previousOpenSectionText) {
//     //         var previousOpenSectionId = $(sectionsJsTreeSelector).find('a:contains(' + $.parseHTML(previousOpenSectionText)[0].data + ')').parents('li')[0].id;
//     //         $(sectionsJsTreeSelector).jstree('open_node', previousOpenSectionId);
//     //         $(sectionsJsTreeSelector).jstree('select_node', previousOpenSectionId);
//     //     } else {
//     //         // Show 1st Section and 1st tree nodes
//     //         $('.sections div.box:gt(0)').boxWidget('collapse');
//     //         var firstId = $(sectionsJsTreeSelector)[0].children[0].children[0].id;
//     //         $(sectionsJsTreeSelector).jstree('open_node', firstId);
//     //         $(sectionsJsTreeSelector).jstree('select_node', firstId);
//     //     }

//     //     // Init jstree search
//     //     $('#' + componentId + '-sections-fields-search').on('keyup', function() {
//     //         $(sectionsJsTreeSelector).jstree(true).search($('#' + componentId + '-sections-fields-search').val());
//     //         $('#' + componentId + '-sections-jstree').find('.jstree-anchor').each(function(i,v) {
//     //             $(v).css("text-transform", 'uppercase');
//     //         });
//     //     });

//     //     // init jstree buttons
//     //     $('#expandTreeBoxes').click(function() {
//     //         $(sectionsJsTreeSelector).jstree('deselect_all');
//     //         $(sectionsJsTreeSelector).jstree('open_all');
//     //         $(sectionIds).each(function (e, section) {
//     //             $('#' + section + ' div.box').boxWidget('expand');
//     //             $('.box-footer').hide();
//     //         });
//     //             that.fixHeight('noFixedHeight');
//     //     });
//     //     $('#collapseTreeBoxes').click(function() {
//     //         $(sectionsJsTreeSelector).jstree('deselect_all');
//     //         $(sectionsJsTreeSelector).jstree('close_all');
//     //         $(sectionIds).each(function (e, section) {
//     //             $('#' + section + ' div.box').boxWidget('collapse');
//     //             $('.box-footer').hide();
//     //         });
//     //             that.fixHeight('fixedHeight');
//     //     });

//     //     $('#' + componentId + '-sections-jstree').find('.jstree-anchor').css("text-transform", 'uppercase');

//     //     // Init jstree selection process
//     //     $(sectionsJsTreeSelector).on('select_node.jstree', function(e, data) {
//     //         var selfId = $(this).jstree('get_selected',true)[0];
//     //         var selfParent = $(this).jstree('get_selected',true)[0].parent;
//     //         var selfName = $(this).jstree('get_selected',true)[0].text;

//     //         if (selfParent === '#') {
//     //             that.navigateToSectionField(selfId, sectionIds, selfName, '', false);
//     //         } else {
//     //             var parentName = that.uiGetSectionsJsTreeParents(data);
//     //             if (parentName.length === 1) {
//     //                 parentName = parentName[0];
//     //                 var node = true;
//     //                 that.navigateToSectionField(selfId, sectionIds, parentName, selfName, node);
//     //             }
//     //         }
//     //     });
//     // };

//     // //Build sections Navigators
//     // BazContent.prototype.buildSectionsNavigators = function() {
//     //     var boxCount = $('.sections div.box').length - 1;
//     //     expandedBoxCount = that.findExpandedBoxCount();
//     //     if (expandedBoxCount === 0) {
//     //         $('.sections div.box').each(function(i) {
//     //             if (i === 0) {
//     //                 $(this).find('.section-navigators').append(
//     //                     '<button type="button" data-toggle="tooltip" title="Next" class="btn btn-box-tool">' +
//     //                     '<i class="fa fa-chevron-down"></i></button>'
//     //                 );
//     //             } else if (i === boxCount) {
//     //                 $(this).find('.section-navigators').append(
//     //                     '<button type="button" data-toggle="tooltip" title="Previous" class="btn btn-box-tool">' +
//     //                     '<i class="fa fa-chevron-up"></i></button>'
//     //                 );
//     //             } else {
//     //                 $(this).find('.section-navigators').append(
//     //                     '<button type="button" data-toggle="tooltip" title="Previous" class="btn btn-box-tool">' +
//     //                     '<i class="fa fa-chevron-up"></i></button>' +
//     //                     '<button type="button" data-toggle="tooltip" title="Next" class="btn btn-box-tool">' +
//     //                     '<i class="fa fa-chevron-down"></i></button>'
//     //                 );
//     //             }
//     //         });
//     //     }   
//     //     // Register Box navigators actions
//     //     $('.section-navigators button').each(function() {
//     //         if ($(this).attr('title') === 'Next') {
//     //             $(this).click(function() {
//     //                 $(this).parents('section').children('.box').boxWidget('collapse');
//     //                 $(this).parents('section').next().children('.box').boxWidget('expand');
//     //             });
//     //         } else if ($(this).attr('title') === 'Previous') {
//     //             $(this).click(function() {
//     //                 $(this).parents('section').children('.box').boxWidget('collapse');
//     //                 $(this).parents('section').prev().children('.box').boxWidget('expand');
//     //             });
//     //         }
//     //     });     
//     // };

//     // //Navigate to field on jstree item selection
//     // BazContent.prototype.navigateToSectionField = function(id, boxes, boxName, nodeName, node) {
//     //     // function that.navigateToSectionField
//     //     // boxes: Ids of all sections (boxes)
//     //     // boxName: Selected selection (box) name
//     //     // nodeName: Selected Node (within box) name. attr "jstree-search" value
//     //     // node: true/false. If navigating between boxes, node === false, else node === true.
        
//     //     // Grab box ID from the box title
//     //     var boxId = $('#' + componentId + '-sections strong:contains("' + $.parseHTML(boxName)[0].data + '")').parents('section')[0].id;
//     //     var isCollapsed = $('#' + boxId + ' div.box').hasClass('collapsed-box');
//     //     var tabId, searchNodePosition;
        
//     //     // Exclude selected box to collape all other boxes
//     //     boxes = jQuery.grep(boxes, function(value) {
//     //         return value != boxId;
//     //     });
//     //     if (nodeName) {
//     //         var searchNode = $('#' + boxId + ' div.box').find('[jstree-search="' + $.parseHTML(nodeName)[0].data + '"]');
//     //         var isInTab = searchNode.parents().hasClass('tab-pane');
//     //     }
//     //     if (!node) {
//     //         if (!isCollapsed) {
//     //             $(sectionsJsTreeSelector).jstree('open_node', id);
//     //             return;
//     //         } else {
//     //             $(sectionsJsTreeSelector).jstree('close_all');
//     //             $(sectionsJsTreeSelector).jstree('open_node', id);
//     //                 $(boxes).each(function (e, boxid) {
//     //                     $('#' + boxid + ' div.box').boxWidget('collapse');
//     //             });
//     //             $('#' + boxId + ' div.box').boxWidget('expand');
//     //                 $('#' + boxId + ' div.box').off();
//     //             // $('#' + boxId + ' div.box').on('expanded.boxwidget', function() {
//     //             //  });
//     //         }
//     //     } else {
//     //         if (!isCollapsed) {
//     //             if (isInTab) {
//     //                 $(searchNode).parents('.tab-content').siblings().find('li').removeClass('active');
//     //                 $(searchNode).parents('.tab-content').children('.tab-pane').removeClass('active');
//     //                 $(searchNode).parents('.tab-pane').addClass('active');
//     //                 tabId = searchNode.parents('.tab-pane').attr('id');
//     //                 $(searchNode).parents('.tab-content').siblings().find('[href="#' + tabId + '"]').parent('li').addClass('active');
//     //             }
//     //             expandedBoxCount = that.findExpandedBoxCount();
//     //             if (expandedBoxCount === 0) {
//     //                 searchNodePosition = $('#' + searchNode.attr('id')).offset().top - 150; // For click scroll
//     //                 $('.content-wrapper, html').animate({scrollTop: searchNodePosition});                   
//     //                 searchNode.addClass('animated fadeIn bg-info-light disabled');
//     //                 setTimeout(function() {
//     //                     searchNode.removeClass('animated fadeIn bg-info-light disabled');
//     //                 }, 2000);
//     //             } else {
//     //                 searchNodePosition = $('#' + searchNode.attr('id')).offset().top - $('#' + searchNode.attr('id')).parents('.box-body').offset().top; // For click scroll
//     //                 $('#' + boxId + ' div.box-body').animate({scrollTop: searchNodePosition});
//     //                 searchNode.addClass('animated fadeIn bg-info-light disabled');
//     //                 setTimeout(function() {
//     //                     searchNode.removeClass('animated fadeIn bg-info-light disabled');
//     //                 }, 2000);                   
//     //             }
//     //         } else {
//     //                 $(boxes).each(function (e, boxid) {
//     //                     $('#' + boxid + ' div.box').boxWidget('collapse');
//     //                 });
//     //             $('#' + boxId + ' div.box').boxWidget('expand');
//     //                 $('#' + boxId + ' div.box').off();
//     //             // $('#' + boxId + ' div.box').on('expanded.boxwidget', function() {
//     //             // });
//     //             if (isInTab) {
//     //                 $(searchNode).parents('.tab-content').siblings().find('li').removeClass('active');
//     //                 $(searchNode).parents('.tab-content').children('.tab-pane').removeClass('active');
//     //                 $(searchNode).parents('.tab-pane').addClass('active');
//     //                 tabId = searchNode.parents('.tab-pane').attr('id');
//     //                 $(searchNode).parents('.tab-content').siblings().find('[href="#' + tabId + '"]').parent('li').addClass('active');

//     //             }
//     //             expandedBoxCount = that.findExpandedBoxCount();
//     //             if (expandedBoxCount === 0) {
//     //                 searchNodePosition = $('#' + searchNode.attr('id')).offset().top - 150; // For click scroll
//     //                 $('.content-wrapper, html').animate({scrollTop: searchNodePosition});
//     //                 searchNode.addClass('animated fadeIn bg-info-light disabled');
//     //                 setTimeout(function() {
//     //                     searchNode.removeClass('animated fadeIn bg-info-light disabled');
//     //                 }, 2000);
//     //             } else {
//     //                 searchNodePosition = $('#' + searchNode.attr('id')).offset().top - $('#' + searchNode.attr('id')).parents('.box-body').offset().top; // For click scroll
//     //                 $('#' + boxId + ' div.box-body').animate({scrollTop: searchNodePosition});
//     //                 searchNode.addClass('animated fadeIn bg-info-light disabled');
//     //                 setTimeout(function() {
//     //                     searchNode.removeClass('animated fadeIn bg-info-light disabled');
//     //                 }, 2000);                   
//     //             }
//     //         }
//     //     }
//     // };

//     // //Redo Sections tree on data change(hidden, disabled, etc)
//     // BazContent.prototype.redoSectionsJsTree = function() {
//     //     // RedoJSTree
//     //     var openSection;
//     //     if ($(sectionsJsTreeSelector).jstree('get_selected',true)[0]) {
//     //         if ($(sectionsJsTreeSelector).jstree('get_selected',true)[0].parent === '#') {
//     //             openSection = $(sectionsJsTreeSelector).jstree('get_selected',true)[0].text;
//     //         } else {
//     //             var openSectionParent = $(sectionsJsTreeSelector).jstree('get_selected',true)[0].parent;
//     //             openSection = $(sectionsJsTreeSelector).find('#' + openSectionParent).children('a').text();
//     //         }
//     //         $(sectionsJsTreeSelector).jstree('destroy');
//     //         that.buildSectionsJsTree();
//     //         that.initSectionsJsTree(openSection);
//     //         that.cancelValidatingForm('sections', componentId, true, true);//cancel any form validation as jstree has changed
//     //     }
//     // }

//     // //Delete a section
//     // BazContent.prototype.removeSection = function(sectionId) {
//     //     $('#' + sectionId).remove();
//     //     delete dataTableFields[componentId][sectionId];
//     //     delete bazDataCollection[componentId][sectionId];      
//     //     that.fixHeight('fixedHeight');//Fix Height after remove
//     //     that.redoSectionsJsTree();//Redo Sections Tree after remove
//     //     that.initValidator();//reinit validator after remove
//     // };

//     // //re-init a section
//     // BazContent.prototype.reinitSection = function(position, positionSectionId, route, sectionId, sectionIcon, sectionTitle) {
//     //     var reBuildSectionTemplate = 
//     //         '<section id="' + sectionId + '" class="sections">' + 
//     //             '<div class="box box-solid box-primary collapsed-box" data-widget="box-widget">' +
//     //                 '<div class="box-header with-border">' +
//     //                     '<span class="widget-icon">' +
//     //                         '<i class="fa fa-fw fa-' + sectionIcon + '"></i>' +
//     //                     '</span>' + 
//     //                     '<strong>' + sectionTitle + '</strong>' +
//     //                     '<div class="box-tools pull-right">' +
//     //                         '<div class="section-navigators"></div>' +
//     //                     '</div>' +
//     //                 '</div>' +
//     //                 '<div class="box-body overflow-y-scroll" style="display: none;">' +
//     //                 '</div>' + 
//     //             '</div>' +
//     //         '</section>';

//     //     if (position === 'before') {
//     //         $('#' + positionSectionId).before(reBuildSectionTemplate);
//     //     } else if (position === 'after') {
//     //         $('#' + positionSectionId).after(reBuildSectionTemplate);
//     //     } else {
//     //         $('#' + componentId).find('.box-group').append(reBuildSectionTemplate);
//     //     }

//     //     $.get(rootPath + 'index.php?route=' + route, function(data){
//     //         $('#' + sectionId).find('.box-body').html(data);
//     //     });

//     //     that.redoSectionsJsTree();//Redo Sections Tree after add
//     //     that.initValidator();//reinit validator after add
//     //     dataTableFields[componentId][sectionId] = { };
//     //     bazDataCollection[componentId][sectionId] = { };
//     //     that.initSectionFields(null, sectionId);
//     //     that.fixHeight('fixedHeight');//Fix Height after add
//     // };  

//     // //Sections Buttons (Bubblemenu)
//     // BazContent.prototype.sectionsButtons = function() {
//     //     $(thisElement).after(
//     //         '<div id="' + componentId + '-menu-button" ' + thisOptions.buttons.menuButton.style + '></div>' + 
//     //         '<div id="' + componentId + '-top-button" ' + thisOptions.buttons.topButton.style + ' ></div>' + 
//     //         '<div id="' + componentId + '-bubble-backdrop" class="bubble-backdrop"></div>'
//     //     );
//     //     //Bubble Menu
//     //     var menu_button = {
//     //         backdrop: true,
//     //         trigger: {
//     //             type: "menu",
//     //             icon: "fa fa-bars",
//     //             style: "large bg-danger",
//     //             tooltip: {
//     //                 tipText: "Menu",
//     //                 tipPosition: "top",
//     //             },
//     //         },
//     //         position: "bottom-right",
//     //         direction: "horizontal",
//     //         buttons:[
//     //             {
//     //                 button: {
//     //                     style: "small bg-success",
//     //                     icon: "fa fa-save"
//     //                 },
//     //                 tooltip: {
//     //                     tipText: "Save",
//     //                     tipPosition: "top",
//     //                 },                  
//     //                 onClick: function() {
//     //                     //clear leftover (incase) validation classes from fields and jstree
//     //                     $('.form-group').each(function(index, formGroup) {
//     //                         $(formGroup).removeClass('has-error has-feedback');
//     //                         $(formGroup).children('.help-block').remove();
//     //                         if ($(formGroup).children('input').is('.jstreevalidate')) {
//     //                             $('.jstreevalidate').parents().siblings('.border-danger').removeClass('border-danger').addClass('border-default');
//     //                         }
//     //                     });
//     //                     $('#' + componentId).find('.alert').remove();

//     //                     //Validate sections fields and sections datatable
//     //                     // var validated = that.validateForm(false, 'sections', false, null);
                        
//     //                     // If validated, store datat to Object
//     //                     // if (validated) {
//     //                     //  that.sectionsToObj();
//     //                     // }
//     //                     that.sectionsToObj();
//     //                     oldSectionsDataCollection = Object.assign({}, bazDataCollection);
//     //                 }
//     //             },
//     //             {
//     //                 button: {
//     //                     style: "small bg-info",
//     //                     icon: "fa fa-exchange"
//     //                 },
//     //                 tooltip: {
//     //                     tipText: "DIFF",
//     //                     tipPosition: "top",
//     //                 },                  
//     //                 onClick: function() {
//     //                     that.sectionsToObj();                       
//     //                     that.diffData();
//     //                 }
//     //             },              
//     //             {
//     //                 button: {
//     //                     style: "small bg-warning",
//     //                     icon: "fa fa-reply"
//     //                 },
//     //                 tooltip: {
//     //                     tipText: "Cancel",
//     //                     tipPosition: "top",
//     //                 },                  
//     //                 onClick: function() {
//     //                     // window.location.href = thisOptions.buttons.menuButton.cancelURL;
//     //                     that.diffData();
//     //                 }
//     //             },
//     //         ],
//     //         onOpen: function() {
//     //         },
//     //         onClose: function() {
//     //         }
//     //     };
//     //     $('#' + componentId + '-menu-button').bazBubbleMenu(menu_button);

//     //     //Top Button
//     //     var top_button = {
//     //         trigger: {
//     //             type: 'click',
//     //             icon: "fa fa-caret-square-o-up",
//     //             style: "small bg-info disabled",
//     //             tooltip: {
//     //                 tipText: "Top",
//     //                 tipPosition: "right",
//     //             },      
//     //         },
//     //         position: "bottom-left",
//     //         onClick: function() {
//     //             $(document).scrollTop(0);
//     //         }
//     //     };
//     //     $('#' + componentId + '-top-button').bazBubbleMenu(top_button);

//     //     // Show scroll Button
//     //     $(function () {
//     //         $(window).scroll(function() {
//     //             scrollFunction();
//     //         });

//     //         function scrollFunction() {
//     //             if ($(document).scrollTop() > 100) {
//     //                 $('#' + componentId + '-top-button').css("visibility", "visible");
//     //             } else {
//     //                 $('#' + componentId + '-top-button').css("visibility", "hidden");
//     //             }
//     //         }
//     //     });
//     // };

//     //Section with forms box-footer buttons
//     BazContent.prototype.initSectionWithFormButtonsAndAction = function(sectionId, buttons) {
//         if ($.isEmptyObject(buttons)) {
//             //eslint-disable-next-line
//             console.log('sectionWithForm buttons object is empty!');
//         } else {
//             $('#' + sectionId + ' .box-footer').append(
//                 '<div class="row"><div id="' + sectionId + '-action-buttons" class="col-md-12">'
//             );

//             for (var button in buttons) {
//                 if (buttons[button]['action'] === 'post') {
//                     $('#' + sectionId + '-action-buttons').append(
//                         '<button type="button" data-style="zoom-in"' +
//                         'class="btn ladda-button btn-' + buttons[button]['type'] +
//                         ' margin-right-5 pull-right" id="' + sectionId + '-' + buttons[button]['id'] + '">' + 
//                         '<span class="caps ladda-label">' + buttons[button]['title'] + '</span></button>'
//                     );
//                     $('#' + sectionId + '-' + buttons[button]['id']).click(function(e) {
//                         e.preventDefault();
//                         var validated = that.validateForm(false, 'sections', false, null);
//                         // If validated, store data to Object and run ajax
//                         if (validated) {
//                             that.sectionsToObj();
//                             var data = $.param(bazDataCollection[componentId]);
//                             //eslint-disable-next-line
//                             console.log(bazDataCollection);
//                             //eslint-disable-next-line
//                             console.log(data);
//                             that.runAjax(
//                                 this,
//                                 buttons[button]['actionURL'], 
//                                 componentId, 
//                                 'post', 
//                                 'json',
//                                 data,
//                                 {
//                                     type: 'pnotify',
//                                     title: buttons[button]['title'],
//                                     message: buttons[button]['actionSuccessNotifyMessage']
//                                 },
//                                 buttons[button]['actionSuccessRedirectURL'],
//                                 buttons[button]['actionSuccessFunction']
//                             );
//                         }
//                     });
//                 } else {
//                     $('#' + sectionId + '-action-buttons').append(
//                         '<a href="' + rootPath + 'index.php?route=' + buttons[button]['actionURL'] + '"' +
//                         'class="maincontentlink btn btn-' + buttons[button]['type'] +
//                         ' margin-right-5 pull-right" id="' + sectionId + '-' + buttons[button]['id'] + '">' +
//                         '<span class="caps">' + buttons[button]['title'] + '</span></a>' 
//                     );                  
//                 }
//             }
//             $('#' + sectionId + ' .box-footer').append(
//                 '</div></div>'
//             );            
//             if (thisOptions.sectionWithForm.dataId === '') {
//                 $('#' + sectionId + '-update').addClass('hidden');
//                 $('#' + sectionId + '-update').attr('disabled', true)
//             } else {
//                 $('#' + sectionId + '-add').addClass('hidden');
//                 $('#' + sectionId + '-add').attr('disabled', true)
//             }
//         }
//     }

//     // //Section with forms box-footer dev buttons
//     // BazContent.prototype.initSectionWithFormDevModeTools = function(sectionId) {
//     //     $('#' + sectionId + '-action-buttons').attr('hidden', true);
//     //     scanOn.each(function(i,v) {
//     //         $(v).parent('.form-group').removeClass('hidden');
//     //         $(v).prop('disabled', false);
//     //         $(v).data('bazpost', "true");
//     //         if ($(v).parent('.form-group').children('select').length === 1) {
//     //             $(v).parent('.form-group').prepend(
//     //                 '<div class="icheck-danger">' +
//     //                     '<input class="devModeTools noneRadio" type="radio" ' +
//     //                     'name="' + $(v)[0].id + '-radio" id="' + $(v)[0].id + '-dev-none-radio"/>' +
//     //                     '<label for="' + $(v)[0].id + '-dev-none-radio">None</label>' +  
//     //                 '</div>' +
//     //                 '<div class="icheck-info">' +
//     //                     '<input class="devModeTools textRadio" type="radio" ' +
//     //                     'name="' + $(v)[0].id + '-radio" id="' + $(v)[0].id + '-dev-text-radio"/>' +
//     //                     '<label for="' + $(v)[0].id + '-dev-text-radio">Text</label>' +  
//     //                 '</div>' +
//     //                 '<div class="icheck-info">' +
//     //                     '<input class="devModeTools valueRadio" type="radio" ' +
//     //                     'name="' + $(v)[0].id + '-radio" id="' + $(v)[0].id + '-dev-value-radio"/>' +
//     //                     '<label for="' + $(v)[0].id + '-dev-value-radio">Value</label>' +  
//     //                 '</div>'                    
//     //                 );                
//     //         } else {
//     //             $(v).parent('.form-group').prepend(
//     //                 '<div class="icheck-info">' +
//     //                     '<input class="devModeTools" type="checkbox" id="' + $(v)[0].id + '-dev-value-checkbox"/>' +
//     //                     '<label for="' + $(v)[0].id + '-dev-value-checkbox">Value</label>' +  
//     //                 '</div>'
//     //                 );                
//     //         }
//     //     });
//     //     function doAjax(id, method) {
//     //         PNotify.removeAll();
//     //         var route = $('#' + sectionId + '-dev-route').val();
//     //         if (route === '') {
//     //             $('#' + sectionId + '-dev-route').parent('.form-group').addClass('has-error');
//     //             $('#' + sectionId + '-dev .form-group').append(
//     //                 '<span class="help-block">Route cannot be empty</span>'
//     //             );
//     //             $('#' + sectionId + '-dev-route').focus(function() {
//     //                 $('#' + sectionId + '-dev-route').parent('.form-group').removeClass('has-error');
//     //                 $('#' + sectionId + '-dev .help-block').remove();
//     //             });
//     //         } else {
//     //             that.sectionsToObj();
//     //             var devData = { };
//     //             var extractComponentId;
//     //             if ($('#' + componentId + ' .devModeTools').is(':checked')) {
//     //                 $('#' + componentId + ' .devModeTools').each(function(i,v) {
//     //                     extractComponentId = $(v)[0].id.split('-',2);
//     //                     if ($(v).is(':checked')) {
//     //                         if ($(v).is('[type=radio]')) {
//     //                             if (!($(v).is('.noneRadio'))) {
//     //                                 if ($(v).is('.valueRadio')) {
//     //                                     devData[extractComponentId[1]] = 
//     //                                     bazDataCollection[componentId][extractComponentId[1]];
//     //                                 } else if ($(v).is('.textRadio')) {
//     //                                     devData[extractComponentId[1]] = 
//     //                                     $(v).parents('.form-group').children('select')[0].selectedOptions[0].text
//     //                                 }
//     //                             }                                
//     //                         } else {
//     //                             devData[extractComponentId[1]] = 
//     //                             bazDataCollection[componentId][extractComponentId[1]];
//     //                         }
//     //                     }
//     //                 });
//     //                 var data = $.param(devData);
//     //                 that.runAjax(
//     //                     id,
//     //                     route, 
//     //                     componentId, 
//     //                     method, 
//     //                     'json',
//     //                     data,
//     //                     {
//     //                         type: 'dialog'
//     //                     },
//     //                     null
//     //                 );
//     //             } else {
//     //                 PNotify.removeAll();
//     //                 new PNotify({
//     //                     title: 'Checkbox/Radio not selected!',
//     //                     text: 'No Dev Mode Tools Field Checkbox/Radio Selected',
//     //                     type: 'error'
//     //                 });
//     //                 pnotifySound.play();                     
//     //             }
//     //         }
//     //     }
//     //     $('#' + sectionId + ' .box-footer').append(
//     //         '<div class="row"><div class="col-md-12" id="' + sectionId + '-dev">' +
//     //         '<h4 class="caps">Dev Test Tools</h4>' +
//     //         '<div class="form-group ">' +
//     //         '   <label>Route</label> <i data-toggle="tooltip" data-html="true" data-placement="right"' +
//     //         ' title="" class="fa fa-fw fa-question-circle fa-1 helper " data-original-title="' +
//     //         'Enter route parameter"></i> <sup><i data-toggle="tooltip" data-html="true" data-placement="top"' +
//     //         ' title="" style="font-size: 7px;" class="fa fa-fw fa-star fa-1 helper text-danger" ' +
//     //         'data-original-title="Required"></i></sup>' +
//     //         '<input type="text" class="form-control input-sm" id="' +
//     //         sectionId + '-dev-route" name="' + sectionId + '-dev-route" ' +
//     //         'placeholder="ROUTE PARAMETER"></div>'
//     //     );    
//     //     $('#' + sectionId + '-dev').append(
//     //         '<button type="button" class="btn ladda-button btn-orange margin-right-5" ' +
//     //         'id="' + sectionId + '-dev-get-button" data-style="expand-right">' + 
//     //         '<span class="caps ladda-label">Test Get</span></button>' +
//     //         '<button type="button" class="btn ladda-button btn-purple margin-right-5" ' +
//     //         'id="' + sectionId + '-dev-post-button" data-style="expand-right">' + 
//     //         '<span class="caps ladda-label">Test Post</span></button>' +
//     //         '</div></div><br><br>' +
//     //         '<span class="text-danger">NOTE: Running Dev Mode. All fields are enabled and visible.</span><br>' +
//     //         '<span class="text-info">HOW-TO: <br>' +
//     //         '1) Enter route parameter example: account/view OR account/edit.<br>' +
//     //         '2) Select checkboxes that are before the fields to sent that fields data to the above given route.<br>' +
//     //         '3) Click "TEST GET" to test get method and "TEST POST" to test post method</span>'
//     //     );
//     //     $('#' + sectionId + '-dev-get-button').click(function(e) {
//     //         e.preventDefault();
//     //         doAjax(this, 'get');
//     //     });
//     //     $('#' + sectionId + '-dev-post-button').click(function(e) {
//     //         e.preventDefault();
//     //         doAjax(this, 'post');
//     //     });            
//     // }

//     // Run AjaxCalls
//     BazContent.prototype.runAjax = function(buttonId, actionURL, componentId, method, dataType, data, responseAction, redirectURL, onSuccessFunction) {
//         // Convert Object to HTML listing (for dev buttons)
//         function createHtmlList(obj){
//             var output = '';
//             Object.keys(obj).forEach(function(k) {
//                 if (typeof obj[k] == "object" && obj[k] !== null){
//                     output += '(ARRAY) ' + k + '<br>';
//                     output += createHtmlList(obj[k]);
//                     output += '<br>';
//                 } else {
//                     output += '    ' + k + ' => ' + obj[k] + '<br>'; 
//                 }
//             });
//             return output;
//         }
//         // runAjax
//         if (data) {
//             var l = Ladda.create(buttonId);
//             var url = rootPath + 'index.php?route=' + actionURL;
//             l.start();
//             $.ajax({
//                 url: url,
//                 data: data,
//                 method: method,
//                 dataType: dataType,
//                 success: function(response) {
//                     if (responseAction.type === 'pnotify') {
//                         if (response.status === 0) {
//                             PNotify.removeAll();
//                             new PNotify({
//                                 title: responseAction.title,
//                                 text: responseAction.message,
//                                 type: 'success'                                 
//                             });
//                             if (redirectURL) {                               
//                                 window.location = rootPath + 'index.php?route=' + redirectURL;
//                             }
//                             if (onSuccessFunction) {
//                                 onSuccessFunction();
//                             }
//                         } else {
//                             PNotify.removeAll();
//                             new PNotify({
//                                 title: responseAction.title,
//                                 text: response.status_str,
//                                 type: 'error'
//                             });
//                         }
//                         pnotifySound.play();   
//                     } else if (responseAction.type === 'dialog') {
//                         BootstrapDialog.alert({
//                             size: BootstrapDialog.SIZE_WIDE,
//                             title: method + ' Response',
//                             message:  
//                             '<pre>URL: ' + url + '</pre>' +
//                             '<pre>Data Sent: ' + data + '</pre>' +
//                             '<pre>Response: <div style="margin-left: 12px;">' + 
//                             createHtmlList(response) + '</div></pre>',
//                             cssClass: 'modal-full-width'
//                         });                        
//                     }
//                 }
//             }).always(function() {
//                     l.stop();
//                     return false;
//             });
//         } else {
//             PNotify.removeAll();
//             new PNotify({
//                 title: 'No Data!',
//                 text: 'Select checkbox/radio to send data',
//                 type: 'error'
//             });
//         }        
//     }

//     // //Make first tab active
//     BazContent.prototype.activateSectionTabs = function(fieldId, sectionId) {
//         // scanOn = [];
//         if (fieldId === null && sectionId) {
//             // scanOn = $('#' + sectionId).find('[data-bazscan]');
//         } else if (fieldId && sectionId === null) {
//             // scanOn.push('#' + fieldId);
//         }
//         // $(scanOn).each(function(i,v) {
//         //     var optionsId = $(v)[0].id;
//         //     if ($(v).data('bazscan')) {
//         //         if ($(v).data('bazscantype') === 'tabs') {
//         //             var activate = $('#' + optionsId + ' ul li:first a').attr('href');
//         //             $('#' + optionsId + ' ul li:first').addClass('active');
//         //             if (activate && activate.indexOf('#') == 0) {
//         //                 $('#' + activate.substr(1) + "").addClass('active');
//         //             }
//         //         }
//         //     }
//         // });
//     };

//     //Modify JsTree
//     BazContent.prototype.modifyJsTree = function(tree, optionsId, task, elthis, elthat, selectedNode, runFunction) {
//         if (task === 'addNode') {
//             tree.jstree('create_node', 
//                 $('#' + selectedNode[0].id), 
//                 $('#' + optionsId + '-tree-add-input').val(),
//                 'last',
//                 function() {
//                     tree.jstree('open_node', $('#' + selectedNode[0].id));
//                 }
//             );
//             $('#' + optionsId + '-tree-search').removeClass('hidden');
//             $('#' + optionsId + '-tree-add').addClass('hidden');
//             $('#' + optionsId + '-tree-add-input').val('');
//             $(elthis).off();
//             $(elthat).off();
//             runFunction();
//         } else if (task === 'editNode') {
//             tree.jstree('rename_node', 
//                 $('#' + selectedNode[0].id), 
//                 $('#' + optionsId + '-tree-edit-input').val()
//             );
//             $('#' + optionsId + '-tree-search').removeClass('hidden');
//             $('#' + optionsId + '-tree-edit').addClass('hidden');
//             $('#' + optionsId + '-tree-edit-input').val('');
//             $(elthis).off();
//             $(elthat).off();
//             runFunction();
//         }
//     };

//     //Get Jstree Selected Path
//     BazContent.prototype.getJsTreeSelectedNodePath = function(formId, nodeTreeSelector) {
//         var nodePath = null;
//         var nodeDataId = null;
        
//         var nodeTreeSelectorId = $(nodeTreeSelector)[0].id;
//         var treePathSeparator = sectionsOptions[nodeTreeSelectorId].bazJstreeOptions.treePathSeparator || ' <i class="fa fa-chevron-right text-sm"></i> ';
//         // var treeName = sectionsOptions[nodeTreeSelectorId].bazJstreeOptions.treeName || 'node';
//         var selectedNode = $(nodeTreeSelector).jstree('get_selected', true);
//         var grabSelectedNode = [];
//         var nodeToAdd = { };
//         var rootParent, nodeSelfId, nodeSelfName;

//         // Check if node are selected, if yes, then add them to array
//         if ($(selectedNode).length === 0) {
//             $(nodeTreeSelector).parents('.form-group').addClass('has-error has-feedback');
//             $(nodeTreeSelector).removeClass('border-default').addClass('border-danger');
//             $(nodeTreeSelector).after(
//                 '<div id="' + nodeTreeSelectorId + '-error" class="help-block"></div>'
//                 );
//             $('#' + nodeTreeSelectorId + '-error').html(sectionsOptions[formId]['messages'][nodeTreeSelectorId]);
//             $('#' + nodeTreeSelectorId + '-tree-search-input').addClass('border-solid border-danger').removeClass('border-default');
//             $('#' + nodeTreeSelectorId + '-tree-search-input').siblings('.input-group-addon').addClass('border-danger bg-danger').removeClass('bg-default');
//             return false;       
//         } else {
//             $(selectedNode).each(function(index,value) {
//                 grabSelectedNode.push(value);//Grab only single node
//             });
//         }
//         // Get full path of the added node
//         $(grabSelectedNode).each(function(index,value) {
//             nodeSelfName = value.text;
//             nodeSelfId = value.id;
//             rootParent = value.parents[1];
//             nodeDataId = value.data.id;
//             var nodeFullPath = [];
//             if (rootParent === '#') {
//                 nodeToAdd[index] = { };
//                 nodeToAdd[index].path = $.trim(nodeSelfName);
//                 nodeToAdd[index].id = nodeDataId;
//                 nodeToAdd[index].jstreeId = nodeSelfId;
//                 nodeToAdd[index].nodeName = nodeSelfName;
//             } else {
//                 var nodeParentName = uiGetParents(value);
//                 $(nodeParentName).each(function(index, value) {
//                     value = $.trim(value.replace(/\n\t/g, ''));
//                     nodeFullPath.push(value);
//                 });
//                 var popRoot = $.trim(nodeFullPath.pop());
//                 nodeFullPath = nodeFullPath.reverse();
//                 nodeFullPath = nodeFullPath.toString();
//                 nodeFullPath = nodeFullPath.replace(/,/g, 
//                     treePathSeparator) + treePathSeparator +  
//                     nodeSelfName;
//                 nodePath = $.trim(nodeFullPath);
//                 nodeToAdd[index] = { };
//                 if (sectionsOptions[nodeTreeSelectorId].bazJstreeOptions.inclRoot) {
//                     nodeToAdd[index].path = treePathSeparator + popRoot + treePathSeparator + nodePath;
//                 } else {
//                     nodeToAdd[index].path = nodePath;
//                 }
//                 nodeToAdd[index].id = nodeDataId;
//                 nodeToAdd[index].jstreeId = nodeSelfId;
//                 nodeToAdd[index].nodeName = nodeSelfName;
//             }
//         });
//         return nodeToAdd;

//         // Function to grab Parent of Selected Node.
//         function uiGetParents(data) {
//             try {
//                 var lnLevel = data.parents.length;
//                 var lsSelectedID = data.id;
//                 var loParent = $("#" + lsSelectedID);
//                 var lsParents = [];
//                 for (var ln = 0; ln <= lnLevel -1 ; ln++) {
//                     loParent = loParent.parent().parent();
//                     if (loParent.children()[1] != undefined) {
//                         lsParents.push(loParent.children()[1].text);
//                     }
//                 }
//                 return lsParents;
//             }
//             catch (err) {
//                 that.error('Error in uiGetParents');
//             }
//         }
//     };

//     //Fields to Datatable
//     BazContent.prototype.fieldsToDatatable = function(fieldsetDatatable) {
//         fieldsetSectionId = $('#' + fieldsetDatatable).parents('section')[0].id;
//         var addSeq = [];
//         var columnDefsObj = [{ 'targets': 0, 'visible': false }, 
//                                 { 'orderable': true, 'className': 'reorder', 'targets': 0 }, 
//                                 { 'orderable': false, 'targets': '_all' }];
//         if (sectionsOptions[fieldsetDatatable]) {
//             $.each(sectionsOptions[fieldsetDatatable].dataTables, function(i,v) {
//                 if (sectionsOptions[v].datatable.rowReorder) {
//                     if (!sectionsOptions[v].datatable.columnDefs) {
//                         sectionsOptions[v].datatable.columnDefs = [];
//                         sectionsOptions[v].datatable.columnDefs = columnDefsObj;
//                     } else {
//                         sectionsOptions[v].datatable.columnDefs = $.merge(columnDefsObj, sectionsOptions[v].datatable.columnDefs);
//                     }
//                     addSeq.push('true');
//                 }
//             });         
//         } else {
//             that.error('Datatable Parameters missing for datatable - ' + fieldsetDatatable);
//         }
//         if ($.inArray('true', addSeq) !== -1) {
//             $('#' + fieldsetDatatable + '-fields').prepend(
//                 '<div class="row margin-top-10 hidden">' +
//                 '   <div class="col-md-12">' +
//                 '       <label>SEQ</label>' +
//                 '       <div data-bazScan="true" data-bazScanType="seq" id="' + fieldsetDatatable + '-seq"></div>' +
//                 '   </div>' +
//                 '</div>' +
//                 '<div class="row margin-top-10 hidden">' +
//                 '   <div class="col-md-12">' +
//                 '       <label>SORT</label>' +
//                 '       <div data-bazScan="true" data-bazScanType="html" id="' + fieldsetDatatable + '-sort"><i class="fa fa-sort"></i></div>' +
//                 '   </div>' +
//                 '</div>'
//             );
//         }

//         $(sectionsOptions[fieldsetDatatable].dataTables).each(function(i,v) {
//             // Generate table th
//             var extractDatatableFieldsLabel = that.extractDatatableFieldsLabel(fieldsetDatatable, v);
//             var labels = [];
//             for (var label in extractDatatableFieldsLabel) {
//                 labels.push('<th>' + extractDatatableFieldsLabel[label].labelName + '</th>');
//             }
//             labels = labels.join('');
//             $('#' + v).append( 
//                 '<div class="margin-bottom-10" id="' + v + '-table-div">' +
//                 '<label>' + sectionsOptions[v].tableTitle + '</label> '+ 
//                 ' <i data-toggle="tooltip" data-placement="right" title="' + sectionsOptions[v].tableTitle + ' table" class="fa fa-question-circle fa-1 helper"></i>' +
//                 '<table id="' + v + '-table-data" class="table table-striped dt-responsive nowrap border-solid border-default" style="margin:0 !important;"' +
//                 ' width="100%" cellspacing="0"><thead>' + 
//                 labels + '</thead><tbody></tbody></table></div>'
//                 );
//             //Init Datatable
//             tableData[fieldsetSectionId][v] = { };
//             tableData[fieldsetSectionId][v] = $('#' + v + '-table-data').DataTable(sectionsOptions[v].datatable);
//             if (sectionsOptions[v].datatable.rowReorder) {
//                 // If rowReorder enabled
//                 tableData[fieldsetSectionId][v].on('row-reorder', function() {
//                     that.rowReorderRedoSeq(tableData[fieldsetSectionId][v], v);
//                     // that.rowReorderDatatableDataToObject(details, fieldsetSectionId, fieldsetDatatable, v);
//                     tableData[fieldsetSectionId][v].draw();
//                 });
//             }
//         });

//         //Assign button click
//         $('#' + fieldsetDatatable + '-assign-button').click(function() {
//             var datatable;
//             if ($(sectionsOptions[fieldsetDatatable].dataTables).length > 1) {
//                 datatable = $('#' + sectionsOptions[fieldsetDatatable].dataTableSelector.id)[0].value;
//             } else {
//                 datatable = sectionsOptions[fieldsetDatatable].dataTables[0];
//             }
//             $('#' + fieldsetDatatable + '-fields').siblings().find('.has-error').removeClass('has-error has-feedback');//remove previous validation
//             $('#' + fieldsetDatatable + '-fields').siblings().find('table').removeClass('border-danger').addClass('border-default');//remove previous validation
//             $('#' + fieldsetDatatable + '-fields').siblings().find('.help-block').remove();//remove previous validation
//             //Execute preExtraction script passed from the html(js script)
//             if (sectionsOptions[datatable].preExtraction) {
//                 sectionsOptions[datatable].preExtraction(tableData[fieldsetSectionId][datatable]);
//             }
//             extractDatatableFieldsData = that.extractDatatableFieldsData(fieldsetDatatable, datatable, false);
//             //Execute postExtraction script passed from the html(js script)
//             if (sectionsOptions[datatable].postExtraction) {
//                 sectionsOptions[datatable].postExtraction(tableData[fieldsetSectionId][datatable], extractDatatableFieldsData);
//             }           
//             var validated = that.validateForm(false, 'datatable', false, fieldsetDatatable + '-form');
//             if (validated) {
//                 var tryAdd = that.addExtractFieldsToDatatable(null, extractDatatableFieldsData, fieldsetDatatable, datatable, false);
//                 if (tryAdd) {
//                     $('#' + fieldsetDatatable).find('.jstreevalidate').val('');
//                     that.validateForm(false, 'sections', true, null);
//                     tableData[fieldsetSectionId][datatable].responsive.recalc();
//                     that.registerDatatableButtons(tableData[fieldsetSectionId][datatable], $('#' + datatable + '-table-div'), datatable, fieldsetSectionId, fieldsetDatatable);

//                     var table = tableData[fieldsetSectionId][datatable];
//                     table.on('responsive-display', function (showHide) {
//                         if (showHide) {
//                             that.registerDatatableButtons(tableData[fieldsetSectionId][datatable], $('#' + datatable + '-table-div'), datatable, fieldsetSectionId, fieldsetDatatable);
//                         }
//                     });
//                     //Execute postSuccess script passed from the html(js script)
//                     if (sectionsOptions[datatable].postSuccess) {
//                         sectionsOptions[datatable].postSuccess(tableData[fieldsetSectionId][datatable], extractDatatableFieldsData);
//                     }                   
//                     that.clearDatatableFormData(datatable, fieldsetDatatable);
//                 }               
//             }
//         });
//     };

//     //Extract Fields Datatable data
//     BazContent.prototype.extractDatatableFieldsLabel = function(fieldsetDatatable, datatable) {
//         fieldsetSectionId = $('#' + fieldsetDatatable).parents('section')[0].id;
//         var extractedLabel = null;
//         extractedLabel = { };
//         var counter = 0;
//         dataTableFields[componentId][fieldsetSectionId][datatable] = [];
//         $('#' + fieldsetDatatable).find('[data-bazscan]').each(function(i,v) {
//             if ($(v).data('bazscan')) {
//                 if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
//                     extractedLabel[counter] = { };
//                     // extractedLabel[counter].labelName = label;
//                     if ($(v).parents().is('.input-group') || $(v).parents().is('.input-group-sm') || $(v).parents().is('.input-group-lg')) {
//                         extractedLabel[counter].labelName = $(v).parent().siblings('label').text();
//                     } else if ($(v).data('bazscantype') === 'jstree') {//jstree
//                         extractedLabel[counter].labelName = $(v).parents('.form-group').children('label').text();
//                     } else if ($(v).data('bazscantype') === 'radio') {// icheck-radio
//                         extractedLabel[counter].labelName = $(v).children('label').text();
//                     } else if ($(v).data('bazscantype') === 'checkbox') {// icheck-checkbox
//                         if ($(v).siblings('label').text() === '') {
//                             extractedLabel[counter].labelName = $(v).parents('.form-group').children('label').text();
//                         } else {
//                             extractedLabel[counter].labelName = $(v).siblings('label').text();
//                         }
//                     } else {
//                         extractedLabel[counter].labelName = $(v).siblings('label').text();
//                     }
//                     dataTableFields[componentId][fieldsetSectionId][datatable].push($(v)[0].id);
//                 }
//             }
//             counter++;
//         });
//         //Add buttons
//         if (sectionsOptions[datatable].bazdatatable && sectionsOptions[datatable].bazdatatable.rowButtons) {
//             extractedLabel[counter] = { };
//             extractedLabel[counter].labelName = 'ACTIONS';
//             dataTableFields[componentId][fieldsetSectionId][datatable].push(fieldsetDatatable + '-actions');
//         }
//         return extractedLabel;
//     };

//     //Extract Fields Datatable data
//     BazContent.prototype.extractDatatableFieldsData = function(fieldsetDatatable, datatable, isEdit) {
//         fieldsetSectionId = $('#' + fieldsetDatatable).parents('section')[0].id;
//         var extractedFieldsData = null;
//         var extractedJstreeData = null;
//         var finalExtractedData = null;
//         extractedFieldsData = { };
//         extractedJstreeData = { };
//         finalExtractedData = { };
//         var counter = 0;
//         $('#' + fieldsetDatatable).find('[data-bazscan]').each(function(i,v) {
//             if ($(v).data('bazscan')) {
//                 if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
//                     extractedFieldsData[counter] = { };
//                     extractedFieldsData[counter].id = v.id;
//                     // extractedFieldsData[counter].data = $('#' + v.id); //Enable if you need all data

//                     if (v.tagName === 'INPUT' && v.type === 'checkbox') {
//                         if ($(v)[0].checked === true) {
//                             extractedFieldsData[counter].extractedData = 'YES';
//                         } else {
//                             extractedFieldsData[counter].extractedData = 'NO';
//                         }
//                     } else if (v.tagName === 'INPUT' || v.tagName === "TEXTAREA") {
//                         if ($(v)[0].value === 'undefined') {//kill if incorrect Data
//                             that.error('data is undefined!');
//                             return;
//                         } else {
//                             extractedFieldsData[counter].extractedData = $(v)[0].value;
//                         }
//                     }
//                     if ($(v).data('bazscantype') === 'select2') {
//                         extractedFieldsData[counter].extractedData = null;
//                         $($(v)[0].selectedOptions).each(function(i,v){
//                             if (!extractedFieldsData[counter].extractedData) {
//                                 extractedFieldsData[counter].extractedData = '<span id="' + $(v)[0].value + '">' + $(v)[0].text + '</span><br>';
//                             } else {
//                                 extractedFieldsData[counter].extractedData = extractedFieldsData[counter].extractedData + '<span id="' + $(v)[0].value + '">' + $(v)[0].text + '</span><br>';
//                             }
//                         });
//                     }
//                     if ($(v).data('bazscantype') === 'jstree') {//jstree
//                         var treeData = that.getJsTreeSelectedNodePath(fieldsetDatatable + '-form', $(v));
//                         extractedJstreeData[counter] = { };
//                         for (i = 0; i < Object.keys(treeData).length; i++) {
//                             extractedJstreeData[counter][i] = { };
//                             extractedJstreeData[counter][i].id = v.id;
//                             extractedJstreeData[counter][i].extractedData = '<span id="' + treeData[i].id + '" data-jstreeId="' + treeData[i].jstreeId + '">' + treeData[i].path + '</span><br>';
//                             extractedJstreeData[counter][i].absolutePath = treeData[i].path;
//                             extractedJstreeData[counter][i].nodeName = treeData[i].nodeName;
//                         }
//                     }
//                     if ($(v).data('bazscantype') === 'radio') {// icheck-radio
//                         extractedFieldsData[counter].extractedData = $(v).find('input:checked').siblings('label').text();
//                     }
//                     if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
//                         extractedFieldsData[counter].extractedData = $(v).trumbowyg('html')
//                     }
//                     if ($(v).data('bazscantype') === 'html') {//HTML (as-is data)
//                         extractedFieldsData[counter].extractedData = $(v).html();
//                     }
//                     if ($(v).data('bazscantype') === 'seq') {//sequence
//                         extractedFieldsData[counter].extractedData = $(v).html();
//                     }
//                 }
//                 if ($(v).data('bazscantype') === 'tableSelector') {
//                     selectedTable = $(v).val();
//                     multiTable = true;
//                 }
//             }
//             counter++;
//         });
//         var rowId = 0;
//         if (isEdit && multiTable) {
//             datatable = selectedTable;
//         }
//         if (datatable) {
//             if (tableData[fieldsetSectionId][datatable].row().count() >= 0) {
//                 rowId = tableData[fieldsetSectionId][datatable].row().count() + 1;
//             }
//             if (Object.keys(extractedJstreeData).length > 0) {
//                 for (var jstreesData in extractedJstreeData) {
//                     for (var jstreeData in extractedJstreeData[jstreesData]) {  
//                         finalExtractedData[jstreeData] = { };
//                         for (var fieldsData in extractedFieldsData) {
//                             finalExtractedData[jstreeData][fieldsData] = extractedFieldsData[fieldsData];
//                             if (fieldsData === jstreesData) {
//                                 finalExtractedData[jstreeData][fieldsData] = extractedJstreeData[jstreesData][jstreeData];
//                             }
//                             if (sectionsOptions[datatable].bazdatatable.rowButtons) {
//                                 //Add Action Buttons
//                                 finalExtractedData[jstreeData][counter] = { };
//                                 finalExtractedData[jstreeData][counter].extractedData = rowId;
//                                 if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && !sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
//                                     finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
//                                     finalExtractedData[jstreeData][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger pull-right margin-left-5 tableDeleteButton"><i class="fa fa-trash-o"></i></button>';
//                                 } else if (!sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
//                                     finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
//                                     finalExtractedData[jstreeData][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary pull-right tableEditButton"><i class="fa fa-pencil"></i></button>';
//                                 } else if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
//                                     finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
//                                     finalExtractedData[jstreeData][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger pull-right margin-left-5 tableDeleteButton"><i class="fa fa-trash-o"></i></button>' +
//                                                                             '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary pull-right tableEditButton"><i class="fa fa-pencil"></i></button>';                        
//                                 }
//                             }
//                         }
//                         rowId++;
//                     }
//                 }
//             } else {//No JS Tree data extraction
//                 finalExtractedData[0] = { };
//                 for (var noJstree in extractedFieldsData) {
//                     finalExtractedData[0][noJstree] = extractedFieldsData[noJstree];
//                     if (sectionsOptions[datatable].bazdatatable && sectionsOptions[datatable].bazdatatable.rowButtons) {
//                         //Add Action Buttons
//                         finalExtractedData[0][counter] = { };
//                         finalExtractedData[0][counter].extractedData = rowId;
//                         if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && !sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
//                             finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';
//                             finalExtractedData[0][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger pull-right margin-left-5 tableDeleteButton"><i class="fa fa-trash-o"></i></button>';
//                         } else if (!sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
//                             finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';                     
//                             finalExtractedData[0][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary pull-right tableEditButton"><i class="fa fa-pencil"></i></button>';
//                         } else if (sectionsOptions[datatable].bazdatatable.rowButtons.canDelete && sectionsOptions[datatable].bazdatatable.rowButtons.canEdit) {
//                             finalExtractedData[0][counter].id = fieldsetDatatable + '-actions';                     
//                             finalExtractedData[0][counter].extractedData = '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-danger pull-right margin-left-5 tableDeleteButton"><i class="fa fa-trash-o"></i></button>' +
//                                                                     '<button data-row-id="' + rowId + '" type="button" class="btn btn-xs btn-primary pull-right tableEditButton"><i class="fa fa-pencil"></i></button>';                        
//                         }
//                     }
//                 }
//                 rowId++;            
//             }
//             return finalExtractedData;
//         } else {
//             return false;
//         }
//     };

//     //Add extracted fields data to datatable
//     BazContent.prototype.addExtractFieldsToDatatable = function(rowIndex, extractDatatableFieldsData, fieldsetDatatable, datatable, isEdit) {
//         fieldsetSectionId = $('#' + fieldsetDatatable).parents('section')[0].id;
//         var migrateData = false;
//         var oldDataTable;
//         // Need to convert to array to add to datatable to merge them later to object and add values to datatable
//         var rowExtractedId = [];
//         var rowExtractedData = [];
//         var found = false;

//         if (isEdit && multiTable) {
//             if (datatable !== selectedTable ){
//                 migrateData = true;
//             }
//             oldDataTable = datatable;
//             datatable = selectedTable;
//         }
//         if (!isEdit && sectionsOptions[datatable].datatable.rowReorder) {
//             var seq = tableData[fieldsetSectionId][datatable].rows().count();
//             if (seq === 0) {
//                 seq = 1;
//             } else {
//                 seq = seq + 1;
//             }
//             for (var dataRows in extractDatatableFieldsData) {
//                 var oldId = extractDatatableFieldsData[dataRows][0].id;
//                 extractDatatableFieldsData[dataRows][0] = { };
//                 extractDatatableFieldsData[dataRows][0].id = oldId;
//                 extractDatatableFieldsData[dataRows][0].extractedData = seq;
//                 seq++;
//             }
//         }
//         for (var rows in extractDatatableFieldsData) {
//             rowExtractedData[rows] = [];
//             rowExtractedId[rows] = [];
//             for (var row in extractDatatableFieldsData[rows]) {
//                 rowExtractedData[rows].push(extractDatatableFieldsData[rows][row].extractedData);//to datatable
//                 rowExtractedId[rows].push(extractDatatableFieldsData[rows][row].id);
//             }
//         }
//         if (sectionsOptions[datatable].bazdatatable && sectionsOptions[datatable].bazdatatable.compareData) {
//             if (sectionsOptions[datatable].bazdatatable.compareData.inclIds || sectionsOptions[datatable].bazdatatable.compareData.exclIds) {
//                 if (tableData[fieldsetSectionId][datatable].rows().count() > 0) {
//                     var currentTable = $('#' + datatable).children().find('tbody tr');
//                     currentTable.each(function() {
//                         $(this).removeClass('animated fadeIn bg-warning disabled');
//                     });
//                     var currentTableData = tableData[fieldsetSectionId][datatable].rows().data();
//                     found = that.compareData(sectionsOptions[datatable].bazdatatable.compareData.inclIds, sectionsOptions[datatable].bazdatatable.compareData.exclIds, extractDatatableFieldsData, currentTableData, rowIndex, datatable);
//                 }
//             }
//         }
//         if (found) {
//             PNotify.removeAll();
//             new PNotify({
//                 title: 'Input data already exists in table!',
//                 text: 'Instead of adding, please edit the data',
//                 type: 'warning',
//                 cornerclass: 'ui-pnotify-sharp',
//             });
//             pnotifySound.play();                
//             return false;
//         } else {
//             if (rowIndex !== null) {//rowIndex is from editDatatableRow
//                 if (!migrateData) {
//                     $(rowExtractedData).each(function(i,v) {
//                         tableData[fieldsetSectionId][datatable].row(rowIndex).data(v).draw();
//                     });             
//                 } else {
//                     tableData[oldDataTable].row(rowIndex).remove().draw();
//                     $(rowExtractedData).each(function(i,v) {
//                         tableData[fieldsetSectionId][datatable].row.add(v).draw();
//                     });
//                     // that.deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, fieldsetSectionId, oldDataTable);
//                     that.registerDatatableButtons(tableData[oldDataTable], $('#' + datatable + '-table-div'), datatable, fieldsetSectionId, fieldsetDatatable);
//                 }
//                 that.registerDatatableButtons(tableData[fieldsetSectionId][datatable], $('#' + datatable + '-table-div'), datatable, fieldsetSectionId, fieldsetDatatable);
//                 rowIndex = null;
//             } else {//add new row
//                 $(rowExtractedData).each(function(i,v) {
//                     tableData[fieldsetSectionId][datatable].row.add(v).draw();
//                 });
//                 that.registerDatatableButtons(tableData[fieldsetSectionId][datatable], $('#' + datatable + '-table-div'), datatable, fieldsetSectionId, fieldsetDatatable);
//             }
//             //Add data to object
//             //that.addEditDatatableDataToObject(rowIndex, rowExtractedId, rowExtractedData, fieldsetDatatable, $('#' + fieldsetDatatable).parents('section')[0].id, datatable);
//             return true;
//         }
//     };

//     //Edit table Row
//     BazContent.prototype.editDatatableRow = function(fieldsetDatatable, rowIndex, rowData, datatable) {
//         fieldsetSectionId = $('#' + fieldsetDatatable).parents('section')[0].id;
//         var fieldsetFields = [];
//         if ($(sectionsOptions[fieldsetDatatable].dataTables).length > 1) {
//             $('#' + fieldsetDatatable).find('[data-bazscan]').each(function(i,v) {
//                 if (!($(v).data('bazscantype') === 'tableSelector' || $(v).data('bazscantype') === 'dropzone')) {
//                     fieldsetFields.push($(v));
//                 }
//             });
//             $('#' + fieldsetDatatable).find('[data-bazscan]').each(function(i,v) {// Selector is always in the end.
//                 if (($(v).data('bazscantype') === 'tableSelector')) {
//                     fieldsetFields.push($(v));
//                 }
//             });         
//         } else {
//             fieldsetFields = $('#' + fieldsetDatatable).find('[data-bazscan]');
//         }
//         $(fieldsetFields).each(function(i,v) {
//             if ($(v).data('bazscan')) {
//                 if ($(v).data('bazscantype') === 'seq') {
//                     $(v).html(rowData[i]);
//                 } else if ($(v).data('bazscantype') !== 'html') {
//                     if (v.tagName === 'INPUT' && v.type === 'checkbox') {
//                         if (rowData[i] === 'YES') {
//                             $(v).prop('checked', true);
//                         } else if (rowData[i] === 'NO') {
//                             $(v).prop('checked', false);
//                         }
//                     } else if (v.tagName === 'INPUT' || v.tagName === 'TEXTAREA') {
//                         $(v).val(rowData[i]);
//                     }
//                     if (v.tagName === "SELECT" && $(v).data('bazscantype') !== 'tableSelector') {//Select2
//                         if (rowData[i]) {
//                             var selectarr = rowData[i].split('<br>');
//                             var selectArr = [];
//                             $(selectarr).each(function(i,v) {
//                                 if (v !== "") {
//                                     var extractIds = v.match(/(["'])(?:(?=(\\?))\2.)*?\1/g); //match double or single quotes
//                                     selectArr.push(extractIds[0].replace(/"/g, ''));
//                                 }
//                             });
//                             $(v).val(selectArr);
//                             $(v).trigger('change');
//                         }
//                     }
//                     if (v.tagName === "SELECT" && $(v).data('bazscantype') === 'tableSelector') {
//                         $(v).val(datatable);
//                         $(v).trigger('change');
//                     }
//                     if (v.tagName === 'DIV') {
//                         if ($(v).data('bazscantype') === 'jstree') {//jstree
//                             if (rowData[i]) {
//                                 var jstreearr = rowData[i].split('<br>');
//                                 var jstreeArr = [];
//                                 $(jstreearr).each(function(i,v) {
//                                     if (v !== "") {
//                                         var extractJstreeId = v.match(/data-jstreeId=".*"/g);
//                                         var extractIds = extractJstreeId[0].match(/(["'])(?:(?=(\\?))\2.)*?\1/g); //match double or single quotes
//                                         jstreeArr.push(extractIds[0].replace(/"/g, ''));
//                                     }
//                                 });
//                                 $(v).jstree('select_node', jstreeArr);
//                             }
//                         }
//                         if ($(v).data('bazscantype') === 'radio') {//radio
//                             $(v).find('input').each(function() {
//                                 if (rowData[i] === $(this).siblings('label').text()) {
//                                     $(this).prop('checked', true);
//                                 } else {
//                                     $(this).prop('checked', false);
//                                 }
//                             });
//                         }
//                         if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
//                             $(v).trumbowyg('html', rowData[i]);
//                         }                       
//                     }
//                 }
//             }
//         });
//         // Enable cancel/update button.
//         $('#' + fieldsetDatatable + '-cancel-button').removeClass('hidden');
//         $('#' + fieldsetDatatable + '-update-button').removeClass('hidden');
//         $('#' + fieldsetDatatable + '-assign-button').addClass('hidden');
//         // Then we extract data again, Compare again, Update data
//         $('#' + fieldsetDatatable + '-update-button').off();
//         $('#' + fieldsetDatatable + '-update-button').click(function() {
//             var validated = that.validateForm(false, 'datatable', false, fieldsetDatatable + '-form');
//             if (validated) {
//                 extractDatatableFieldsData = that.extractDatatableFieldsData(fieldsetDatatable, datatable, true);
//                 var tryAdd = that.addExtractFieldsToDatatable(rowIndex, extractDatatableFieldsData, fieldsetDatatable, datatable, true);
//                 if (tryAdd) {
//                     //Execute postSuccess script passed from the html(js script)
//                     if (sectionsOptions[datatable].postSuccess) {
//                         sectionsOptions[datatable].postSuccess(tableData[fieldsetSectionId][datatable], extractDatatableFieldsData);
//                     }                   
//                 }
//                 that.clearDatatableFormData(datatable, fieldsetDatatable);
//             }
//             // Hide cancel/update button.
//             $('#' + fieldsetDatatable + '-cancel-button').addClass('hidden');
//             $('#' + fieldsetDatatable + '-update-button').addClass('hidden');
//             $('#' + fieldsetDatatable + '-assign-button').removeClass('hidden');
//         });
//         $('#' + fieldsetDatatable + '-cancel-button').off();
//         $('#' + fieldsetDatatable + '-cancel-button').click(function() {
//             // Hide cancel/update button.
//             $('#' + fieldsetDatatable + '-cancel-button').addClass('hidden');
//             $('#' + fieldsetDatatable + '-update-button').addClass('hidden');
//             $('#' + fieldsetDatatable + '-assign-button').removeClass('hidden');
//             that.clearDatatableFormData(datatable, fieldsetDatatable);
//         });
//     };

//     //Compare extracted fields data with data already in table
//     BazContent.prototype.compareData = function(inclIds, exclIds, inputData, currentTableData, rowIndexToExcl, datatable) {
//         var foundRow;
//         var excludeActions = false;
//         var excludeSeqAndSort = false;
//         if ((sectionsOptions[datatable].bazdatatable.rowButtons.canDelete === true) || (sectionsOptions[datatable].bazdatatable.rowButtons.canEdit === true)) {
//             excludeActions = true;
//         }
//         if (sectionsOptions[datatable].datatable.rowReorder === true) {
//             excludeSeqAndSort = true;
//         }
//         if (inclIds && exclIds) {
//             for (var a = 0; a < currentTableData.length; a++) {
//                 if (a !== rowIndexToExcl) {
//                     for (var aInputData in inputData) {
//                         if (compareAll(inputData[aInputData], currentTableData[a])) {
//                             foundRow = $('#' + datatable).find('tbody tr')[a];
//                             $(foundRow).addClass('animated fadeIn bg-warning disabled');                        
//                             return true;
//                         }
//                     }
//                 }
//             }
//             return false;
//         } else if (inclIds && Object.keys(inclIds).length > 0) {
//             for (var b = 0; b < currentTableData.length; b++) {
//                 if (b !== rowIndexToExcl) {
//                     for (var bInputData in inputData) {
//                         if (compareOnlyInclIds(inclIds, inputData[bInputData], currentTableData[b])) {
//                             foundRow = $('#' + datatable).find('tbody tr')[b];
//                             $(foundRow).addClass('animated fadeIn bg-warning disabled');                        
//                             return true;
//                         }
//                     }
//                 }
//             }
//             return false;
//         } else if (exclIds && Object.keys(exclIds).length > 0) {
//             for (var c = 0; c < currentTableData.length; c++) {
//                 if (c !== rowIndexToExcl) {
//                     for (var cInputData in inputData) {
//                         if (compareAllMinusExclIds(exclIds, inputData[cInputData], currentTableData[c])) {
//                             foundRow = $('#' + datatable).find('tbody tr')[c];
//                             $(foundRow).addClass('animated fadeIn bg-warning disabled');                        
//                             return true;
//                         }
//                     }
//                 }
//             }
//             return false;
//         } else {
//             return false;
//         }

//         function compareAll(inputData, currentTableData) {
//             var currentTableDataLength = 0;
//             var startAt = 0;
//             if (excludeSeqAndSort && excludeActions) {
//                 currentTableDataLength = currentTableData.length - 3;
//                 startAt = 2;
//             } else if (!excludeSeqAndSort && excludeActions) {
//                 currentTableDataLength = currentTableData.length - 1;
//             } else if (excludeSeqAndSort && !excludeActions) {
//                 currentTableDataLength = currentTableData.length - 2;
//                 startAt = 2;
//             }
//             for (var i = 0; i < currentTableDataLength; i++) {
//                 if (currentTableData[startAt] === inputData[startAt].extractedData) {
//                     return true;
//                 }
//                 startAt++;
//             }
//         }

//         function compareOnlyInclIds(inclIds, inputData, currentTableData) {
//             var inclIdsArray = [];
//             var uniqueData = { };
//             var uniqueDataFound = [];
//             var found = [];
//             var currentTableDataLength = 0;
//             var startAt = 0;
//             if (excludeSeqAndSort && excludeActions) {
//                 currentTableDataLength = currentTableData.length - 3;
//                 startAt = 2;
//             } else if (!excludeSeqAndSort && excludeActions) {
//                 currentTableDataLength = currentTableData.length - 1;
//             } else if (excludeSeqAndSort && !excludeActions) {
//                 currentTableDataLength = currentTableData.length - 2;
//                 startAt = 2;
//             }
//             for (var inclId in inclIds) {
//                 for (var sweepInclIds in inputData) {
//                     if (inclId === inputData[sweepInclIds].id) {
//                         if (inclIds[inclId].length >= 0) {
//                             uniqueData[sweepInclIds] = [];
//                             for (var uniqueInclId in inclIds[inclId]) {
//                                 uniqueData[sweepInclIds].push(inclIds[inclId][uniqueInclId]);
//                             }
//                         }
//                         inclIdsArray.push(Number(sweepInclIds));
//                         break;
//                     }
//                 }
//             }
//             for (var i = 0; i < currentTableDataLength; i++) {
//                 var foundInclId = null;
//                 foundInclId = $.inArray(startAt,inclIdsArray);
//                 if (foundInclId !== -1) {
//                     if (currentTableData[startAt] === inputData[startAt].extractedData) {
//                         if (uniqueData[startAt].length > 0 ) {
//                             for (var j = 0; j < uniqueData[startAt].length; j++) {
//                                 if (uniqueData[startAt][j] === inputData[startAt].extractedData) {
//                                     uniqueDataFound.push('true');
//                                 }
//                             }
//                         } else {
//                             found.push('true');
//                         }
//                     }
//                 }
//                 startAt++;
//             }
//             if (found.length === Object.keys(inclIds).length) {
//                 return true;
//             } else if (uniqueDataFound.length > 0) {
//                 return true;
//             } else {
//                 return false;
//             }
//         }

//         function compareAllMinusExclIds(exclIds, inputData, currentTableData) {
//             var exclIdsArray = [];
//             var currentTableDataLength = 0;
//             var startAt = 0;
//             if (excludeSeqAndSort && excludeActions) {
//                 currentTableDataLength = currentTableData.length - 3;
//                 startAt = 2;
//             } else if (!excludeSeqAndSort && excludeActions) {
//                 currentTableDataLength = currentTableData.length - 1;
//             } else if (excludeSeqAndSort && !excludeActions) {
//                 currentTableDataLength = currentTableData.length - 2;
//                 startAt = 2;
//             }
//             for (var exclId in exclIds) {
//                 for (var sweepExclIds in inputData) {
//                     if (exclId === inputData[sweepExclIds].id) {
//                         exclIdsArray.push(Number(sweepExclIds));
//                         break;
//                     }
//                 }
//             }
//             for (var i = 0; i < currentTableDataLength; i++) {
//                 var foundExclId = null;
//                 foundExclId = $.inArray(startAt,exclIdsArray);
//                 if (foundExclId === -1) {
//                     if (currentTableData[startAt] === inputData[startAt].extractedData) {
//                         return true;
//                     } else {
//                         return false;                           
//                     }
//                 }
//                 startAt++;
//             }
//         }
//     };

//     BazContent.prototype.rowReorderRedoSeq = function(table, datatable) {
//         var redoSeq = 1;
//         $('#' + datatable).find('td.reorder').each(function() {
//             $(this).html(redoSeq);
//             redoSeq++;
//         });
//     };

//     //Register table row edit and delete buttons
//     BazContent.prototype.registerDatatableButtons = function(table, el, datatable, fieldsetSectionId, fieldsetDatatable) {
//         var rowIndex, rowData;
//         $(el).find('table').each(function() {
//             $(this).find('.tableDeleteButton').each(function() {
//                 $(this).off();
//                 $(this).click(function() {
//                     if ($(this).closest('tr').hasClass('child')) {
//                         rowIndex = table.row($(this).closest('tr').prev('tr')).index();
//                         table.row($(this).closest('tr').prev('tr')).remove().draw();
//                         that.rowReorderRedoSeq(table, datatable);
//                         // that.deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, fieldsetSectionId, datatable);
//                         that.clearDatatableFormData(datatable, fieldsetDatatable);
//                     } else {
//                         rowIndex = table.row($(this).closest('tr')).index();
//                         table.row($(this).parents('tr')).remove().draw();
//                         that.rowReorderRedoSeq(table, datatable);
//                         // that.deleteDatatableDataFromObject(rowIndex, fieldsetDatatable, fieldsetSectionId, datatable);
//                         that.clearDatatableFormData(datatable, fieldsetDatatable);
//                     }
//                     that.registerDatatableButtons(table, el, datatable, fieldsetSectionId, fieldsetDatatable);
//                     //Execute postSuccess script passed from the html(js script)
//                     if (sectionsOptions[datatable].postSuccess) {
//                         sectionsOptions[datatable].postSuccess(tableData[fieldsetSectionId][datatable]);
//                     }
//                     // Hide cancel/update button.
//                     $('#' + fieldsetDatatable + '-cancel-button').addClass('hidden');
//                     $('#' + fieldsetDatatable + '-update-button').addClass('hidden');
//                     $('#' + fieldsetDatatable + '-assign-button').removeClass('hidden');                    
//                 });
//             });
//             $(this).find('.tableEditButton').each(function() {
//                 $(this).off();
//                 $(this).click(function() {
//                     if ($(this).closest('tr').hasClass('child')) {
//                         rowIndex = table.row($(this).closest('tr').prev('tr')).index();
//                         rowData = table.row($(this).closest('tr').prev('tr')).data();
//                     } else {
//                         rowIndex = table.row($(this).closest('tr')).index();
//                         rowData = table.row($(this).closest('tr')).data();
//                     }
//                     // var popActions = rowData.pop();//get rid of actions
//                     that.clearDatatableFormData(datatable, fieldsetDatatable);
//                     that.editDatatableRow(fieldsetDatatable, rowIndex, rowData, datatable);
//                     //Execute onEdit script passed from the html(js script)
//                     if (sectionsOptions[datatable].onEdit) {
//                         sectionsOptions[datatable].onEdit(tableData[fieldsetSectionId][datatable]);
//                     }
//                 });
//             });
//         });
//     };

//     //Clear form data on success insertion
//     BazContent.prototype.clearDatatableFormData = function(datatable, fieldsetDatatable) {
//         var fieldsToClear;
//         if (sectionsOptions[datatable].bazdatatable.keepFieldsData) {
//             var fieldsToKeep = sectionsOptions[datatable].bazdatatable.keepFieldsData;
//         }
//         var allFields = [];
//         $('#' + fieldsetDatatable).find('[data-bazscan]').each(function(i,v) {
//             allFields.push($(v)[0].id);
//         });
//         if (fieldsToKeep && fieldsToKeep.length > 0) {
//             fieldsToClear = $(allFields).not(sectionsOptions[datatable].bazdatatable.keepFieldsData).get();//diff array
//         } else if (fieldsToKeep && fieldsToKeep.length === 0) {
//             fieldsToClear = null;
//         } else {
//             fieldsToClear = allFields;
//         }
//         if (fieldsToClear) {
//             $.each(fieldsToClear, function(i,v) {
//                 v = '#' + v;
//                 if ($(v).data('bazscan')) {
//                     if ($(v)[0].tagName === 'INPUT' && $(v)[0].type === 'checkbox') {
//                         $(v).prop('checked', $(v).prop('defaultChecked'));
//                     } else if ($(v)[0].tagName === 'INPUT' || $(v)[0].tagName === 'TEXTAREA') {
//                         $(v).val('');
//                     }
//                     if ($(v)[0].tagName === "SELECT") {//select2
//                         $(v).val(null).trigger('change');
//                     }
//                     if ($(v)[0].tagName === 'DIV') {
//                         if ($(v).data('bazscantype') === 'jstree') {//jstree
//                             $(v).jstree('deselect_all');
//                         }
//                         if ($(v).data('bazscantype') === 'radio') {//radio
//                             if ($(v).find('input[checked]').length !== 0) {
//                                 $(v).find('input[checked]').prop('checked', true);
//                             } else {
//                                 $(v).find('input').each(function(i,v) {
//                                     $(v).prop('checked', false);
//                                 });
//                             }
//                         }
//                         if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
//                             $(v).trumbowyg('empty');
//                         }                   
//                     }
//                 }
//             });
//         }
//     };

//     //Sections to Obj
//     BazContent.prototype.sectionsToObj = function() {
//         var extractComponentId;
//         $('.sections').find('[data-bazpost]').each(function(i,v) {
//             if (v.tagName !== 'FIELDSET') {
//                 if ($(v).parents('fieldset').data('bazscantype') !== 'datatable') {
//                     if (v.tagName === 'INPUT' && v.type === 'checkbox') {
//                         extractComponentId = $(v)[0].id.split('-',2);
//                         if ($(v).data('bazpost')) {
//                             if ($(v)[0].checked === true) {
//                                 bazDataCollection[componentId][extractComponentId[1]] = 'YES';
//                             } else {
//                                 bazDataCollection[componentId][extractComponentId[1]] = 'NO';
//                             }
//                         } else {
//                             delete bazDataCollection[componentId][extractComponentId[1]];
//                         }
//                     } else if (v.tagName === 'INPUT' || v.tagName === "TEXTAREA") {
//                         extractComponentId = $(v)[0].id.split('-',2);
//                         if ($(v).data('bazpost')) {
//                             if ($(v)[0].value === 'undefined') {//kill if incorrect Data
//                                 that.error('data is undefined!');
//                                 return;
//                             } else {
//                                 bazDataCollection[componentId][extractComponentId[1]] = $(v)[0].value;
//                             }
//                         } else {
//                             delete bazDataCollection[componentId][extractComponentId[1]];
//                         } 
//                     } else if ($(v).data('bazscantype') === 'select2') {
//                         thatV = v;
//                         extractComponentId = $(thatV)[0].id.split('-',2);
//                         if ($(v).data('bazpost')) {
//                             if ($(thatV).data('multiple')) {
//                                 bazDataCollection[componentId][extractComponentId[1]] = [];
//                                 $($(v)[0].selectedOptions).each(function(i,v){
//                                     var thisSelectId = $(v)[0].value;
//                                     var thisSelectName = $(v)[0].text;
//                                     var thisSelectObject = { };
//                                     thisSelectObject[thisSelectId] = thisSelectName;
//                                     extractComponentId = $(v)[0].id.split('-',2);
//                                     bazDataCollection[componentId][extractComponentId[1]].push(thisSelectObject);
//                                 });
//                             } else {
//                                 bazDataCollection[componentId][extractComponentId[1]] = $(thatV).val();
//                             }
//                         } else {
//                             delete bazDataCollection[componentId][extractComponentId[1]];
//                         }
//                     } else if ($(v).data('bazscantype') === 'radio') {// icheck-radio
//                         extractComponentId = $(v)[0].id.split('-',2);
//                         if ($(v).data('bazpost')) {
//                             bazDataCollection[componentId][extractComponentId[1]] = 
//                             $(v).find('input:checked').siblings('label').text();
//                         } else {
//                             delete bazDataCollection[componentId][extractComponentId[1]];
//                         }
//                     } else if ($(v).data('bazscantype') === 'trumbowyg') {//trumbowyg
//                         extractComponentId = $(v)[0].id.split('-',2);
//                         if ($(v).data('bazpost')) {
//                             bazDataCollection[componentId][extractComponentId[1]] = $(v).trumbowyg('html');
//                         } else {
//                             delete bazDataCollection[componentId][extractComponentId[1]];
//                         }
//                     } else if ($(v).data('bazscantype') === 'counters') {//counters
//                         thatV = v;
//                         extractComponentId = $(thatV)[0].id.split('-',2);
//                         if ($(v).data('bazpost')) {                        
//                             bazDataCollection[componentId][extractComponentId[1]] = [];
//                             $(v).find('span').each(function(i,v) {
//                                 var thisCounterId = $(v).parent('div')[0].id;
//                                 var counterObject = { };
//                                 counterObject[thisCounterId] = $(v).html();
//                                 bazDataCollection[componentId][extractComponentId[1]].push(counterObject);
//                             });
//                         } else {
//                             delete bazDataCollection[componentId][extractComponentId[1]];
//                         }                            
//                     }
//                 }
//             }
//         });
//         // Add tables data to bazDataCollection
//         // for (var section in tableData) {
//         //  for (var data in tableData[section]) {
//         //      var excludeActions = false;
//         //      var excludeSeqAndSort = false;
//         //      var currentTableDataLength = 0;
//         //      if ((sectionsOptions[data].bazdatatable.rowButtons.canDelete === true) || (sectionsOptions[data].bazdatatable.rowButtons.canEdit === true)) {
//         //          excludeActions = true;
//         //      }
//         //      if (sectionsOptions[data].datatable.rowReorder === true) {
//         //          excludeSeqAndSort = true;
//         //      }
//         //      bazDataCollection[componentId][section][data] = [];
//         //      $.each(tableData[section][data].rows().data(), function(i,v) {
//         //          var startAt = 0;
//         //          if (excludeSeqAndSort && excludeActions) {
//         //              currentTableDataLength = v.length - 3;
//         //              startAt = 2;
//         //          } else if (!excludeSeqAndSort && excludeActions) {
//         //              currentTableDataLength = v.length - 1;
//         //          } else if (excludeSeqAndSort && !excludeActions) {
//         //              currentTableDataLength = v.length - 2;
//         //              startAt = 2;
//         //          }
//         //          var thatI = i;
//         //          bazDataCollection[componentId][section][data][i] = { };
//         //          for (var j = 0; j < currentTableDataLength; j++) {
//         //              var columnData;
//         //              var columnDataHasId = v[startAt].match(/id="(.*?)"/g)
//         //              if (columnDataHasId) {
//         //                  columnData = (columnDataHasId.toString().match(/"(.*?)"/g)).toString().replace(/"/g, '');
//         //              } else {
//         //                  columnData = v[startAt];
//         //              }
//         //              bazDataCollection[componentId][section][data][thatI][dataTableFields[componentId][section][data][startAt]] = columnData;
//         //              startAt++;
//         //          }
//         //      });
//         //  }
//         // }
//         //Generate XML from data (sdk.xml data)
//         // var toxml = that.sectionsObjToXml(bazDataCollection);
//         //Generate BazContent field details table
//         // var tobazdatafields = that.bazFieldDetails(sectionsOptions)
//         //eslint-disable-next-line
//         // console.log(oldSectionsDataCollection);
//         // eslint-disable-next-line
//         // console.log(toxml);
//         // BootstrapDialog.alert({
//         //  size: BootstrapDialog.SIZE_WIDE,
//         //  message: tobazdatafields,
//         //  cssClass: 'modal-full-width'
//         // });       
//     };

//     //DataArray to sections
//     BazContent.prototype.dataArrayToSections = function(dataId, dataURL) {
//         var dataArray;
//         jQuery.ajaxSetup({async:false});
//         $.get(rootPath + 'index.php?route=' + dataURL + '&id=' + dataId, function(data) {
//                             dataArray = data;
//                         }, 'json');
//         jQuery.ajaxSetup({async:true});

//         for (var data in dataArray) {
//             //eslint-disable-next-line
//             console.log(dataArray[data]);
//             $.each(dataArray[data], function(index, data) {//DO ALL INPUT FILEDS & COUNTERS
//                 if (!data.childs) {
//                     if ($('#' + data.id).data('bazscan')) {
//                         if (($('#' + data.id)[0].tagName === 'INPUT' && $('#' + data.id)[0].type !== 'checkbox') || $('#' + data.id)[0].tagName === "TEXTAREA") {//Input
//                             $('#' + data.id).val(data.value);
//                         } else if ($('#' + data.id).data('bazscantype') === 'trumbowyg') {//trumbowyg
//                             $('#' + data.id).trumbowyg('html', data.value);
//                         }
//                     }
//                 }
//             });
//             $.each(dataArray[data], function(index, data) {//COUNTERS, SELECT2 & DATATABLE
//                 if (data.childs) {
//                     if ($('#' + data.id).data('bazscan')) {
//                         if ($('#' + data.id).data('bazscantype') === 'counters') {//counters
//                             $.each(data.childs, function(index, child) {
//                                 $('#' + child[0].id + ' span').html(child[0].value);
//                             });
//                         } else if ($('#' + data.id).data('bazscantype') === 'select2') {//select2
//                             if ($('#' + data.id).prop('multiple')) {
//                                 var multipleId = [];
//                                 $.each(data.childs, function(index, child) {
//                                     if ($('#' + data.id).find("option[value='" + child[0].id + "']").length) {
//                                         multipleId.push(child[0].id);
//                                     } else {
//                                         var newOption = new Option(child[0].value, child[0].id, true, true);
//                                         $('#' + data.id).append(newOption).trigger('change');                                       
//                                     }
//                                 });
//                                 if (multipleId.length > 0) {
//                                     $('#' + data.id).val(multipleId).trigger('change');
//                                 }
//                             } else {    
//                                 $.each(data.childs, function(index, child) {
//                                     $('#' + data.id).val(child[0].id);
//                                 });
//                                 $('#' + data.id).trigger('change');
//                             }
//                         }
//                     } else if ($('#' + data.id).parents('fieldset').data('bazscantype') === 'datatable') {//Datatable
//                         $.each(data.childs, function(index, child) {
//                             var childArray = [];
//                             if (sectionsOptions[data.id].datatable.rowReorder) {
//                                 childArray.push(index);
//                                 childArray.push('<i class="fa fa-sort"></i>');
//                             }
//                             $.each(child, function(index, children) {
//                                 //eslint-disable-next-line
//                                 console.log(children.id);
//                                 childArray.push(children.value);
//                             });
//                             if (sectionsOptions[data.id].bazdatatable.rowButtons.canDelete && !sectionsOptions[data.id].bazdatatable.rowButtons.canEdit) {
//                                 childArray.push('<button data-row-id="' + index + '" type="button" class="btn btn-xs btn-danger pull-right margin-left-5 tableDeleteButton"><i class="fa fa-trash-o"></i></button>');
//                             } else if (!sectionsOptions[data.id].bazdatatable.rowButtons.canDelete && sectionsOptions[data.id].bazdatatable.rowButtons.canEdit) {
//                                 childArray.push('<button data-row-id="' + index + '" type="button" class="btn btn-xs btn-primary pull-right tableEditButton"><i class="fa fa-pencil"></i></button>');
//                             } else if (sectionsOptions[data.id].bazdatatable.rowButtons.canDelete && sectionsOptions[data.id].bazdatatable.rowButtons.canEdit) {
//                                 childArray.push('<button data-row-id="' + index + '" type="button" class="btn btn-xs btn-danger pull-right margin-left-5 tableDeleteButton"><i class="fa fa-trash-o"></i></button>' +
//                                                 '<button data-row-id="' + index + '" type="button" class="btn btn-xs btn-primary pull-right tableEditButton"><i class="fa fa-pencil"></i></button>');
//                             }
//                             tableData[$('#' + data.id).parents('section')[0].id][data.id].row.add(childArray).draw();
//                         });
//                         tableData[$('#' + data.id).parents('section')[0].id][data.id].responsive.recalc();
//                         that.registerDatatableButtons(tableData[$('#' + data.id).parents('section')[0].id][data.id], 
//                             $('#' + data.id + '-table-div'), 
//                             data.id, 
//                             $('#' + data.id).parents('section')[0].id, 
//                             $('#' + data.id).parents('form').children('fieldset')[0].id);
//                     }
//                 }
//             });
//             $.each(dataArray[data], function(index, data) {//CHECKBOX & RADIO TRIGGER CHANGE, CAUSING INPUT/FIELDS TO CHANGE (LAST STEP)
//                 if (!data.childs) {
//                     if ($('#' + data.id).data('bazscan')) {         
//                         if ($('#' + data.id)[0].tagName === 'INPUT' && $('#' + data.id)[0].type === 'checkbox') {
//                             if (data.value === 'YES') {
//                                 $('#' + data.id).prop('checked', true);
//                             } else if (data.value === 'NO') {
//                                 $('#' + data.id).prop('checked', false);
//                             }
//                             $('#' + data.id).trigger('change', true);
//                         } else if ($('#' + data.id).data('bazscantype') === 'radio') {// icheck-radio
//                             $('#' + data.id + ' label:contains("' + data.value + '")').siblings('input').attr('checked', true);
//                             $('input[name=' + data.id + ']:checked').trigger('change', true);
//                         } 
//                     }
//                 }
//             });
//         }
//                         // } else if ($('#' + data.id).data('bazscantype') === 'counters') {//counters
//                         //  thatV = v;
//                         //  bazDataCollection[componentId][sectionId][$('#' + data.id)[0].id] = [];
//                         //  $('#' + data.id).find('span').each(function(i,v) {
//                         //      var thisCounterId = $('#' + data.id).parent('div')[0].id;
//                         //      bazDataCollection[componentId][sectionId][$(thatV)[0].id][thisCounterId] = [];
//                         //      bazDataCollection[componentId][sectionId][$(thatV)[0].id][thisCounterId].push($('#' + data.id).html());
//                         //  });
//                         // }                                                          
//                         //          } else if ($('#' + data.id).data('bazscantype') === 'select2') {
//                         //              thatV = v;
//                         //              bazDataCollection[componentId][sectionId][$(thatV)[0].id] = [];
//                         //              $($('#' + data.id)[0].selectedOptions).each(function(i,v){
//                         //                  bazDataCollection[componentId][sectionId][$(thatV)[0].id].push($('#' + data.id)[0].value);
//                         //              });
//     };

//     //Sections Object to XML
//     BazContent.prototype.sectionsObjToXml = function(object) {
//         var preXml = '<?xml version="1.0" encoding="UTF-8"?>\n' +
//                     '<sdk>\n<hook>\n<component>APPLICATION</component>\n<method>Get</method>\n<input>\n<name>id</name>\n<value>1</value>\n' +
//                     '</input>\n<output>\n<api>\n<status>APIE_NONE</status>\n';

//         var toXml = function(objectValue, objectId, children) {
//             var xml = "";
//             if (objectValue instanceof Array) {
//                 xml += '<array>\n<array>\n<name>id</name>\n<value>' + objectId + 
//                         '</value>\n</array>\n<array>\n<name>childs</name>\n';
//                 for (var i = 0, n = objectValue.length; i < n; i++) {
//                     xml += toXml(objectValue[i], objectId, true);
//                 }
//                 xml += '</array>\n</array>\n';              
//             }
//             else if (typeof(objectValue) == "object") {
//                 if (children) {
//                     xml += '<array>\n';
//                 }
//                 for (var x in objectValue) {
//                     xml += toXml(objectValue[x], x);
//                 }
//                 if (children) {
//                     xml += '</array>\n';
//                 }
//             }
//             else {
//                 if (children) {
//                     xml += '<array>\n<array>\n<name>value</name>\n<value><![CDATA[' + objectValue.toString() + 
//                             ']]></value>\n</array>\n</array>\n';
//                 } else {
//                     xml += '<array>\n<array>\n<name>id</name>\n<value>' + objectId + 
//                             '</value>\n</array>\n<array>\n<name>value</name>\n<value><![CDATA[' + objectValue.toString() + 
//                             ']]></value>\n</array>\n</array>\n';
//                 }
//             }
//             return xml;
//         }, xml="";
//         for (var id in object) {
//             xml += toXml(object[id], id);
//         }
//         var postXml = '</api>\n</output>\n</hook>\n</sdk>';
//         var finalXml = preXml + xml + postXml;
//         return finalXml;
//     };

//     //Sections Object to XML
//     BazContent.prototype.bazFieldDetails = function(object) {
//         var preDetails = '<table class="table table-striped nowrap border-solid border-default" style="margin:0 !important;" width="100%" cellspacing="0">';
//         var foundTh, rowDetails, postDetails, finalDetails;
//         var th = [];

//         for (var id in object) {
//             if (object[id].bazFieldDetails) {
//                 extractTh(object[id].bazFieldDetails);
//                 rowDetails += '<tr>'
//                 extractDetails(object[id].bazFieldDetails);
//                 rowDetails += '</tr>'
//             }
//         }
//         function extractTh(objectValue) {
//             for (var thValues in objectValue) {
//                 foundTh = $.inArray(thValues,th);
//                 if (foundTh === -1) {
//                     th.push(thValues);
//                 }
//             }
//         }

//         function extractDetails(objectValue) {
//             for (var rowValues in objectValue) {
//                 $.each(th, function(i,v) {
//                     if (v === rowValues) {
//                         rowDetails += '<td>' + objectValue[rowValues] + '</td>';
//                     }
//                 });
//             }
//         }
//         preDetails += '<thead><tr><th>' + th.join('</th><th>') + '</th></tr></thead>';
//         postDetails = '</table>';
//         finalDetails = preDetails + rowDetails + postDetails;

//         return finalDetails;
//     };

//     //Diff Old data with new data
//     BazContent.prototype.diffData = function() {
//         // var deepDiff = DeepDiff.noConflict();
//         // var diff = deepDiff(oldSectionsDataCollection, bazDataCollection);

//         // //eslint-disable-next-line
//         // console.log(diff);
//         var delta = jsondiffpatch.diff(oldSectionsDataCollection, bazDataCollection);
//         var el = document.getElementById('visual');
//         jsondiffpatch.formatters.html.hideUnchanged(el, 10);
//         el.innerHTML = jsondiffpatch.formatters.html.format(delta, oldSectionsDataCollection);

//     }
//     //Register any custom events
//     BazContent.prototype.registerCustomEvents = function() {
//         //
//     }; 

//     // BazContentPlugin Definition
//     // =================
//     function BazContentPlugin(option) {
//         return this.each(function() {
//             var $this = $(this);
//             var data = $this.data(DataKey);

//             if (!data) {
//                 var options = $.extend({}, Default, $this.data(), typeof option === 'object' && option);
//                 $this.data(DataKey, (data = new BazContent($this, options)));
//             }

//             if (typeof option === 'string') {
//                 if (typeof data[option] === 'undefined') {
//                     throw new Error('No method named ' + option);
//                 }
//                 data[option]();
//             }
//         });
//     }

//     var old = $.fn.bazContent;

//     $.fn.bazContent             = BazContentPlugin;
//     $.fn.bazContent.Constructor = BazContent;

//     // No Conflict Mode
//     // ================
//     $.fn.bazContent.noConflict = function () {
//         $.fn.bazContent = old;
//         return this;
//     };
// })(jQuery);