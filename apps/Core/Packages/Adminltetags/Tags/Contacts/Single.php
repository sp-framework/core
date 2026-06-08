<?php

namespace Apps\Core\Packages\Adminltetags\Tags\Contacts;

use Apps\Core\Packages\Adminltetags\Adminltetags;

class Single
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

    public function __construct($view, $tag, $links, $escaper, $params, $contactsParams = [])
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

        $this->buildSingleContactLayout();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildSingleContactData()
    {
        $fieldsArr = ['portrait','prefix','first_name','last_name','suffix','email','secondary_email','contact_phone','contact_phone_ext','contact_mobile','contact_other','contact_fax','contact_notes'];
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

        $fieldsArr = ['multiple','includePortrait','includeNamePrefix','includeName','includeNameSuffix','includeEmail','includeSecondaryEmail','includeOther','includeNotes','firstNameFieldHidden','firstNameFieldDisabled','firstNameFieldRequired','firstNameFieldBazPostOnCreate','firstNameFieldBazPostOnUpdate','lastNameFieldHidden','lastNameFieldDisabled','lastNameFieldRequired','lastNameFieldBazPostOnCreate','lastNameFieldBazPostOnUpdate','emailFieldHidden','emailFieldDisabled','emailFieldRequired','emailFieldBazPostOnCreate','emailFieldBazPostOnUpdate','secondaryEmailFieldHidden','secondaryEmailFieldDisabled','secondaryEmailFieldRequired','secondaryEmailFieldBazPostOnCreate','secondaryEmailFieldBazPostOnUpdate','ccEmailsToSecondaryEmailFieldHidden','ccEmailsToSecondaryEmailFieldDisabled','ccEmailsToSecondaryEmailFieldRequired','ccEmailsToSecondaryEmailFieldBazPostOnCreate','ccEmailsToSecondaryEmailFieldBazPostOnUpdate','contactPhoneFieldHidden','contactPhoneFieldDisabled','contactPhoneFieldRequired','contactPhoneFieldBazPostOnCreate','contactPhoneFieldBazPostOnUpdate','contactPhoneExtFieldHidden','contactPhoneExtFieldDisabled','contactPhoneExtFieldRequired','contactPhoneExtFieldBazPostOnCreate','contactPhoneExtFieldBazPostOnUpdate','contactMobileFieldHidden','contactMobileFieldDisabled','contactMobileFieldRequired','contactMobileFieldBazPostOnCreate','contactMobileFieldBazPostOnUpdate','contactFaxFieldHidden','contactFaxFieldDisabled','contactFaxFieldRequired','contactFaxFieldBazPostOnCreate','contactFaxFieldBazPostOnUpdate','contactOtherFieldHidden','contactOtherFieldDisabled','contactOtherFieldRequired','contactOtherFieldBazPostOnCreate','contactOtherFieldBazPostOnUpdate','contactNotesFieldHidden','contactNotesFieldDisabled','contactNotesFieldRequired','contactNotesFieldBazPostOnCreate','contactNotesFieldBazPostOnUpdate'];

        foreach ($fieldsArr as $field) {
            $this->contactsParams[$field] =
                isset($this->params[$field]) &&
                    $this->params[$field] === true ?
                true :
                false;
        }

        //Note: for profile updating contact fields require OnCreate Permissions as there is no profile ID sent via POST.
    }

    protected function buildSingleContactLayout()
    {
        $this->content .=
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                             => $this->params['component'],
                            'componentName'                         => $this->params['componentName'],
                            'componentId'                           => $this->params['componentId'],
                            'sectionId'                             => $this->params['sectionId'],
                            'fieldId'                               => 'contact_reference',
                            'fieldLabel'                            => 'Contact Reference',
                            'fieldType'                             => 'input',
                            'fieldHelp'                             => true,
                            'fieldHelpTooltipContent'               => 'Placeholder for js.',
                            'fieldHidden'                           => true,
                            'fieldDisabled'                         => true,
                            'fieldRequired'                         => false,
                            'fieldBazScan'                          => true,
                            'fieldBazPostOnCreate'                  => false,
                            'fieldBazPostOnUpdate'                  => false,
                            'fieldDataInputMinLength'               => 1,
                            'fieldDataInputMaxLength'               => 100,
                            'fieldValue'                            => ''
                        ]
                    ) .
                '</div>
            </div>';

        if (isset($this->contactsParams['includePortrait']) && $this->contactsParams['includePortrait'] === true) {
            if ($this->contactsParams['multiple']) {
                $this->content .= '<div class="col">';
                $this->content .= '<div class="row">';
                $this->content .= '<div class="col">';
                $this->content .= $this->inclPortrait();
                $this->content .= '</div>';
                $this->content .= '</div>';
                $this->content .= '<hr>';
            } else {
                $this->content .= '<div class="row vdivide">';
                $this->content .= '<div class="col-md-3 col-sm-12">';
                $this->content .= $this->inclPortrait();
                $this->content .= '</div>';
            }
        } else {
            if ($this->contactsParams['multiple']) {
                $this->content .= '<div class="col">';
            }
        }

        $this->content .= '<div class="col">';
        if (isset($this->contactsParams['includeName']) && $this->contactsParams['includeName'] === true) {
            $this->content .= $this->inclName();
        }

        if (isset($this->contactsParams['includeEmail']) && $this->contactsParams['includeEmail'] === true) {
            $this->content .= $this->inclEmail();
        }

        $this->content .= $this->inclPhone();

        if (isset($this->contactsParams['includeOther']) && $this->contactsParams['includeOther'] === true) {
            $this->content .= $this->inclOther();
        }

        if (isset($this->contactsParams['includeNotes']) && $this->contactsParams['includeNotes'] === true) {
            $this->content .= $this->inclNotes();
        }

        if ($this->contactsParams['multiple']) {
            $this->content .= '</div></div>';
        } else {
            $this->content .= '</div>';
        }

        $this->content .= $this->inclBaseJs();

        if (isset($this->contactsParams['includeName']) && $this->contactsParams['includeName'] === true) {
            $this->content .= $this->inclNameJs();
        }

        if (isset($this->contactsParams['includeEmail']) && $this->contactsParams['includeEmail'] === true) {
            $this->content .= $this->inclEmailJs();
        }

        $this->content .= $this->inclPhoneJs();

        if (isset($this->contactsParams['includeOther']) && $this->contactsParams['includeOther'] === true) {
            $this->content .= $this->inclOtherJs();
        }

        if (isset($this->contactsParams['includeNotes']) && $this->contactsParams['includeNotes'] === true) {
            $this->content .= $this->inclNotesJs();
        }

        $this->content .=
            '});</script>';
    }

    protected function inclPortrait()
    {
        $portrait = '';

        if (!isset($this->params['maxHeight']) ||
            isset($this->params['maxHeight']) && $this->params['maxHeight'] == 0
        ) {
            $this->contactsParams['maxHeight'] = 200;
        }

        if (!isset($this->params['maxWidth']) ||
            isset($this->params['maxWidth']) && $this->params['maxWidth'] == 0
        ) {
            $this->contactsParams['maxWidth'] = 200;
        }

        if (isset($this->contactsParams['portrait']) && $this->contactsParams['portrait'] !== '') {
            $portraitLink = $this->links->url('system/storages/q/uuid/' . $this->contactsParams['portrait'] . '/w/' . $this->contactsParams['maxWidth']);
        } else {
            $portraitLink = '';
        }
        if (isset($this->contactsParams['initials_avatar']['large']) && $this->contactsParams['initials_avatar']['large'] !== '') {
            $initialsAvatar = $this->contactsParams['initials_avatar'];
        } else {
            $initialsAvatar = '';
        }

        $portrait .=
            $this->adminLTETags->useTag('fields',
                [
                    'component'                      => $this->params['component'],
                    'componentName'                  => $this->params['componentName'],
                    'componentId'                    => $this->params['componentId'],
                    'sectionId'                      => $this->params['sectionId'],
                    'fieldId'                        => 'portrait',
                    'fieldLabel'                     => false,
                    'fieldType'                      => 'files/croppie',
                    'fieldValue'                     => $this->contactsParams['portrait'],
                    'imageType'                      => 'portrait',
                    'initialsAvatar'                 => $initialsAvatar,
                    'storageType'                    => 'private',
                    'upload'                         => true,
                    'avatar'                         => true,
                    'remove'                         => true,
                    'recover'                        => true,
                    'portraitLink'                   => $portraitLink,
                    'maxHeight'                      => $this->contactsParams['maxHeight'],
                    'maxWidth'                       => $this->contactsParams['maxWidth']
                ]
            );

        return $portrait;
    }

    protected function inclName()
    {
        $name =
            '<div class="row">';

        if ($this->contactsParams['includeNamePrefix']) {
            $name .=
                '<div class="col-md-2">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                      => $this->params['component'],
                            'componentName'                  => $this->params['componentName'],
                            'componentId'                    => $this->params['componentId'],
                            'sectionId'                      => $this->params['sectionId'],
                            'fieldId'                        => 'prefix',
                            'fieldLabel'                     => 'Prefix',
                            'fieldType'                      => 'input',
                            'fieldHelp'                      => true,
                            'fieldHelpTooltipContent'        => 'Prefix',
                            'fieldBazScan'                   => true,
                            'fieldBazJstreeSearch'           =>
                                (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                            'fieldBazPostOnCreate'           => $this->contactsParams['firstNameFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'           => $this->contactsParams['firstNameFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'        => 1,
                            'fieldDataInputMaxLength'        => 100,
                            'fieldValue'                     => $this->contactsParams['prefix']
                        ]
                    ) .
                '</div>';
        }

        $name .=
            '<div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                      => $this->params['component'],
                        'componentName'                  => $this->params['componentName'],
                        'componentId'                    => $this->params['componentId'],
                        'sectionId'                      => $this->params['sectionId'],
                        'fieldId'                        => 'first_name',
                        'fieldLabel'                     => 'First Name',
                        'fieldType'                      => 'input',
                        'fieldHelp'                      => true,
                        'fieldHelpTooltipContent'        => 'First Name',
                        'fieldHidden'                    => $this->contactsParams['firstNameFieldHidden'],
                        'fieldDisabled'                  => $this->contactsParams['firstNameFieldDisabled'],
                        'fieldRequired'                  => $this->contactsParams['firstNameFieldRequired'],
                        'fieldBazScan'                   => true,
                        'fieldBazJstreeSearch'           =>
                            (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                        'fieldBazPostOnCreate'           => $this->contactsParams['firstNameFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'           => $this->contactsParams['firstNameFieldBazPostOnUpdate'],
                        'fieldDataInputMinLength'        => 1,
                        'fieldDataInputMaxLength'        => 100,
                        'fieldValue'                     => $this->contactsParams['first_name']
                    ]
                ) .
            '</div>
            <div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                      => $this->params['component'],
                        'componentName'                  => $this->params['componentName'],
                        'componentId'                    => $this->params['componentId'],
                        'sectionId'                      => $this->params['sectionId'],
                        'fieldId'                        => 'last_name',
                        'fieldLabel'                     => 'Last Name',
                        'fieldType'                      => 'input',
                        'fieldHelp'                      => true,
                        'fieldHelpTooltipContent'        => 'Last Name',
                        'fieldHidden'                    => $this->contactsParams['lastNameFieldHidden'],
                        'fieldDisabled'                  => $this->contactsParams['lastNameFieldDisabled'],
                        'fieldRequired'                  => $this->contactsParams['lastNameFieldRequired'],
                        'fieldBazScan'                   => true,
                        'fieldBazJstreeSearch'           =>
                            (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                        'fieldBazPostOnCreate'           => $this->contactsParams['lastNameFieldBazPostOnCreate'],
                        'fieldBazPostOnUpdate'           => $this->contactsParams['lastNameFieldBazPostOnUpdate'],
                        'fieldDataInputMinLength'        => 1,
                        'fieldDataInputMaxLength'        => 100,
                        'fieldValue'                     => $this->contactsParams['last_name']
                    ]
                ) .
            '</div>';

        if ($this->contactsParams['includeNameSuffix']) {
            $name .=
                '<div class="col-md-2">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                      => $this->params['component'],
                            'componentName'                  => $this->params['componentName'],
                            'componentId'                    => $this->params['componentId'],
                            'sectionId'                      => $this->params['sectionId'],
                            'fieldId'                        => 'suffix',
                            'fieldLabel'                     => 'Suffix',
                            'fieldType'                      => 'input',
                            'fieldHelp'                      => true,
                            'fieldHelpTooltipContent'        => 'Suffix',
                            'fieldBazScan'                   => true,
                            'fieldBazJstreeSearch'           =>
                                (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                            'fieldBazPostOnCreate'           => $this->contactsParams['firstNameFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'           => $this->contactsParams['firstNameFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'        => 1,
                            'fieldDataInputMaxLength'        => 100,
                            'fieldValue'                     => $this->contactsParams['suffix']
                        ]
                    ) .
                '</div>';
        }

        $name .=
            '</div>';

        return $name;
    }

    protected function inclEmail()
    {
        $ccEmailsChecked = false;
        if (isset($this->params['contacts']['cc_emails_to_secondary_email'])) {
            if ($this->params['contacts']['cc_emails_to_secondary_email'] == 1) {
                $ccEmailsChecked = true;
            }
        }

        $email =
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                      => $this->params['component'],
                            'componentName'                  => $this->params['componentName'],
                            'componentId'                    => $this->params['componentId'],
                            'sectionId'                      => $this->params['sectionId'],
                            'fieldId'                        => 'email',
                            'fieldLabel'                     => 'Email',
                            'fieldType'                      => 'input',
                            'fieldHelp'                      => true,
                            'fieldHelpTooltipContent'        => 'Email',
                            'fieldHidden'                    => $this->contactsParams['emailFieldHidden'],
                            'fieldDisabled'                  => $this->contactsParams['emailFieldDisabled'],
                            'fieldRequired'                  => $this->contactsParams['emailFieldRequired'],
                            'fieldBazScan'                   => true,
                            'fieldBazJstreeSearch'           =>
                                (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                            'fieldBazPostOnCreate'           => $this->contactsParams['emailFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'           => $this->contactsParams['emailFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'        => 1,
                            'fieldDataInputMaxLength'        => 100,
                            'fieldValue'                     => $this->contactsParams['email']
                        ]
                    ) .
                '</div>';
        if (isset($this->contactsParams['includeSecondaEmail']) && $this->contactsParams['includeSecondaEmail'] === true) {
            $email .=
                '<div class="col">
                    <div class="row">
                        <div class="col-md-10">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'secondary_email',
                                    'fieldLabel'                     => $this->contactsParams['secondaryEmailFieldLabel'],
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => $this->contactsParams['secondaryEmailFieldLabel'],
                                    'fieldHidden'                    => $this->contactsParams['secondaryEmailFieldHidden'],
                                    'fieldDisabled'                  => $this->contactsParams['secondaryEmailFieldDisabled'],
                                    'fieldRequired'                  => $this->contactsParams['secondaryEmailFieldRequired'],
                                    'fieldBazScan'                   => true,
                                    'fieldBazJstreeSearch'           =>
                                        (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                                    'fieldBazPostOnCreate'           => $this->contactsParams['secondaryEmailFieldBazPostOnCreate'],
                                    'fieldBazPostOnUpdate'           => $this->contactsParams['secondaryEmailFieldBazPostOnUpdate'],
                                    'fieldDataInputMinLength'        => 1,
                                    'fieldDataInputMaxLength'        => 100,
                                    'fieldValue'                     => $this->contactsParams['secondary_email']
                                ]
                            ) .
                        '</div>
                        <div class="col-md-2">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'cc_emails_to_secondary_email',
                                    'fieldLabel'                     => 'CC?',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'CC Emails to secondary email address?',
                                    'fieldHidden'                    => $this->contactsParams['ccEmailsToSecondaryEmailFieldHidden'],
                                    'fieldDisabled'                  => $this->contactsParams['ccEmailsToSecondaryEmailFieldDisabled'],
                                    'fieldRequired'                  => $this->contactsParams['ccEmailsToSecondaryEmailFieldRequired'],
                                    'fieldType'                      => 'checkbox',
                                    'fieldCheckboxType'              => 'info',
                                    'fieldCheckboxChecked'           => $ccEmailsChecked,
                                    'fieldBazScan'                   => true,
                                    'fieldBazJstreeSearch'           =>
                                        (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                                    'fieldBazPostOnCreate'           => $this->contactsParams['ccEmailsToSecondaryEmailFieldBazPostOnCreate'],
                                    'fieldBazPostOnUpdate'           => $this->contactsParams['ccEmailsToSecondaryEmailFieldBazPostOnUpdate'],
                                ]
                            ) .
                        '</div>
                    </div>
                </div>
            </div>';

        } else {
            $email .= '</div>';
        }

        return $email;
    }

    protected function inclPhone()
    {
        return
            '<div class="row">
                <div class="col">
                    <div class="row">
                        <div class="col-md-8">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'contact_phone',
                                    'fieldLabel'                     => 'Phone',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Phone',
                                    'fieldHidden'                    => $this->contactsParams['contactPhoneFieldHidden'],
                                    'fieldDisabled'                  => $this->contactsParams['contactPhoneFieldDisabled'],
                                    'fieldRequired'                  => $this->contactsParams['contactPhoneFieldRequired'],
                                    'fieldBazScan'                   => true,
                                    'fieldBazJstreeSearch'           =>
                                        (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                                    'fieldBazPostOnCreate'           => $this->contactsParams['contactPhoneFieldBazPostOnCreate'],
                                    'fieldBazPostOnUpdate'           => $this->contactsParams['contactPhoneFieldBazPostOnUpdate'],
                                    'fieldDataInputMinLength'        => 1,
                                    'fieldDataInputMaxLength'        => 15,
                                    'fieldValue'                     => $this->contactsParams['contact_phone']
                                ]
                            ) .
                        '</div>
                        <div class="col-md-4">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'contact_phone_ext',
                                    'fieldLabel'                     => 'Extension',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHidden'                    => $this->contactsParams['contactPhoneExtFieldHidden'],
                                    'fieldDisabled'                  => $this->contactsParams['contactPhoneExtFieldDisabled'],
                                    'fieldRequired'                  => $this->contactsParams['contactPhoneExtFieldRequired'],
                                    'fieldBazScan'                   => true,
                                    'fieldBazJstreeSearch'           =>
                                        (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                                    'fieldBazPostOnCreate'           => $this->contactsParams['contactPhoneExtFieldBazPostOnCreate'],
                                    'fieldBazPostOnUpdate'           => $this->contactsParams['contactPhoneExtFieldBazPostOnUpdate'],
                                    'fieldDataInputMinLength'        => 1,
                                    'fieldDataInputMaxLength'        => 10,
                                    'fieldValue'                     => $this->contactsParams['contact_phone_ext']
                                ]
                            ) .
                        '</div>
                    </div>
                </div>
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                      => $this->params['component'],
                            'componentName'                  => $this->params['componentName'],
                            'componentId'                    => $this->params['componentId'],
                            'sectionId'                      => $this->params['sectionId'],
                            'fieldId'                        => 'contact_mobile',
                            'fieldLabel'                     => 'Mobile',
                            'fieldType'                      => 'input',
                            'fieldHelp'                      => true,
                            'fieldHelpTooltipContent'        => 'Mobile',
                            'fieldHidden'                    => $this->contactsParams['contactMobileFieldHidden'],
                            'fieldDisabled'                  => $this->contactsParams['contactMobileFieldDisabled'],
                            'fieldRequired'                  => $this->contactsParams['contactMobileFieldRequired'],
                            'fieldBazScan'                   => true,
                            'fieldBazJstreeSearch'           =>
                                (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                            'fieldBazPostOnCreate'           => $this->contactsParams['contactMobileFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'           => $this->contactsParams['contactMobileFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'        => 1,
                            'fieldDataInputMaxLength'        => 15,
                            'fieldValue'                     => $this->contactsParams['contact_mobile']
                        ]
                    ) .
                '</div>
            </div>';
    }

    protected function inclOther()
    {
        return
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                      => $this->params['component'],
                            'componentName'                  => $this->params['componentName'],
                            'componentId'                    => $this->params['componentId'],
                            'sectionId'                      => $this->params['sectionId'],
                            'fieldId'                        => 'contact_fax',
                            'fieldLabel'                     => $this->contactsParams['contactFaxFieldLabel'],
                            'fieldType'                      => 'input',
                            'fieldHelp'                      => true,
                            'fieldHelpTooltipContent'        => $this->contactsParams['contactFaxFieldLabel'],
                            'fieldHidden'                    => $this->contactsParams['contactFaxFieldHidden'],
                            'fieldDisabled'                  => $this->contactsParams['contactFaxFieldDisabled'],
                            'fieldRequired'                  => $this->contactsParams['contactFaxFieldRequired'],
                            'fieldBazScan'                   => true,
                            'fieldBazJstreeSearch'           =>
                                (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                            'fieldBazPostOnCreate'           => $this->contactsParams['contactFaxFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'           => $this->contactsParams['contactFaxFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'        => 1,
                            'fieldDataInputMaxLength'        => 15,
                            'fieldValue'                     => $this->contactsParams['contact_fax']
                        ]
                    ) .
                '</div>
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                      => $this->params['component'],
                            'componentName'                  => $this->params['componentName'],
                            'componentId'                    => $this->params['componentId'],
                            'sectionId'                      => $this->params['sectionId'],
                            'fieldId'                        => 'contact_other',
                            'fieldLabel'                     => $this->contactsParams['contactOtherFieldLabel'],
                            'fieldType'                      => 'input',
                            'fieldHelp'                      => true,
                            'fieldHelpTooltipContent'        => $this->contactsParams['contactOtherFieldLabel'],
                            'fieldHidden'                    => $this->contactsParams['contactOtherFieldHidden'],
                            'fieldDisabled'                  => $this->contactsParams['contactOtherFieldDisabled'],
                            'fieldRequired'                  => $this->contactsParams['contactOtherFieldRequired'],
                            'fieldBazScan'                   => true,
                            'fieldBazJstreeSearch'           =>
                                (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                            'fieldBazPostOnCreate'           => $this->contactsParams['contactOtherFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'           => $this->contactsParams['contactOtherFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'        => 1,
                            'fieldDataInputMaxLength'        => 15,
                            'fieldValue'                     => $this->contactsParams['contact_other']
                        ]
                    ) .
                '</div>
            </div>';
    }

    protected function inclNotes()
    {
        return
            '<div class="row">
                <div class="col">' .
                    $this->adminLTETags->useTag('fields',
                        [
                            'component'                      => $this->params['component'],
                            'componentName'                  => $this->params['componentName'],
                            'componentId'                    => $this->params['componentId'],
                            'sectionId'                      => $this->params['sectionId'],
                            'fieldId'                        => 'contact_notes',
                            'fieldLabel'                     => $this->contactsParams['contactNotesFieldLabel'],
                            'fieldType'                      => 'textarea',
                            'fieldHelp'                      => true,
                            'fieldHelpTooltipContent'        => $this->contactsParams['contactNotesFieldLabel'],
                            'fieldHidden'                    => $this->contactsParams['contactNotesFieldHidden'],
                            'fieldDisabled'                  => $this->contactsParams['contactNotesFieldDisabled'],
                            'fieldRequired'                  => $this->contactsParams['contactNotesFieldRequired'],
                            'fieldBazScan'                   => true,
                            'fieldBazJstreeSearch'           =>
                                (isset($this->params['multiple']) && $this->params['multiple'] === true) ? false : true,
                            'fieldBazPostOnCreate'           => $this->contactsParams['contactNotesFieldBazPostOnCreate'],
                            'fieldBazPostOnUpdate'           => $this->contactsParams['contactNotesFieldBazPostOnUpdate'],
                            'fieldDataInputMinLength'        => 1,
                            'fieldDataInputMaxLength'        => 2048,
                            'fieldTextareaRows'              => 2,
                            'fieldValue'                     => $this->contactsParams['contact_notes']
                        ]
                    ) .
                '</div>
            </div>';
    }

    protected function inclBaseJs()
    {
        $baseJs =
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
                    "' . $this->compSecId . '-contact_reference"           : { },';

        return $baseJs;
    }

    protected function inclNameJs()
    {
        return
            '"' . $this->compSecId . '-prefix"                      : { },
            "' . $this->compSecId . '-first_name"                   : { },
            "' . $this->compSecId . '-last_name"                    : { },
            "' . $this->compSecId . '-suffix"                       : { },';
    }

    protected function inclEmailJs()
    {
        if (isset($this->contactsParams['includeSecondaEmail']) && $this->contactsParams['includeSecondaEmail'] === true) {
            return
                '"' . $this->compSecId . '-email"                       : { },
                "' . $this->compSecId . '-secondary_email"              : { },
                "' . $this->compSecId . '-cc_emails_to_secondary_email" : { },';
        } else {
            return
                '"' . $this->compSecId . '-email"                       : { },';
        }
    }

    protected function inclPhoneJs()
    {
        return
            '"' . $this->compSecId . '-contact_phone"               : { },
            "' . $this->compSecId . '-contact_phone_ext"            : { },
            "' . $this->compSecId . '-contact_mobile"               : { },';
    }

    protected function inclOtherJs()
    {
        return
            '"' . $this->compSecId . '-contact_other"               : { },
            "' . $this->compSecId . '-contact_fax"                  : { },';
    }

    protected function inclNotesJs()
    {
        return
            '"' . $this->compSecId . '-contact_notes"               : { }';
    }
}
