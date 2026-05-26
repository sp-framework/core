<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Contacts;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Multiple
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $adminLTETags;

    protected $params;

    protected $content;

    protected $contactsParams = [];

    protected $compSecId;

    public function __construct($view, $tag, $links, $escaper, $params, $contactsParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->adminLTETags = new Adminltetags();

        $this->params = $params;

        $this->contactsParams = $contactsParams;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->buildSingleContactData();

        $this->buildMultipleContactsLayout();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildSingleContactData()
    {
        $fieldsArr = ['portrait','prefix','firstName','lastName','suffix','email','secondaryEmail','contactPhone','contactPhoneExt','contactMobile','contactOther','contactFax','contactNotes'];
        foreach ($fieldsArr as $field) {
            $this->contactsParams[$field] =
                isset($this->params['contacts'][$field]) ?
                $this->params['contacts'][$field] :
                '';
        }

        $this->contactsParams['secondaryEmailFieldLabel'] =
            isset($this->params['secondaryEmailFieldLabel']) ?
            $this->params['secondaryEmailFieldLabel'] :
            'Secondary Email';

        $this->contactsParams['contactOtherFieldLabel'] =
            isset($this->params['contactOtherFieldLabel']) ?
            $this->params['contactOtherFieldLabel'] :
            'Other Contact #';

        $this->contactsParams['contactFaxFieldLabel'] =
            isset($this->params['contactFaxFieldLabel']) ?
            $this->params['contactFaxFieldLabel'] :
            'Fax';

        $this->contactsParams['contactNotesFieldLabel'] =
            isset($this->params['contactNotesFieldLabel']) ?
            $this->params['contactNotesFieldLabel'] :
            'Contact Notes';

        $fieldsArr = null;
        $field = null;

        $fieldsArr = ['includePortrait','includeNamePrefix','includeName','includeNameSuffix','includeEmail','includeOther','includeNotes','firstNameFieldHidden','firstNameFieldDisabled','firstNameFieldRequired','firstNameFieldBazPostOnCreate','firstNameFieldBazPostOnUpdate','lastNameFieldHidden','lastNameFieldDisabled','lastNameFieldRequired','lastNameFieldBazPostOnCreate','lastNameFieldBazPostOnUpdate','emailFieldHidden','emailFieldDisabled','emailFieldRequired','emailFieldBazPostOnCreate','emailFieldBazPostOnUpdate','secondaryEmailFieldHidden','secondaryEmailFieldDisabled','secondaryEmailFieldRequired','secondaryEmailFieldBazPostOnCreate','secondaryEmailFieldBazPostOnUpdate','ccEmailsToSecondaryEmailFieldHidden','ccEmailsToSecondaryEmailFieldDisabled','ccEmailsToSecondaryEmailFieldRequired','ccEmailsToSecondaryEmailFieldBazPostOnCreate','ccEmailsToSecondaryEmailFieldBazPostOnUpdate','contactPhoneFieldHidden','contactPhoneFieldDisabled','contactPhoneFieldRequired','contactPhoneFieldBazPostOnCreate','contactPhoneFieldBazPostOnUpdate','contactPhoneExtFieldHidden','contactPhoneExtFieldDisabled','contactPhoneExtFieldRequired','contactPhoneExtFieldBazPostOnCreate','contactPhoneExtFieldBazPostOnUpdate','contactMobileFieldHidden','contactMobileFieldDisabled','contactMobileFieldRequired','contactMobileFieldBazPostOnCreate','contactMobileFieldBazPostOnUpdate','contactFaxFieldHidden','contactFaxFieldDisabled','contactFaxFieldRequired','contactFaxFieldBazPostOnCreate','contactFaxFieldBazPostOnUpdate','contactOtherFieldHidden','contactOtherFieldDisabled','contactOtherFieldRequired','contactOtherFieldBazPostOnCreate','contactOtherFieldBazPostOnUpdate','contactNotesFieldHidden','contactNotesFieldDisabled','contactNotesFieldRequired','contactNotesFieldBazPostOnCreate','contactNotesFieldBazPostOnUpdate'];

        foreach ($fieldsArr as $field) {
            $this->contactsParams[$field] =
                isset($this->params[$field]) &&
                    $this->params[$field] === true ?
                true :
                false;
        }
    }

    protected function buildMultipleContactsLayout()
    {
        $this->contactsParams['multiple'] = true;

        $singleContactArr = [
            'component'                                   => $this->params['component'],
            'componentName'                               => $this->params['componentName'],
            'componentId'                                 => $this->params['componentId'],
            'sectionId'                                   => $this->params['sectionId'],
            'contactFieldType'                            => 'single',
            'multiple'                                    => true
        ];

        $singleContactArr = array_merge($singleContactArr, $this->contactsParams);

        $this->content .=
            '<div class="row vdivide" id="' . $this->compSecId . '-contacts">
                <div class="col">
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                             => $this->params['component'],
                                    'componentName'                         => $this->params['componentName'],
                                    'componentId'                           => $this->params['componentId'],
                                    'sectionId'                             => $this->params['sectionId'],
                                    'fieldId'                               => 'contact_ids',
                                    'fieldLabel'                            => 'Contact IDs',
                                    'fieldType'                             => 'input',
                                    'fieldHelp'                             => true,
                                    'fieldHelpTooltipContent'               => 'Contact IDs',
                                    'fieldRequired'                         => false,
                                    'fieldBazScan'                          => false,
                                    'fieldBazPostOnCreate'                  => true,
                                    'fieldBazPostOnUpdate'                  => true,
                                    'fieldHidden'                           => true,
                                    'fieldDisabled'                         => true,
                                    'fieldValue'                            => ''
                                ]
                            ) .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                             => $this->params['component'],
                                    'componentName'                         => $this->params['componentName'],
                                    'componentId'                           => $this->params['componentId'],
                                    'sectionId'                             => $this->params['sectionId'],
                                    'fieldId'                               => 'delete_contact_ids',
                                    'fieldLabel'                            => 'Delete Contact IDs',
                                    'fieldType'                             => 'input',
                                    'fieldHelp'                             => true,
                                    'fieldHelpTooltipContent'               => 'Delete Contact IDs',
                                    'fieldRequired'                         => false,
                                    'fieldBazScan'                          => false,
                                    'fieldBazPostOnCreate'                  => true,
                                    'fieldBazPostOnUpdate'                  => true,
                                    'fieldHidden'                           => true,
                                    'fieldDisabled'                         => true,
                                    'fieldValue'                            => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                             => $this->params['component'],
                                    'componentName'                         => $this->params['componentName'],
                                    'componentId'                           => $this->params['componentId'],
                                    'sectionId'                             => $this->params['sectionId'],
                                    'fieldId'                               => 'contact_id',
                                    'fieldLabel'                            => 'Contact ID',
                                    'fieldType'                             => 'input',
                                    'fieldHelp'                             => true,
                                    'fieldHelpTooltipContent'               => 'Contact ID',
                                    'fieldRequired'                         => false,
                                    'fieldBazScan'                          => false,
                                    'fieldBazPostOnCreate'                  => false,
                                    'fieldBazPostOnUpdate'                  => false,
                                    'fieldHidden'                           => true,
                                    'fieldDisabled'                         => true,
                                    'fieldValue'                            => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row" id="' . $this->compSecId . '-contact_types-contact">
                        <div class="col">' .
                            $this->adminLTETags->useTag('contacts', $singleContactArr) .
                        '</div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('buttons',
                                [
                                    'component'                     => $this->params['component'],
                                    'componentName'                 => $this->params['componentName'],
                                    'componentId'                   => $this->params['componentId'],
                                    'sectionId'                     => $this->params['sectionId'],
                                    'buttonType'                    => 'button',
                                    'buttons'                       =>
                                        [
                                            'add-contact'       => [
                                                'title'                   => 'Add',
                                                'size'                    => 'xs',
                                                'type'                    => 'primary',
                                                'icon'                    => 'plus',
                                                'position'                => 'right'
                                            ],
                                            'update-contact'    => [
                                                'title'                   => 'Update',
                                                'hidden'                  => true,
                                                'disabled'                => true,
                                                'size'                    => 'xs',
                                                'type'                    => 'primary',
                                                'icon'                    => 'plus',
                                                'position'                => 'right'
                                            ],
                                            'cancel-contact'    => [
                                                'title'                   => 'Cancel',
                                                'size'                    => 'xs',
                                                'type'                    => 'secondary',
                                                'icon'                    => 'times',
                                                'position'                => 'right'
                                            ]
                                        ]
                                ]
                            ) .
                        '</div>
                    </div>
                </div>
                <div class="col">
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                 => $this->params['component'],
                                    'componentName'             => $this->params['componentName'],
                                    'componentId'               => $this->params['componentId'],
                                    'sectionId'                 => $this->params['sectionId'],
                                    'fieldId'                   => 'contacts',
                                    'fieldLabel'                => 'Contacts',
                                    'fieldType'                 => 'html',
                                    'fieldHelp'                 => true,
                                    'fieldHelpTooltipContent'   => 'List of contacts',
                                    'fieldAdditionalClass'      => 'mb-0',
                                    'fieldRequired'             => false,
                                    'fieldBazScan'              => false,
                                    'fieldBazJstreeSearch'      => true,
                                    'fieldBazPostOnCreate'      => false,
                                    'fieldBazPostOnUpdate'      => false
                                ]
                            ) .
                            '<ul class="list-group list-group-sortable" id="' . $this->compSecId . '-sortable-contacts-list">';
                                if (isset($this->params['contacts']) && is_array($this->params['contacts']) && count($this->params['contacts']) > 0) {
                                    $this->content .=
                                        '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-contacts-list-nodata" hidden>
                                            <div class="row">
                                                <div class="col text-uppercase">
                                                    <i class="fa fa-fw fa-exclamation"></i> Add New Contact
                                                </div>
                                            </div>
                                        </div>';

                                    foreach ($this->params['contacts'] as $key => $contact) {
                                        $this->content .=
                                            '<li class="list-group-item list-group-item-secondary" area-disabled="false" style="cursor: pointer" data-new="0" data-contact-id="' . $contact['id'] . '">
                                                <div class="row">
                                                    <div class="col">
                                                        <i class="fa fa-sort fa-fw handle"></i>
                                                    </div>
                                                    <div class="col">
                                                        <button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 contactDeleteButton">
                                                            <i class="fa fas fa-fw text-xs fa-trash"></i>
                                                        </button>
                                                        <button data-sort-id="" type="button" class="btn btn-xs btn-primary float-right ml-1 contactEditButton">
                                                            <i class="fa fas fa-fw text-xs fa-edit"></i>
                                                        </button>
                                                        <button data-sort-id="" type="button" class="btn btn-xs btn-info float-right ml-1 contactCopyButton">
                                                            <i class="fa fas fa-fw text-xs fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col list-group-item-data">
                                                        <dl class="row mb-0">
                                                            <dt class="text-uppercase mb-0 col-sm-4">Contact Reference</dt>
                                                            <dd class="mb-0 col-sm-8 cla-contactReference">' . $contact['contact_reference'] . '</dd>';
                                                            if (isset($contact['attention_to']) && $contact['attention_to'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Attention To</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-attentionTo">' . $contact['attention_to'] . '</dd>';
                                                            }
                                                            $this->content .=
                                                                '<dt class="text-uppercase mb-0 col-sm-4">Street Contact</dt>
                                                                <dd class="mb-0 col-sm-8 cla-street">' . $contact['street_contact'] . '</dd>';
                                                            if (isset($contact['street_contact_2']) && $contact['street_contact_2'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Street Contact 2</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-street2">' . $contact['street_contact_2'] . '</dd>';
                                                            }
                                                            if (isset($contact['street_contact_3']) && $contact['street_contact_3'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Street Contact 3</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-street3">' . $contact['street_contact_3'] . '</dd>';
                                                            }
                                                            if (isset($contact['street_contact_4']) && $contact['street_contact_4'] !== '') {
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">Street Contact 4</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-street4">' . $contact['street_contact_4'] . '</dd>';
                                                            }
                                                            $this->content .=
                                                                '<dt class="text-uppercase mb-0 col-sm-4">City</dt>
                                                                <dd class="mb-0 col-sm-8 cla-city" data-id="' . $contact['city_id'] . '">' . $contact['city_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Post Code</dt>
                                                                <dd class="mb-0 col-sm-8 cla-postcode">' . $contact['post_code'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">State</dt>
                                                                <dd class="mb-0 col-sm-8 cla-state" data-id="' . $contact['state_id'] . '">' . $contact['state_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Country</dt>
                                                                <dd class="mb-0 col-sm-8 cla-country" data-id="' . $contact['country_id'] . '">' . $contact['country_name'] . '</dd>
                                                        </dl>
                                                    </div>
                                                </div>
                                            </li>';
                                    }
                                } else {
                                    $this->content .=
                                        '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-contacts-list-nodata">
                                            <div class="row">
                                                <div class="col text-uppercase">
                                                    <i class="fa fa-fw fa-exclamation"></i> Add New Contact
                                                </div>
                                            </div>
                                        </div>';
                                }

                            $this->content .=
                            '</ul>
                        </div>
                    </div>
                </div>
            </div>' .
            $this->inclContactsJs();
    }

    protected function inclContactsJs()
    {
        $inclJs =
            '<script type="text/javascript">
            var dataCollectionComponent, dataCollectionSection, dataCollectionSectionForm;

            if (!window["dataCollection"]["' . $this->params['componentId'] . '"]) {
                dataCollectionComponent =
                    window["dataCollection"]["' . $this->params['componentId'] . '"] = { };
            } else {
                dataCollectionComponent =
                    window["dataCollection"]["' . $this->params['componentId'] . '"];
            }
            if (!dataCollectionComponent["' . $this->compSecId . '"]) {
                dataCollectionSection =
                    dataCollectionComponent["' . $this->compSecId . '"] = { };
            } else {
                dataCollectionSection =
                    dataCollectionComponent["' . $this->compSecId . '"];
            }
            if (!dataCollectionSection["' . $this->compSecId . '-form"]) {
                dataCollectionSectionForm =
                    dataCollectionSection["' . $this->compSecId . '-form"] = { };
            } else {
                dataCollectionSectionForm =
                    dataCollectionSection["' . $this->compSecId . '-form"];
            }

            dataCollectionSection =
                $.extend(dataCollectionSection, {
                    "' . $this->compSecId . '-contact_reference"                   : {
                        afterInit : function () {
                            dataCollectionSection["data"]["contact_ids"] = { }
                            dataCollectionSection["data"]["delete_contact_ids"] = [];

                            function initMainButtons() {
                                $("#' . $this->compSecId . '-cancel-contact").off();
                                $("#' . $this->compSecId . '-cancel-contact").click(function(e) {
                                    e.preventDefault();
                                    $(".contactEditButton, .contactDeleteButton").attr("disabled", false);
                                    toggleContactFields(true);
                                    $("#' . $this->compSecId . '-contacts").trigger("contactCancel");
                                });
                                $("#' . $this->compSecId . '-add-contact, #' . $this->compSecId . '-update-contact").off();
                                $("#' . $this->compSecId . '-add-contact, #' . $this->compSecId . '-update-contact").attr("disabled", false);
                                $("#' . $this->compSecId . '-add-contact, #' . $this->compSecId . '-update-contact").click(function(e) {
                                    e.preventDefault();
                                    $(".contactEditButton, .contactDeleteButton").attr("disabled", false);

                                    if ($(this)[0].id === "' . $this->compSecId . '-update-contact") {
                                        extractData(true);
                                        $("#' . $this->compSecId . '-contacts").trigger("contactUpdate");
                                    } else {
                                        extractData();
                                        $("#' . $this->compSecId . '-contacts").trigger("contactAdd");
                                    }
                                });
                            }

                            function toggleContactFields(status, update = false) {
                                if (status === true) {
                                    $("#' . $this->compSecId . '-contact_reference").val("");
                                    $("#' . $this->compSecId . '-contact_id").val("");
                                    $("#' . $this->compSecId . '-attention_to").val("");
                                    $("#' . $this->compSecId . '-street_contact").val("");
                                    $("#' . $this->compSecId . '-street_contact_2").val("");
                                    $("#' . $this->compSecId . '-street_contact_3").val("");
                                    $("#' . $this->compSecId . '-street_contact_4").val("");
                                    $("#' . $this->compSecId . '-city_id").val("");
                                    $("#' . $this->compSecId . '-city_name").val("");
                                    $("#' . $this->compSecId . '-post_code").val("");
                                    $("#' . $this->compSecId . '-state_id").val("");
                                    $("#' . $this->compSecId . '-state_name").val("");
                                    $("#' . $this->compSecId . '-country_id").val("");
                                    $("#' . $this->compSecId . '-country_name").val("");
                                }

                                if (update === true) {
                                    $("#' . $this->compSecId . '-add-contact").attr("hidden", true);
                                    $("#' . $this->compSecId . '-update-contact").attr("hidden", false);
                                    $("#' . $this->compSecId . '-update-contact").attr("disabled", false);
                                } else {
                                    $("#' . $this->compSecId . '-update-contact").attr("hidden", true);
                                    $("#' . $this->compSecId . '-add-contact").attr("hidden", false);
                                    $("#' . $this->compSecId . '-add-contact").attr("disabled", false);
                                }

                                $("#' . $this->compSecId . '-contact_reference").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-attention_to").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_contact").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_contact_2").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_contact_3").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-street_contact_4").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-city_name").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-post_code").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-state_name").removeClass("is-invalid");
                                $("#' . $this->compSecId . '-country_name").removeClass("is-invalid");
                            }

                            function extractData(update = false) {
                                if ($("#' . $this->compSecId . '-contact_reference").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-contact_reference").val() === "") {
                                    $("#' . $this->compSecId . '-contact_reference").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-contact_reference").focus(function() {
                                        $("#' . $this->compSecId . '-contact_reference").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-attention_to").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-attention_to").val() === "") {
                                    $("#' . $this->compSecId . '-attention_to").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-attention_to").focus(function() {
                                        $("#' . $this->compSecId . '-attention_to").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_contact").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_contact").val() === "") {
                                    $("#' . $this->compSecId . '-street_contact").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_contact").focus(function() {
                                        $("#' . $this->compSecId . '-street_contact").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_contact_2").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_contact_2").val() === "") {
                                    $("#' . $this->compSecId . '-street_contact_2").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_contact_2").focus(function() {
                                        $("#' . $this->compSecId . '-street_contact_2").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_contact_3").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_contact_3").val() === "") {
                                    $("#' . $this->compSecId . '-street_contact_3").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_contact_3").focus(function() {
                                        $("#' . $this->compSecId . '-street_contact_3").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-street_contact_4").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-street_contact_4").val() === "") {
                                    $("#' . $this->compSecId . '-street_contact_4").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-street_contact_4").focus(function() {
                                        $("#' . $this->compSecId . '-street_contact_4").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-city_name").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-city_name").val() === "") {
                                    $("#' . $this->compSecId . '-city_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-city_name").focus(function() {
                                        $("#' . $this->compSecId . '-city_name").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-post_code").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-post_code").val() === "") {
                                    $("#' . $this->compSecId . '-post_code").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-post_code").focus(function() {
                                        $("#' . $this->compSecId . '-post_code").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-state_name").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-state_name").val() === "") {
                                    $("#' . $this->compSecId . '-state_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-state_name").focus(function() {
                                        $("#' . $this->compSecId . '-state_name").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                if ($("#' . $this->compSecId . '-country_name").siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                    $("#' . $this->compSecId . '-country_name").val() === "") {
                                    $("#' . $this->compSecId . '-country_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-country_name").focus(function() {
                                        $("#' . $this->compSecId . '-country_name").removeClass("is-invalid");
                                    });

                                    return;
                                }

                                var data = { };
                                var contactId, contactNew;

                                data["contact_id"] = $("#' . $this->compSecId . '-contact_id").val();
                                var html =
                                    \'<dl class="row mb-0">\';
                                if ($("#' . $this->compSecId . '-contact_reference").length > 0) {
                                    data["contact_reference"] = $("#' . $this->compSecId . '-contact_reference").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Contact Reference</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-contactReference">\' + data["contact_reference"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-attention_to").length > 0) {
                                    data["attention_to"] = $("#' . $this->compSecId . '-attention_to").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Attention To</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-attentionTo">\' + data["attention_to"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_contact").length > 0) {
                                    data["street_contact"] = $("#' . $this->compSecId . '-street_contact").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Contact</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street">\' + data["street_contact"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_contact_2").length > 0) {
                                    data["street_contact_2"] = $("#' . $this->compSecId . '-street_contact_2").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Contact 2</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street2">\' + data["street_contact_2"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_contact_3").length > 0) {
                                    data["street_contact_3"] = $("#' . $this->compSecId . '-street_contact_3").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Contact 3</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street3">\' + data["street_contact_3"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-street_contact_4").length > 0) {
                                    data["street_contact_4"] = $("#' . $this->compSecId . '-street_contact_4").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Street Contact 4</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-street4">\' + data["street_contact_4"] + \'</dd>\';
                                }
                                data["city_id"] = $("#' . $this->compSecId . '-city_id").val();
                                if ($("#' . $this->compSecId . '-city_name").length > 0) {
                                    data["city_name"] = $("#' . $this->compSecId . '-city_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">City</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-city" data-id="\' + data["city_id"] + \'">\' + data["city_name"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-post_code").length > 0) {
                                    data["post_code"] = $("#' . $this->compSecId . '-post_code").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Post Code</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-postcode">\' + data["post_code"] + \'</dd>\';
                                }
                                data["state_id"] = $("#' . $this->compSecId . '-state_id").val();
                                if ($("#' . $this->compSecId . '-state_name").length > 0) {
                                    data["state_name"] = $("#' . $this->compSecId . '-state_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">State</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-state" data-id="\' + data["state_id"] + \'">\' + data["state_name"] + \'</dd>\';
                                }
                                data["country_id"] = $("#' . $this->compSecId . '-country_id").val();
                                if ($("#' . $this->compSecId . '-country_name").length > 0) {
                                    data["country_name"] = $("#' . $this->compSecId . '-country_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Country</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-country" data-id="\' + data["country_id"] + \'">\' + data["country_name"] + \'</dd>\';
                                }

                                html +=
                                    \'</dl>\';

                                if ($("#' . $this->compSecId . '-sortable-contacts-list").length > 0) {
                                    var contactsLi = $("#' . $this->compSecId . '-sortable-contacts-list li");

                                    if (data["contact_id"] === "") {
                                        contactId = Date.now();
                                        contactNew = "1";
                                    } else {
                                        contactId = data["contact_id"];
                                        contactNew = "0";
                                    }

                                    var list =
                                        \'<li class="list-group-item list-group-item-secondary\' +
                                            \'" area-disabled="false" style="cursor: pointer" \' +
                                            \'" data-new="\' + contactNew + \'" data-contact-id="\' + contactId + \'">\' +
                                            \'<div class="row">\' +
                                                \'<div class="col">\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 contactDeleteButton">\' +
                                                        \'<i class="fa fas fa-fw text-xs fa-trash"></i>\' +
                                                    \'</button>\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-primary float-right ml-1 contactEditButton">\' +
                                                        \'<i class="fa fas fa-fw text-xs fa-edit"></i>\' +
                                                    \'</button>\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-info float-right ml-1 contactCopyButton">\' +
                                                        \'<i class="fa fas fa-fw text-xs fa-copy"></i>\' +
                                                    \'</button>\' +
                                                \'</div>\' +
                                            \'</div>\' +
                                            \'<div class="row">\' +
                                                \'<div class="col list-group-item-data">\' +
                                                    html +
                                                \'</div>\' +
                                            \'</div>\' +
                                        \'</li>\';

                                    if (update === false && contactsLi.length > 0) {
                                        var exists = false;

                                        $(contactsLi).each(function(index, li) {
                                            if ($(li).find(".cla-contactReference").text() === data["contact_reference"]) {
                                                PNotify.error({"title" : "Contact with same reference already added!"});
                                                exists = true;
                                                return;
                                            }
                                        });

                                        if (exists === false) {
                                            $("#' . $this->compSecId . '-sortable-contacts-list").append(list);
                                        } else {
                                            return;
                                        }
                                    } else if (update === true) {
                                        $("#' . $this->compSecId . '-sortable-contacts-list [data-contact-id=" + contactId + "]")
                                            .find(".list-group-item-data").empty().append(html);
                                    } else {
                                        $("#' . $this->compSecId . '-sortable-contacts-list").append(list);
                                        $("#' . $this->compSecId . '-contacts-list-nodata").attr("hidden", true);
                                    }
                                }

                                collectData();
                                registerContactButtons();
                                toggleContactFields(true);
                            }

                            function collectData() {
                                if ($("#' . $this->compSecId . '-sortable-contacts-list li").length > 0) {
                                    $("#' . $this->compSecId . '-sortable-contacts-list li").each(function(index, id) {
                                        var data = { };
                                        var contactId;

                                        contactId = $(this).data("contact-id");
                                        data["new"] = $(this).data("new");

                                        $(id).find("dd").each(function(index,dd) {
                                            if ($(dd).is(".cla-attentionTo")) {
                                                data["attention_to"] = $(dd).html();
                                            } else if ($(dd).is(".cla-contactReference")) {
                                                data["contact_reference"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street")) {
                                                data["street_contact"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street2")) {
                                                data["street_contact_2"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street3")) {
                                                data["street_contact_3"] = $(dd).html();
                                            } else if ($(dd).is(".cla-street4")) {
                                                data["street_contact_4"] = $(dd).html();
                                            } else if ($(dd).is(".cla-city")) {
                                                data["city_id"] = $(dd).data("id");
                                                data["city_name"] = $(dd).html();
                                            } else if ($(dd).is(".cla-postcode")) {
                                                data["post_code"] = $(dd).html();
                                            } else if ($(dd).is(".cla-state")) {
                                                data["state_id"] = $(dd).data("id");
                                                data["state_name"] = $(dd).html();
                                            } else if ($(dd).is(".cla-country")) {
                                                data["country_id"] = $(dd).data("id");
                                                data["country_name"] = $(dd).html();
                                            }
                                        });

                                        dataCollectionSection["data"]["contact_ids"][contactId] = data;
                                    });
                                }
                            }

                            function registerContactButtons() {
                                $(".contactEditButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        $(this).attr("disabled", true);
                                        $(this).siblings(".contactDeleteButton").attr("disabled", true);

                                        $($(this).parents("li").children(".row")[1]).find("dd").each(function(index,dd) {
                                            if ($(dd).is(".cla-contactReference")) {
                                                $("#' . $this->compSecId . '-contact_reference").val($(dd).html());
                                            } else if ($(dd).is(".cla-attentionTo")) {
                                                $("#' . $this->compSecId . '-attention_to").val($(dd).html());
                                            } else if ($(dd).is(".cla-street")) {
                                                $("#' . $this->compSecId . '-street_contact").val($(dd).html());
                                            } else if ($(dd).is(".cla-street2")) {
                                                $("#' . $this->compSecId . '-street_contact_2").val($(dd).html());
                                            } else if ($(dd).is(".cla-street3")) {
                                                $("#' . $this->compSecId . '-street_contact_3").val($(dd).html());
                                            } else if ($(dd).is(".cla-street4")) {
                                                $("#' . $this->compSecId . '-street_contact_4").val($(dd).html());
                                            } else if ($(dd).is(".cla-city")) {
                                                $("#' . $this->compSecId . '-city_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-city_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-postcode")) {
                                                $("#' . $this->compSecId . '-post_code").val($(dd).html());
                                            } else if ($(dd).is(".cla-state")) {
                                                $("#' . $this->compSecId . '-state_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-state_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-country")) {
                                                $("#' . $this->compSecId . '-country_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-country_name").val($(dd).html());
                                            }

                                            $("#' . $this->compSecId . '-contact_id").val($(dd).parents("li").data("contact-id"));
                                        });
                                        toggleContactFields(false, true);
                                    });
                                });

                                $(".contactCopyButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        $("#' . $this->compSecId . '-contact_types").val(0).trigger("change");
                                        $($(this).parents("li").children(".row")[1]).find("dd").each(function(index,dd) {
                                            if ($(dd).is(".cla-contactReference")) {
                                                $("#' . $this->compSecId . '-contact_reference").val($(dd).html());
                                            } else if ($(dd).is(".cla-attentionTo")) {
                                                $("#' . $this->compSecId . '-attention_to").val($(dd).html());
                                            } else if ($(dd).is(".cla-street")) {
                                                $("#' . $this->compSecId . '-street_contact").val($(dd).html());
                                            } else if ($(dd).is(".cla-street2")) {
                                                $("#' . $this->compSecId . '-street_contact_2").val($(dd).html());
                                            } else if ($(dd).is(".cla-street3")) {
                                                $("#' . $this->compSecId . '-street_contact_3").val($(dd).html());
                                            } else if ($(dd).is(".cla-street4")) {
                                                $("#' . $this->compSecId . '-street_contact_4").val($(dd).html());
                                            } else if ($(dd).is(".cla-city")) {
                                                $("#' . $this->compSecId . '-city_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-city_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-postcode")) {
                                                $("#' . $this->compSecId . '-post_code").val($(dd).html());
                                            } else if ($(dd).is(".cla-state")) {
                                                $("#' . $this->compSecId . '-state_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-state_name").val($(dd).html());
                                            } else if ($(dd).is(".cla-country")) {
                                                $("#' . $this->compSecId . '-country_id").val($(dd).data("id"));
                                                $("#' . $this->compSecId . '-country_name").val($(dd).html());
                                            }
                                        });
                                        toggleContactFields(false, false);
                                    });
                                });

                                $(".contactDeleteButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        var contactsCount = $(this).parents("ul").children("li").length;

                                        dataCollectionSection["data"]["delete_contact_ids"].push($(this).parents("li").data("contact-id"));

                                        if (dataCollectionSection["data"]["contact_ids"][$(this).parents("li").data("contact-id")]) {
                                            delete(dataCollectionSection["data"]["contact_ids"][$(this).parents("li").data("contact-id")]);
                                        }

                                        $(this).parents("li").remove();

                                        contactsCount = contactsCount - 1;

                                        if (contactsCount === 0) {
                                            $("#' . $this->compSecId . '-contacts-list-nodata").attr("hidden", false);
                                        }
                                        collectData();
                                    });
                                });
                            }

                            initMainButtons();
                            collectData();
                            registerContactButtons();
                        }
                    }
                });
            </script>';

        return $inclJs;
    }
}
