<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Employees;

use Apps\Dash\Packages\AdminLTETags\AdminLTETags;
use Phalcon\Helper\Json;

class Multiple
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

        $this->buildMultipleEmployeesLayout();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildMultipleEmployeesLayout()
    {
        $this->content .=
            '<div class="row vdivide">
                <div class="col">
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'employee_ids',
                                    'fieldLabel'                     => 'Employee IDs',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Employee IDs',
                                    'fieldRequired'                  => false,
                                    'fieldBazScan'                   => false,
                                    'fieldBazPostOnCreate'           => true,
                                    'fieldBazPostOnUpdate'           => true,
                                    'fieldHidden'                    => true,
                                    'fieldDisabled'                  => true,
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
                                    'fieldId'                        => 'employee_id',
                                    'fieldLabel'                     => 'Employee ID',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Employee ID',
                                    'fieldRequired'                  => false,
                                    'fieldBazScan'                   => false,
                                    'fieldBazPostOnCreate'           => false,
                                    'fieldBazPostOnUpdate'           => false,
                                    'fieldHidden'                    => true,
                                    'fieldDisabled'                  => true,
                                    'fieldValue'                     => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row">
                        <div class="col">' .
                            $this->adminLTETags->useTag('fields',
                                [
                                    'component'                      => $this->params['component'],
                                    'componentName'                  => $this->params['componentName'],
                                    'componentId'                    => $this->params['componentId'],
                                    'sectionId'                      => $this->params['sectionId'],
                                    'fieldId'                        => 'search_employees',
                                    'fieldLabel'                     => 'Search Employees',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Search Employee.',
                                    'fieldRequired'                  => false,
                                    'fieldBazScan'                   => true,
                                    'fieldBazPostOnCreate'           => false,
                                    'fieldBazPostOnUpdate'           => false,
                                    'fieldHidden'                    => false,
                                    'fieldDisabled'                  => false,
                                    'fieldValue'                     => ''
                                ]
                            ) .
                        '</div>
                    </div>
                    <div class="row" id="' . $this->compSecId . '-new-employee">
                        <div class="col">' .
                            $this->adminLTETags->useTag('employees',
                                [
                                    'component'                                   => $this->params['component'],
                                    'componentName'                               => $this->params['componentName'],
                                    'componentId'                                 => $this->params['componentId'],
                                    'sectionId'                                   => $this->params['sectionId'],
                                    'employeeFieldType'                            => 'single'
                                ]
                            ) .
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
                                            'add-employee'       => [
                                                'title'                   => 'Add',
                                                'size'                    => 'xs',
                                                'type'                    => 'primary',
                                                'icon'                    => 'plus',
                                                'position'                => 'right'
                                            ],
                                            'cancel-employee'    => [
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
                <div class="col">';
                    $this->content .=
                        '<div class="row">
                            <div class="col">' .
                                $this->adminLTETags->useTag('fields',
                                    [
                                        'component'                 => $this->params['component'],
                                        'componentName'             => $this->params['componentName'],
                                        'componentId'               => $this->params['componentId'],
                                        'sectionId'                 => $this->params['sectionId'],
                                        'fieldId'                   => 'employees',
                                        'fieldLabel'                => 'Location Contacts',
                                        'fieldType'                 => 'html',
                                        'fieldHelp'                 => true,
                                        'fieldHelpTooltipContent'   => 'Note: First employee is the list is primary employee',
                                        'fieldAdditionalClass'      => 'mb-0',
                                        'fieldRequired'             => false,
                                        'fieldBazScan'              => false,
                                        'fieldBazJstreeSearch'      => true,
                                        'fieldBazPostOnCreate'      => false,
                                        'fieldBazPostOnUpdate'      => false
                                    ]
                                ) .
                                '<ul class="list-group list-group-sortable" id="' . $this->compSecId . '-sortable-employees">';

                                    if (isset($this->params['employee_ids']) &&
                                        count($this->params['employee_ids']) > 0
                                    ) {
                                        $this->content .=
                                            '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-employees-nodata" hidden>
                                                <div class="row">
                                                    <div class="col text-uppercase">
                                                        <i class="fa fa-fw fa-exclamation"></i> Add Contacts
                                                    </div>
                                                </div>
                                            </div>';

                                        foreach ($this->params['employee_ids'] as $key => $employee) {
                                            if ($key === 0) {
                                                $listType = 'success';
                                            } else {
                                                $listType = 'secondary';
                                            }

                                            $this->content .=
                                                '<li class="list-group-item list-group-item-' . $listType . '" area-disabled="false" style="cursor: pointer"  data-new="0" data-employee-id="' . $employee['id'] . '">
                                                    <div class="row">
                                                        <div class="col">
                                                            <i class="fa fa-sort fa-fw handle"></i>
                                                        </div>
                                                        <div class="col">
                                                            <button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 employeeDeleteButton">
                                                                <i class="fa fas fa-fw text-xs fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col list-group-item-data">
                                                            <dl class="row mb-0">
                                                                <dt class="text-uppercase mb-0 col-sm-4">Email</dt>
                                                                <dd class="mb-0 col-sm-8 cla-email">' . $employee['account_email'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Mobile</dt>
                                                                <dd class="mb-0 col-sm-8 cla-mobile">' . $employee['contact_mobile'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">First Name</dt>
                                                                <dd class="mb-0 col-sm-8 cla-first-name">' . $employee['first_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Last Name</dt>
                                                                <dd class="mb-0 col-sm-8 cla-last-name">' . $employee['last_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Phone</dt>
                                                                <dd class="mb-0 col-sm-8 cla-state">' . $employee['contact_phone'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Extension</dt>
                                                                <dd class="mb-0 col-sm-8 cla-extension">' . $employee['contact_phone_ext'] . '</dd>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </li>';
                                        }
                                    } else {
                                        $this->content .=
                                            '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-employees-nodata">
                                                <div class="row">
                                                    <div class="col text-uppercase">
                                                        <i class="fa fa-fw fa-exclamation"></i> Add Employees
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

                $this->inclEmployeesJs();
    }

    protected function inclEmployeesJs()
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
                    "' . $this->compSecId . '-search_employees"                   : {
                        afterInit : function () {
                            dataCollectionSection["' . $this->compSecId . '-form"]["autoCompleteSearchEmployees"] =
                                new autoComplete({
                                    data: {
                                        src: async() => {
                                            const url = "' . $this->links->url("hrms/employees/searchEmployee") . '";

                                            var myHeaders = new Headers();
                                            myHeaders.append("accept", "application/json");

                                            var formdata = new FormData();
                                            formdata.append("search", document.querySelector("#' . $this->compSecId . '-search_employees").value);
                                            formdata.append($("#security-token").attr("name"), $("#security-token").val());

                                            var requestOptions = {
                                                method: "POST",
                                                headers: myHeaders,
                                                body: formdata
                                            };

                                            const responseData = await fetch(url, requestOptions);

                                            const response = await responseData.json();

                                            if (response.tokenKey && response.token) {
                                                $("#security-token").attr("name", response.tokenKey);
                                                $("#security-token").val(response.token);
                                            }

                                            if (response.responseData && response.responseData.employees) {
                                                return response.responseData.employees;
                                            } else {
                                                return [];
                                            }
                                        },
                                        key: ["full_name"],
                                        cache: false
                                    },
                                    selector: "#' . $this->compSecId . '-search_employees",
                                    threshold : 3,
                                    debounce: 500,
                                    searchEngine: "strict",
                                    resultsList: {
                                        render: true,
                                        container: source => {
                                            source.setAttribute("id", "' . $this->compSecId . '-search_employees_list");
                                            source.setAttribute("class", "autoComplete_results");
                                        },
                                        destination: "#' . $this->compSecId . '-search_employees",
                                        position: "afterend",
                                        element: "div",
                                        className: "autoComplete_results"
                                    },
                                    maxResults: 5,
                                    highlight: true,
                                    resultItem: {
                                        content: (data, source) => {
                                            source.innerHTML = data.match;
                                        },
                                        element: "div"
                                    },
                                    noResults: () => {
                                        const result = document.createElement("li");
                                        result.setAttribute("class", "autoComplete_result text-danger");
                                        result.setAttribute("tabindex", "1");
                                        result.innerHTML = "No search results. Click field help for more information.";
                                        if (document.querySelector("#' . $this->compSecId . '-search_employees_list")) {
                                            $("#' . $this->compSecId . '-search_employees_list").empty().append(result);
                                        } else {
                                            $("#' . $this->compSecId . '-search_employees").parent(".form-group").append(
                                                \'<div id="' . $this->compSecId . '-search_employees_list" class="autoComplete_results"></div>\'
                                            );
                                            document.querySelector("#' . $this->compSecId . '-search_employees_list").appendChild(result);
                                        }
                                    },
                                    onSelection: feedback => {
                                        $("#' . $this->compSecId . '-employee_id").val(feedback.selection.value.id);
                                        $("#' . $this->compSecId . '-employee_id").attr("value", feedback.selection.value.id);
                                        $("#' . $this->compSecId . '-search_employees").blur();
                                        $("#' . $this->compSecId . '-search_employees").val(feedback.selection.value.full_name);
                                        $("#' . $this->compSecId . '-search_employees").attr("value", feedback.selection.value.full_name);

                                        var url = "' . $this->links->url("hrms/employees/searchEmployeeId") . '";
                                        var postData = { };
                                        postData["id"] = feedback.selection.value.id;
                                        postData[$("#security-token").attr("name")] = $("#security-token").val();

                                        $.post(url, postData, function(response) {
                                            if (response.responseData) {
                                                toggleEmployeeFields(response.responseData);
                                            }
                                            if (response.tokenKey && response.token) {
                                                $("#security-token").attr("name", response.tokenKey);
                                                $("#security-token").val(response.token);
                                            }
                                        }, "json");
                                    }
                            });
                            // On delete
                            $("#' . $this->compSecId . '-search_employees").on("input propertychange", function() {
                                $("#' . $this->compSecId . '-employee_id").val("");
                                toggleEmployeeFields(false);
                            });

                            $("#' . $this->compSecId . '-search_employees").focusout(function() {
                                $("#' . $this->compSecId . '-search_employees_list").children("li").remove();
                            });

                            dataCollectionSection["data"]["employee_ids"] = { }
                            initMainButtons();

                            function initMainButtons() {
                                $("#' . $this->compSecId . '-cancel-employee").off();
                                $("#' . $this->compSecId . '-cancel-employee").click(function(e) {
                                    e.preventDefault();
                                    $("#' . $this->compSecId . '-search_employees").val("");
                                    toggleEmployeeFields(false);
                                });
                                $("#' . $this->compSecId . '-add-employee").off();
                                $("#' . $this->compSecId . '-add-employee").click(function(e) {
                                    e.preventDefault();
                                    $("#' . $this->compSecId . '-search_employees").val("");
                                    extractData();
                                });
                            }

                            function toggleEmployeeFields(data) {
                                if (data) {
                                    $("#' . $this->compSecId . '-employee_id").val(data.employee.id);
                                    $("#' . $this->compSecId . '-account_email").val(data.employee.account_email);
                                    $("#' . $this->compSecId . '-contact_mobile").val(data.employee.contact_mobile);
                                    $("#' . $this->compSecId . '-first_name").val(data.employee.first_name);
                                    $("#' . $this->compSecId . '-last_name").val(data.employee.last_name);
                                    $("#' . $this->compSecId . '-contact_phone").val(data.employee.contact_phone);
                                    $("#' . $this->compSecId . '-contact_phone_ext").val(data.employee.contact_phone_ext);
                                } else {
                                    $("#' . $this->compSecId . '-employee_id").val("");
                                    $("#' . $this->compSecId . '-account_email").val("");
                                    $("#' . $this->compSecId . '-contact_mobile").val("");
                                    $("#' . $this->compSecId . '-first_name").val("");
                                    $("#' . $this->compSecId . '-last_name").val("");
                                    $("#' . $this->compSecId . '-contact_phone").val("");
                                    $("#' . $this->compSecId . '-contact_phone_ext").val("");
                                }
                            }

                            function extractData() {
                                if ($("#' . $this->compSecId . '-account_email").val() === "") {
                                    $("#' . $this->compSecId . '-account_email").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-account_email").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-contact_mobile").val() === "") {
                                    $("#' . $this->compSecId . '-contact_mobile").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-contact_mobile").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-first_name").val() === "") {
                                    $("#' . $this->compSecId . '-first_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-first_name").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-last_name").val() === "") {
                                    $("#' . $this->compSecId . '-last_name").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-last_name").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else if ($("#' . $this->compSecId . '-contact_phone").val() === "") {
                                    $("#' . $this->compSecId . '-contact_phone").addClass("is-invalid");
                                    $("#' . $this->compSecId . '-contact_phone").focus(function() {
                                        $(this).removeClass("is-invalid");
                                    });
                                } else {
                                    var data = { };
                                    var employeeId, employeeNew;

                                    data["employee_id"] = $("#' . $this->compSecId . '-employee_id").val();
                                    data["first_name"] = $("#' . $this->compSecId . '-first_name").val();
                                    data["last_name"] = $("#' . $this->compSecId . '-last_name").val();
                                    data["account_email"] = $("#' . $this->compSecId . '-account_email").val();
                                    data["contact_mobile"] = $("#' . $this->compSecId . '-contact_mobile").val();
                                    data["contact_phone"] = $("#' . $this->compSecId . '-contact_phone").val();
                                    data["contact_phone_ext"] = $("#' . $this->compSecId . '-contact_phone_ext").val();

                                    var html =
                                        \'<dl class="row mb-0">\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Email</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-email">\' + data["account_email"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Mobile</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-mobile">\' + data["contact_mobile"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">First Name</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-first-name">\' + data["first_name"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Last Name</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-last-name">\' + data["last_name"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Phone</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-phone">\' + data["contact_phone"] + \'</dd>\' +
                                            \'<dt class="text-uppercase mb-0 col-sm-4">Extension</dt>\' +
                                            \'<dd class="mb-0 col-sm-8 cla-extension">\' + data["contact_phone_ext"] + \'</dd>\' +
                                        \'</dl>\';

                                    if ($("#' . $this->compSecId . '-sortable-employees li").length > 0) {
                                        listType = "secondary";
                                    } else {
                                        listType = "success";
                                    }

                                    if (data["employee_id"] === "") {
                                        employeeId = Date.now();
                                        employeeNew = "1";
                                    } else {
                                        employeeId = data["employee_id"];
                                        employeeNew = "0";
                                    }

                                    var list =
                                        \'<li class="list-group-item list-group-item-\' + listType +
                                            \'" area-disabled="false" style="cursor: pointer" data-new="\' + employeeNew +
                                            \'" data-employee-id="\' + employeeId + \'">\' +
                                            \'<div class="row">\' +
                                                \'<div class="col">\' +
                                                    \'<i class="fa fa-sort fa-fw handle"></i>\' +
                                                \'</div>\' +
                                                \'<div class="col">\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 employeeDeleteButton">\' +
                                                        \'<i class="fa fas fa-fw text-xs fa-trash"></i>\' +
                                                    \'</button>\' +
                                                \'</div>\' +
                                            \'</div>\' +
                                            \'<div class="row">\' +
                                                \'<div class="col list-group-item-data">\' +
                                                    html +
                                                \'</div>\' +
                                            \'</div>\' +
                                        \'</li>\';

                                    var employeesLi = $("#' . $this->compSecId . '-sortable-employees li");

                                    if (employeesLi.length > 0) {
                                        var exists = false;
                                        $(employeesLi).each(function(index, li) {
                                            var email = $($(li).find(".cla-email")).html();

                                            if (email.toLowerCase() === data["account_email"].toLowerCase()) {
                                                PNotify.error({"title" : "Employee with same email already added!"});
                                                exists = true;

                                                return;
                                            }
                                        });

                                        if (exists === false) {
                                            $("#' . $this->compSecId . '-sortable-employees").append(list);
                                        }
                                    } else {
                                        $("#' . $this->compSecId . '-sortable-employees").append(list);

                                        $("#' . $this->compSecId . '-employees-nodata").attr("hidden", true);
                                    }

                                    toggleEmployeeFields(false);
                                    initSortable("' . $this->compSecId . '-sortable-employees");
                                    collectData();
                                    registerEmployeeButtons();
                                }
                            }

                            function initSortable(element) {
                                var el = document.getElementById(element);
                                dataCollectionSection["' . $this->compSecId . '-form"]["sortable"] = { };
                                dataCollectionSection["' . $this->compSecId . '-form"]["sortable"] = Sortable.create(el, {
                                    dataIdAttr : "data-employee-id",
                                    onEnd: function(e) {
                                        if (e.newIndex === 0) {
                                            $($(e.from).find("li")).each(function(index, li) {
                                                $(li).removeClass("list-group-item-success");
                                                $(li).addClass("list-group-item-secondary");
                                            });
                                            $(e.item).removeClass("list-group-item-secondary");
                                            $(e.item).addClass("list-group-item-success");
                                        }
                                        collectData();
                                    }
                                });
                            }

                            function collectData() {
                                dataCollectionSection["data"]["employee_ids"] = { };

                                if ($("#' . $this->compSecId . '-sortable-employees li").length > 0) {
                                    $("#' . $this->compSecId . '-sortable-employees li").each(function(index, id) {
                                        var data = { };
                                        data["seq"] = index;
                                        var employeeId;

                                        $(id).find("dd").each(function(index,dd) {
                                            employeeId = $(dd).parents("li").data("employee-id");
                                            data["id"] = $(dd).parents("li").data("employee-id");
                                            data["new"] = $(dd).parents("li").data("new");
                                            if ($(dd).is(".cla-email")) {
                                                data["account_email"] = $(dd).html();
                                            } else if ($(dd).is(".cla-mobile")) {
                                                data["contact_mobile"] = $(dd).html();
                                            } else if ($(dd).is(".cla-first-name")) {
                                                data["first_name"] = $(dd).html();
                                            } else if ($(dd).is(".cla-last-name")) {
                                                data["last_name"] = $(dd).html();
                                            } else if ($(dd).is(".cla-phone")) {
                                                data["contact_phone"] = $(dd).html();
                                            } else if ($(dd).is(".cla-extension")) {
                                                data["contact_phone_ext"] = $(dd).html();
                                            }
                                        });

                                        dataCollectionSection["data"]["employee_ids"][employeeId] = data;
                                    });
                                }
                            }

                            function registerEmployeeButtons() {
                                $(".employeeDeleteButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {

                                        var employeesCount = $(this).parents("ul").children("li").length;

                                        if (employeesCount > 1) {
                                            if ($(this).parents("li").is(".list-group-item-success")) {

                                                $($(this).parents("li").siblings("li")[0]).removeClass("list-group-item-secondary");
                                                $($(this).parents("li").siblings("li")[0]).addClass("list-group-item-success");
                                            }
                                        }

                                        $(this).parents("li").remove();

                                        employeesCount = employeesCount - 1;

                                        if (employeesCount === 0) {
                                            $("#' . $this->compSecId . '-employees-nodata").attr("hidden", false);
                                        }

                                        collectData();
                                    });
                                });
                            }

                            $(".list-group-sortable").each(function(index, ul) {
                                initSortable($(ul)[0].id);
                            });
                            collectData();
                            registerEmployeeButtons();
                        }
                    }
                });
            </script>';

        return $inclJs;
    }
}