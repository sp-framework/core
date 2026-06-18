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

        $this->contactsParams['contactPostLink'] =
            isset($this->params['contactPostLink']) ?
            $this->params['contactPostLink'] :
            '';

        if ($this->contactsParams['contactPostLink'] !== '') {
            if (!isset($this->params['contactPackageClass'])) {
                throw new \Exception('contactPostLink requires contactPackageClass');
            }
            if (!isset($this->params['contactPackageRowId'])) {
                throw new \Exception('contactPostLink requires contactPackageRowId');
            }

            $this->contactsParams['contactPackageClass'] = $this->params['contactPackageClass'];
            $this->contactsParams['contactPackageRowId'] = $this->params['contactPackageRowId'];
        } else {
            $this->contactsParams['contactPackageClass'] = '';
            $this->contactsParams['contactPackageRowId'] = '';
        }

        $this->contactsParams['contactSortable'] =
            isset($this->params['contactSortable']) ?
            $this->params['contactSortable'] :
            true;

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
                <div class="col-md-7">
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
                            '<ul class="list-group list-group-sortable" id="' . $this->compSecId . '-sortable-contacts-list" style="max-height: ' . ($this->contactsParams['includePortrait'] === true ? '700' : '450') . 'px;overflow: scroll;border-radius: 0 !important;">';
                                if (isset($this->params['contacts']) && is_array($this->params['contacts']) && count($this->params['contacts']) > 0) {
                                    $this->params['contacts'] = msort($this->params['contacts'], 'seq');

                                    $this->content .=
                                        '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-contacts-list-nodata" hidden>
                                            <div class="row">
                                                <div class="col text-uppercase">
                                                    <i class="fa fa-fw fa-exclamation"></i> Add New Contact
                                                </div>
                                            </div>`
                                        </div>';

                                    foreach ($this->params['contacts'] as $key => $contact) {
                                        $this->content .=
                                            '<li class="list-group-item list-group-item-secondary" area-disabled="false" style="border: 1px solid rgba(0, 0, 0, 0.125); cursor: pointer" data-new="0" data-contact-id="' . $contact['id'] . '" data-contact-seq="' . $contact['seq'] . '">
                                                <div class="row">';

                                                if ($this->contactsParams['contactSortable']) {
                                                    $this->content .=
                                                        '<div class="col">
                                                            <i class="fa fa-sort fa-fw handle"></i>
                                                        </div>';
                                                }

                                                $this->content .=
                                                    '<div class="col">
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
                                                <div class="text-center image-content ' . $this->compSecId . '-portrait-image-content">';
                                                    if ($contact['portrait'] !== '') {
                                                        $contactPortraitLink = $this->links->url('system/storages/q/uuid/' . $contact["portrait"] . '/w/80');

                                                        $this->content .=
                                                            '<img id="' . $this->compSecId . '-portrait-croppie-image-' . $contact['id'] . '" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $contactPortraitLink . '" class="user-image img-fluid img-thumbnail" style="max-width:80px;max-height:80px;">
                                                            <div class="image-text-portrait d-none">' . $contact['portrait'] . '</div>';
                                                    } else {
                                                        $this->content .=
                                                            '<img id="' . $this->compSecId . '-portrait-croppie-image-' . $contact['id'] . '" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->links->images('general/portrait.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:80px;max-height:80px;">
                                                            <div class="image-text-portrait d-none">' . $this->links->images('general/portrait.png') . '</div>';
                                                    }
                                                $this->content .=
                                                '</div>';
                                                $this->content .=
                                                    '<div class="row">
                                                        <div class="col list-group-item-data">
                                                            <dl class="row mb-0">
                                                                <dt class="text-uppercase mb-0 col-sm-4 d-none">Contact Reference</dt>
                                                                <dd class="mb-0 col-sm-8 cla-contactReference d-none">' . $contact['full_name'] . '</dd>';

                                                            $this->content .=
                                                                '<dt class="text-uppercase mb-0 col-sm-4 d-none">Portrait</dt>
                                                                <dd class="mb-0 col-sm-8 cla-portrait d-none">' . $contact["portrait"] . '</dd>';

                                                                if (isset($contact['prefix']) && $contact['prefix'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Prefix</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-prefix">' . $contact['prefix'] . '</dd>';
                                                                }
                                                                $this->content .=
                                                                    '<dt class="text-uppercase mb-0 col-sm-4">First Name</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-firstName">' . $contact['first_name'] . '</dd>
                                                                    <dt class="text-uppercase mb-0 col-sm-4">Last Name</dt>
                                                                    <dd class="mb-0 col-sm-8 cla-lastName">' . $contact['last_name'] . '</dd>';
                                                                if (isset($contact['suffix']) && $contact['suffix'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Suffix</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-suffix">' . $contact['suffix'] . '</dd>';
                                                                }
                                                                if (isset($contact['email']) && $contact['email'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Email</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-email">' . $contact['email'] . '</dd>';
                                                                }
                                                                if (isset($contact['secondary_email']) && $contact['secondary_email'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Secondary Email</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-secondaryEmail">' . $contact['secondary_email'] . '</dd>';
                                                                }
                                                                if (isset($contact['cc_emails_to_secondary_email']) && $contact['cc_emails_to_secondary_email'] !== '') {
                                                                    if ($contact['cc_emails_to_secondary_email'] == '0') {
                                                                        $contact['cc_emails_to_secondary_email'] = 'N';
                                                                    } else if ($contact['cc_emails_to_secondary_email'] == '1') {
                                                                        $contact['cc_emails_to_secondary_email'] = 'Y';
                                                                    }
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">CC Secondary Email</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-ccSecondaryEmail">' . $contact['cc_emails_to_secondary_email'] . '</dd>';
                                                                }
                                                                if (isset($contact['contact_phone']) && $contact['contact_phone'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Phone</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-phone">' . $contact['contact_phone'] . '</dd>';
                                                                }
                                                                if (isset($contact['contact_phone_ext']) && $contact['contact_phone_ext'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Extension</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-extension">' . $contact['contact_phone_ext'] . '</dd>';
                                                                }
                                                                if (isset($contact['contact_mobile']) && $contact['contact_mobile'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Mobile</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-mobile">' . $contact['contact_mobile'] . '</dd>';
                                                                }
                                                                if (isset($contact['contact_fax']) && $contact['contact_fax'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Fax</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-fax">' . $contact['contact_fax'] . '</dd>';
                                                                }
                                                                if (isset($contact['contact_other']) && $contact['contact_other'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Other Contact #</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-other">' . $contact['contact_other'] . '</dd>';
                                                                }
                                                                if (isset($contact['contact_notes']) && $contact['contact_notes'] !== '') {
                                                                    $this->content .=
                                                                        '<dt class="text-uppercase mb-0 col-sm-4">Contact Notes</dt>
                                                                        <dd class="mb-0 col-sm-8 cla-notes">' . $contact['contact_notes'] . '</dd>';
                                                                }

                                                                $this->content .=
                                                            '</dl>
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
                            var contactPostLink = "' . $this->contactsParams['contactPostLink'] . '";
                            var contactSortable = "' . $this->contactsParams['contactSortable'] . '";

                            dataCollectionSection["data"]["contact_ids"] = { }
                            dataCollectionSection["data"]["delete_contact_ids"] = [];

                            function initMainButtons() {
                                $("#' . $this->compSecId . '-cancel-contact").off();
                                $("#' . $this->compSecId . '-cancel-contact").click(function(e) {
                                    e.preventDefault();
                                    $(".contactEditButton, .contactDeleteButton, .contactCopyButton").attr("disabled", false);

                                    toggleContactFields(true);

                                    if ($("#' . $this->compSecId . '-portrait-croppie").length > 0) {
                                        $("#body").trigger("resetCroppie");
                                    }

                                    $("#' . $this->compSecId . '-contactes").trigger("contactCancel");
                                });
                                $("#' . $this->compSecId . '-add-contact, #' . $this->compSecId . '-update-contact").off();
                                $("#' . $this->compSecId . '-add-contact, #' . $this->compSecId . '-update-contact").attr("disabled", false);
                                $("#' . $this->compSecId . '-add-contact, #' . $this->compSecId . '-update-contact").click(function(e) {
                                    e.preventDefault();
                                    $(".contactEditButton, .contactDeleteButton, .contactCopyButton").attr("disabled", false);

                                    if ($(this)[0].id === "' . $this->compSecId . '-update-contact") {
                                        extractData(true, true);
                                        $("#' . $this->compSecId . '-contactes").trigger("contactUpdate");
                                    } else {
                                        extractData(false, true);
                                        $("#' . $this->compSecId . '-contactes").trigger("contactAdd");
                                    }
                                });
                            }

                            function toggleContactFields(status, update = false) {
                                var fields = ["contact_id","contact_reference","portrait","prefix","first_name","last_name","suffix","email","secondary_email","cc_emails_to_secondary_email","contact_phone","contact_phone_ext","contact_mobile","contact_other","contact_fax","contact_notes"];

                                if (status === true) {
                                    $(fields).each(function(index, field) {
                                        if ($("#' . $this->compSecId . '-" + field).length > 0) {
                                            if (field === "cc_emails_to_secondary_email") {
                                                $("#' . $this->compSecId . '-" + field)[0]["checked"] = false;
                                            } else {
                                                $("#' . $this->compSecId . '-" + field).val("");
                                            }
                                        }
                                    });
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

                                $(fields).each(function(index, field) {
                                    if ($("#' . $this->compSecId . '-" + field).length > 0) {
                                        $("#' . $this->compSecId . '-" + field).removeClass("is-invalid");
                                    }
                                });
                            }

                            function extractData(update = false, onclick = false) {
                                var fields = ["prefix","first_name","last_name","suffix","email","secondary_email","cc_emails_to_secondary_email","contact_phone","contact_phone_ext","contact_mobile","contact_other","contact_fax","contact_notes"];

                                var fieldError = false;
                                $(fields).each(function(index, field) {
                                    if ($("#' . $this->compSecId . '-" + field).siblings().find("[data-original-title=\'Required\']").length > 0 &&
                                        $("#' . $this->compSecId . '-" + field).val() === "") {
                                        $("#' . $this->compSecId . '-" + field).addClass("is-invalid");
                                        $("#' . $this->compSecId . '-" + field).focus(function() {
                                            $("#' . $this->compSecId . '-" + field).removeClass("is-invalid");
                                        });

                                        fieldError = true;
                                    }
                                });

                                if (fieldError) {
                                    return;
                                }

                                var data = { };
                                var contactId, contactNew;

                                data["contact_id"] = $("#' . $this->compSecId . '-contact_id").val();
                                var html =
                                    \'<dl class="row mb-0">\';
                                if ($("#' . $this->compSecId . '-contact_reference").length > 0) {
                                    data["contact_reference"] = $("#' . $this->compSecId . '-first_name").val().trim() + " " + $("#' . $this->compSecId . '-last_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4 d-none">Contact Reference</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-contactReference d-none">\' + data["contact_reference"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-portrait").length > 0) {
                                    data["portrait"] = $("#' . $this->compSecId . '-portrait").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4 d-none">Portrait</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-portrait d-none">\' + data["portrait"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-prefix").length > 0) {
                                    data["prefix"] = $("#' . $this->compSecId . '-prefix").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Prefix</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-prefix">\' + data["prefix"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-first_name").length > 0) {
                                    data["first_name"] = $("#' . $this->compSecId . '-first_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">First Name</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-firstName">\' + data["first_name"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-last_name").length > 0) {
                                    data["last_name"] = $("#' . $this->compSecId . '-last_name").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Last Name</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-lastName">\' + data["last_name"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-suffix").length > 0) {
                                    data["suffix"] = $("#' . $this->compSecId . '-suffix").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Suffix</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-suffix">\' + data["suffix"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-email").length > 0) {
                                    data["email"] = $("#' . $this->compSecId . '-email").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Email</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-email">\' + data["email"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-secondary_email").length > 0) {
                                    data["secondary_email"] = $("#' . $this->compSecId . '-secondary_email").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Secondary Email</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-secondaryEmail">\' + data["secondary_email"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-cc_emails_to_secondary_email").length > 0) {
                                    data["cc_emails_to_secondary_email"] = $("#' . $this->compSecId . '-cc_emails_to_secondary_email")[0].checked;
                                    var ccEmailsToSecondaryEmail = "N";
                                    if (data["cc_emails_to_secondary_email"]) {
                                        ccEmailsToSecondaryEmail = "Y";
                                    }
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">CC Secondary Email</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-ccSecondaryEmail">\' + ccEmailsToSecondaryEmail + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-contact_phone").length > 0) {
                                    data["contact_phone"] = $("#' . $this->compSecId . '-contact_phone").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Phone</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-phone">\' + data["contact_phone"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-contact_phone_ext").length > 0) {
                                    data["contact_phone_ext"] = $("#' . $this->compSecId . '-contact_phone_ext").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Extension</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-extension">\' + data["contact_phone_ext"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-contact_mobile").length > 0) {
                                    data["contact_mobile"] = $("#' . $this->compSecId . '-contact_mobile").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Mobile</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-mobile">\' + data["contact_mobile"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-contact_fax").length > 0) {
                                    data["contact_fax"] = $("#' . $this->compSecId . '-contact_fax").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Fax</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-fax">\' + data["contact_fax"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-contact_other").length > 0) {
                                    data["contact_other"] = $("#' . $this->compSecId . '-contact_other").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Other Contact #</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-other">\' + data["contact_other"] + \'</dd>\';
                                }
                                if ($("#' . $this->compSecId . '-contact_notes").length > 0) {
                                    data["contact_notes"] = $("#' . $this->compSecId . '-contact_notes").val().trim();
                                    html +=
                                        \'<dt class="text-uppercase mb-0 col-sm-4">Contact Notes</dt>\' +
                                        \'<dd class="mb-0 col-sm-8 cla-notes">\' + data["contact_notes"] + \'</dd>\';
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
                                            \'" data-new="\' + contactNew + \'" data-contact-id="\' + contactId + \'" data-contact-seq="\' + contactId + \'">\' +
                                            \'<div class="row">\' +
                                                \'<div class="col">\' +
                                                    \'<i class="fa fa-sort fa-fw handle"></i>\' +
                                                \'</div>\' +
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
                                            \'</div>\';

                                            if ($("#' . $this->compSecId . '-portrait").length > 0) {
                                                var contactPortraitLink;

                                                data["portrait"] = $("#' . $this->compSecId . '-portrait").val().trim();
                                                list +=
                                                    \'<div class="text-center image-content ' . $this->compSecId . '-portrait-image-content">\';

                                                if (data["portrait"] === "") {
                                                    list +=
                                                    \'<img id="' . $this->compSecId . '-portrait-croppie-image-\' + contactId + \'" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="' . $this->links->images('general/portrait.png') . '" class="user-image img-fluid img-thumbnail" style="max-width:80px;max-height:80px;">\' +
                                                    \'<div class="image-text-portrait d-none">' . $this->links->images('general/portrait.png') . '</div>\';
                                                } else {
                                                    contactPortraitLink = \'' . $this->links->url('system/storages/q/uuid/\' + data["portrait"] + \'/w/80\'') . ';
                                                    list +=
                                                    \'<img id="' . $this->compSecId . '-portrait-croppie-image-\' + contactId + \'" alt="portrait" data-type="portrait" data-orgimage="' . $this->links->images('general/portrait.png') . '" src="\' + contactPortraitLink + \'" class="user-image img-fluid img-thumbnail" style="max-width:80px;max-height:80px;">\' +
                                                    \'<div class="image-text-portrait d-none">\' + data["portrait"] + \'</div>\';
                                                }
                                                list +=
                                                    \'</div>\';
                                            }

                                        list +=
                                            \'<div class="row">\' +
                                                \'<div class="col list-group-item-data">\' +
                                                    html +
                                                \'</div>\' +
                                            \'</div>\' +
                                        \'</li>\';

                                    if (update === false && contactsLi.length > 0) {
                                        var exists = false;

                                        $(contactsLi).each(function(index, li) {
                                            if ($(li).find(".cla-firstName").text() === data["first_name"] &&
                                                $(li).find(".cla-lastName").text() === data["last_name"]
                                            ) {
                                                PNotify.error({"title" : "Contact with same name already added!"});
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

                                        if ($("#' . $this->compSecId . '-portrait-croppie").length > 0 && contactPortraitLink) {
                                            $("#' . $this->compSecId . '-portrait-croppie-image-" + contactId).attr("src", contactPortraitLink);

                                            $("#body").trigger("saveCroppie");
                                        }
                                    } else {
                                        $("#' . $this->compSecId . '-sortable-contacts-list").append(list);
                                        $("#' . $this->compSecId . '-contacts-list-nodata").attr("hidden", true);
                                    }
                                }

                                collectData(onclick);
                                registerContactButtons();
                                toggleContactFields(true);
                            }

                            if (contactSortable) {
                                function initSortable(element) {
                                    var el = document.getElementById(element);
                                    dataCollectionSection["' . $this->compSecId . '-form"]["sortable"] = { };
                                    dataCollectionSection["' . $this->compSecId . '-form"]["sortable"] = Sortable.create(el, {
                                        dataIdAttr : "data-contact-seq",
                                        onEnd: function(e) {
                                            collectData(true);
                                        }
                                    });
                                }
                            }

                            function collectData(onclick = false) {
                                $("#' . $this->compSecId . '-sortable-contacts-list li").each(function(index, id) {
                                    var data = { };
                                    var contactId;

                                    contactId = $(this).data("contact-id");
                                    data["new"] = $(this).data("new");
                                    data["seq"] = index;

                                    $(id).find("dd").each(function(index,dd) {
                                        if ($(dd).is(".cla-portrait")) {
                                            data["portrait"] = $(dd).html();
                                        } else if ($(dd).is(".cla-prefix")) {
                                            data["prefix"] = $(dd).html();
                                        } else if ($(dd).is(".cla-firstName")) {
                                            data["first_name"] = $(dd).html();
                                        } else if ($(dd).is(".cla-lastName")) {
                                            data["last_name"] = $(dd).html();
                                        } else if ($(dd).is(".cla-suffix")) {
                                            data["suffix"] = $(dd).html();
                                        } else if ($(dd).is(".cla-email")) {
                                            data["email"] = $(dd).html();
                                        } else if ($(dd).is(".cla-secondaryEmail")) {
                                            data["secondary_email"] = $(dd).html();
                                        } else if ($(dd).is(".cla-ccSecondaryEmail")) {
                                            data["cc_emails_to_secondary_email"] = false;
                                            if ($(dd).html().toLowerCase() === "y") {
                                                data["cc_emails_to_secondary_email"] = true;
                                            }
                                        } else if ($(dd).is(".cla-phone")) {
                                            data["contact_phone"] = $(dd).html();
                                        } else if ($(dd).is(".cla-extension")) {
                                            data["contact_phone_ext"] = $(dd).html();
                                        } else if ($(dd).is(".cla-mobile")) {
                                            data["contact_mobile"] = $(dd).html();
                                        } else if ($(dd).is(".cla-fax")) {
                                            data["contact_fax"] = $(dd).html();
                                        } else if ($(dd).is(".cla-other")) {
                                            data["contact_other"] = $(dd).html();
                                        } else if ($(dd).is(".cla-notes")) {
                                            data["contact_notes"] = $(dd).html();
                                        }
                                    });

                                    dataCollectionSection["data"]["contact_ids"][contactId] = data;
                                });

                                if (onclick && contactPostLink !== "") {
                                    postData();
                                }

                                if ($("#' . $this->compSecId . '-portrait-croppie").length > 0) {
                                    $("#body").trigger("resetCroppie");
                                }
                            }

                            function registerContactButtons() {
                                $(".contactEditButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        editCopy("edit", this);
                                    });
                                });

                                $(".contactCopyButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        editCopy("copy", this);
                                    });
                                });

                                $(".contactDeleteButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {
                                        Swal.fire({
                                            title                       : \'<span class="text-danger"> Delete contact?</span>\',
                                            icon                        : "question",
                                            background                  : "rgba(0,0,0,.8)",
                                            backdrop                    : "rgba(0,0,0,.6)",
                                            buttonsStyling              : false,
                                            confirmButtonText           : "Delete",
                                            customClass                 : {
                                                "confirmButton"             : "btn btn-danger btn-sm text-uppercase",
                                                "cancelButton"              : "ml-2 btn btn-secondary btn-sm text-uppercase",
                                            },
                                            showCancelButton            : true,
                                            keydownListenerCapture      : true,
                                            allowOutsideClick           : true,
                                            allowEscapeKey              : true,
                                            didOpen                     : function() {
                                                dataCollection.env.sounds.swalSound.play();
                                            }
                                        }).then((result) => {
                                            if (result.value) {
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

                                                collectData(true);
                                            } else {
                                                return;
                                            }
                                        });
                                    });
                                });
                            }

                            function editCopy(task, button) {
                                $(button).attr("disabled", true);
                                $(button).siblings(".contactDeleteButton").attr("disabled", true);

                                var portrait = "";

                                $($(button).parents("li").children(".row")[1]).find("dd").each(function(index,dd) {
                                    if ($(dd).is(".cla-contactReference")) {
                                        $("#' . $this->compSecId . '-contact_reference").val($(dd).html());
                                    } else if ($(dd).is(".cla-portrait")) {
                                        $("#' . $this->compSecId . '-portrait").val($(dd).html());
                                        portrait = $(dd).html();
                                    } else if ($(dd).is(".cla-prefix")) {
                                        $("#' . $this->compSecId . '-prefix").val($(dd).html());
                                    } else if ($(dd).is(".cla-firstName")) {
                                        $("#' . $this->compSecId . '-first_name").val($(dd).html());
                                    } else if ($(dd).is(".cla-lastName")) {
                                        $("#' . $this->compSecId . '-last_name").val($(dd).html());
                                    } else if ($(dd).is(".cla-suffix")) {
                                        $("#' . $this->compSecId . '-suffix").val($(dd).html());
                                    } else if ($(dd).is(".cla-email")) {
                                        $("#' . $this->compSecId . '-email").val($(dd).html());
                                    } else if ($(dd).is(".cla-secondaryEmail")) {
                                        $("#' . $this->compSecId . '-secondary_email").val($(dd).html());
                                    } else if ($(dd).is(".cla-ccSecondaryEmail")) {
                                        var ccEmailsToSecondaryEmail = false;
                                        if ($(dd).html().toLowerCase() === "y") {
                                            ccEmailsToSecondaryEmail = true;
                                        }
                                        $("#' . $this->compSecId . '-cc_emails_to_secondary_email")[0].checked = ccEmailsToSecondaryEmail;
                                    } else if ($(dd).is(".cla-phone")) {
                                        $("#' . $this->compSecId . '-contact_phone").val($(dd).html());
                                    } else if ($(dd).is(".cla-extension")) {
                                        $("#' . $this->compSecId . '-contact_phone_ext").val($(dd).html());
                                    } else if ($(dd).is(".cla-mobile")) {
                                        $("#' . $this->compSecId . '-contact_mobile").val($(dd).html());
                                    } else if ($(dd).is(".cla-fax")) {
                                        $("#' . $this->compSecId . '-contact_fax").val($(dd).html());
                                    } else if ($(dd).is(".cla-other")) {
                                        $("#' . $this->compSecId . '-contact_other").val($(dd).html());
                                    } else if ($(dd).is(".cla-notes")) {
                                        $("#' . $this->compSecId . '-contact_notes").val($(dd).html());
                                    }

                                    if (task === "edit") {
                                        $("#' . $this->compSecId . '-contact_id").val($(dd).parents("li").data("contact-id"));
                                    } else if (task === "copy") {
                                        $("#' . $this->compSecId . '-contact_id").val("");
                                    }
                                });

                                if (task === "edit") {
                                    toggleContactFields(false, true);
                                    $(button).siblings(".contactCopyButton").attr("disabled", true);
                                } else if (task === "copy") {
                                    toggleContactFields(false, false);
                                    $(button).siblings(".contactEditButton").attr("disabled", true);
                                }

                                if ($("#' . $this->compSecId . '-portrait-croppie").length > 0 && portrait !== "") {
                                    portrait = \'' . $this->links->url('system/storages/q/uuid/\' + portrait + \'/w/200\'') . ';
                                    $("#' . $this->compSecId . '-portrait-croppie-image").attr("src", portrait);
                                    $("#' . $this->compSecId . '-portrait-croppie-image").attr("hidden", false);
                                    $("#' . $this->compSecId . '-portrait-croppie").attr("hidden", true);

                                    $("#body").trigger("saveCroppie");
                                }
                            }

                            if (contactSortable) {
                                initSortable($("#' . $this->compSecId . '-sortable-contacts-list")[0].id);
                            }
                            initMainButtons();
                            if (contactPostLink === "") {
                                collectData();
                            }
                            registerContactButtons();

                            function postData() {
                                var postData = { };
                                postData[$("#security-token").attr("name")] = $("#security-token").val();
                                postData["package_class"] = "' . $this->contactsParams['contactPackageClass'] . '";
                                postData["package_row_id"] = "' . $this->contactsParams['contactPackageRowId'] . '";
                                postData["contact_ids"] = dataCollectionSection["data"]["contact_ids"];
                                postData["delete_contact_ids"] = dataCollectionSection["data"]["delete_contact_ids"];

                                $.post(contactPostLink, postData, function(response) {
                                    if (response.responseCode == 1) {
                                        paginatedPNotify("error", {title: response.responseMessage});
                                        return;
                                    }

                                    if (response.responseCode == 0) {
                                        dataCollectionSection["data"]["contact_ids"] = response.responseData.contacts;
                                        dataCollectionSection["data"]["delete_contact_ids"] = [];

                                        $(dataCollectionSection["data"]["contact_ids"]).each(function(index, contactArr) {
                                            for (var contactId in contactArr) {
                                                var contactLi = Array.from($(\'.cla-contactReference\')).find(item => item.textContent.trim() === contactArr[contactId]["full_name"]);

                                                $(contactLi).parents("li").data("new", 0);
                                                $(contactLi).parents("li").data("contact-id", contactId);
                                                $(contactLi).parents("li").data("contact-seq", contactArr[contactId]["seq"]);
                                                $(contactLi).parents("li").attr("data-new", 0);
                                                $(contactLi).parents("li").attr("data-contact-id", contactId);
                                                $(contactLi).parents("li").attr("data-contact-seq", contactArr[contactId]["seq"]);
                                            }
                                        });

                                        paginatedPNotify("success", {title: response.responseMessage});
                                        return;
                                    }

                                    if (response.tokenKey && response.token) {
                                        $("#security-token").attr("name", response.tokenKey);
                                        $("#security-token").val(response.token);
                                    }
                                }, "json");
                            }
                        }
                    }
                });
            </script>';

        return $inclJs;
    }
}