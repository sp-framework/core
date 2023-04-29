<?php

namespace Apps\Core\Packages\AdminLTETags\Tags\Employees;

use Apps\Core\Packages\AdminLTETags\AdminLTETags;

class Single
{
    protected $view;

    protected $tag;

    protected $links;

    protected $escaper;

    protected $adminLTETags;

    protected $params;

    protected $content;

    protected $employeesParams = [];

    protected $compSecId;

    public function __construct($view, $tag, $links, $escaper, $params, $employeesParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->adminLTETags = new AdminLTETags();

        $this->params = $params;

        $this->employeesParams = $employeesParams;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->buildSingleEmployeeLayout();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildSingleEmployeeLayout()
    {
        $this->content .=
            '<div class="row">' . $this->inclEmailMobile() . '</div>' .
            '<div class="row">' . $this->inclName() . '</div>' .
            '<div class="row">' . $this->inclPhone() . '</div>';

        $this->content .=
            $this->inclBaseJs() .
                $this->inclEmailMobileJs() .
                $this->inclNameJs() .
                $this->inclPhoneJs();

        $this->content .=
            '});</script>';
    }

    protected function inclEmailMobile()
    {
        return
            '<div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'account_email',
                        'fieldLabel'                            => 'Email',
                        'fieldType'                             => 'input',
                        'fieldPlaceholder'                      => 'Email',
                        'fieldHelp'                             => true,
                        'fieldHelpTooltipContent'               => 'Employee Email address. If field is disabled, it means employee has an account on this system. Only Systems Administrators can change the email address.',
                        'fieldBazScan'                          => true,
                        'fieldBazJstreeSearch'                  => true,
                        'fieldBazPostOnCreate'                  => false,
                        'fieldBazPostOnUpdate'                  => false,
                        'fieldRequired'                         => false,
                        'fieldDisabled'                         => true,
                        'fieldDataInputMinLength'               => 1,
                        'fieldDataInputMaxLength'               => 100,
                        'fieldValue'                            => ''
                    ]
                ) .
            '</div>
            <div class="col">' .
                $this->adminLTETags->useTag('fields',
                    [
                        'component'                             => $this->params['component'],
                        'componentName'                         => $this->params['componentName'],
                        'componentId'                           => $this->params['componentId'],
                        'sectionId'                             => $this->params['sectionId'],
                        'fieldId'                               => 'contact_mobile',
                        'fieldLabel'                            => 'Mobile',
                        'fieldType'                             => 'input',
                        'fieldHelp'                             => true,
                        'fieldHelpTooltipContent'               => 'Mobile phone number of the employee',
                        'fieldRequired'                         => false,
                        'fieldDisabled'                         => true,
                        'fieldBazScan'                          => true,
                        'fieldBazJstreeSearch'                  => true,
                        'fieldBazPostOnCreate'                  => false,
                        'fieldBazPostOnUpdate'                  => false,
                        'fieldDataInputMinLength'               => 1,
                        'fieldDataInputMaxLength'               => 15,
                        'fieldValue'                            => ''
                    ]
                ) .
            '</div>';
    }

    protected function inclName()
    {
        return
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
                        'fieldRequired'                  => false,
                        'fieldDisabled'                         => true,
                        'fieldBazScan'                   => true,
                        'fieldBazJstreeSearch'           => true,
                        'fieldBazPostOnCreate'           => false,
                        'fieldBazPostOnUpdate'           => false,
                        'fieldDataInputMinLength'        => 1,
                        'fieldDataInputMaxLength'        => 100,
                        'fieldValue'                     => ''
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
                        'fieldRequired'                  => false,
                        'fieldDisabled'                         => true,
                        'fieldBazScan'                   => true,
                        'fieldBazJstreeSearch'           => true,
                        'fieldBazPostOnCreate'           => false,
                        'fieldBazPostOnUpdate'           => false,
                        'fieldDataInputMinLength'        => 1,
                        'fieldDataInputMaxLength'        => 100,
                        'fieldValue'                     => ''
                    ]
                ) .
            '</div>';
    }

    protected function inclPhone()
    {
        return
            '<div class="col">' .
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
                        'fieldHelpTooltipContent'        => 'Phone number of the employee',
                        'fieldRequired'                  => false,
                        'fieldDisabled'                         => true,
                        'fieldBazScan'                   => true,
                        'fieldBazJstreeSearch'           => true,
                        'fieldBazPostOnCreate'           => false,
                        'fieldBazPostOnUpdate'           => false,
                        'fieldDataInputMinLength'        => 1,
                        'fieldDataInputMaxLength'        => 15,
                        'fieldValue'                     => ''
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
                        'fieldId'                        => 'contact_phone_ext',
                        'fieldLabel'                     => 'Extension',
                        'fieldType'                      => 'input',
                        'fieldHelp'                      => true,
                        'fieldHelpTooltipContent'        => 'Phone number extension of the employee',
                        'fieldRequired'                  => false,
                        'fieldDisabled'                         => true,
                        'fieldBazScan'                   => true,
                        'fieldBazJstreeSearch'           => true,
                        'fieldBazPostOnCreate'           => false,
                        'fieldBazPostOnUpdate'           => false,
                        'fieldDataInputMinLength'        => 1,
                        'fieldDataInputMaxLength'        => 15,
                        'fieldValue'                     => ''
                    ]
                ) .
            '</div>';
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
                    ';

        return $baseJs;
    }

    protected function inclEmailMobileJs()
    {
        return
            '"' . $this->compSecId . '-account_email"        : { },
            "' . $this->compSecId . '-contact_mobile"        : { },';
    }

    protected function inclNameJs()
    {
        return
            '"' . $this->compSecId . '-first_name"           : { },
            "' . $this->compSecId . '-last_name"             : { },';
    }

    protected function inclPhoneJs()
    {
        return
            '"' . $this->compSecId . '-contact_phone"        : { },
            "' . $this->compSecId . '-contact_phone_ext"     : { }';
    }
}
