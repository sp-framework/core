<?php

namespace Apps\Dash\Packages\AdminLTETags\Tags\Contacts;

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

    protected $contactsParams = [];

    protected $compSecId;

    public function __construct($view, $tag, $links, $escaper, $params, $contactsParams)
    {
        $this->view = $view;

        $this->tag = $tag;

        $this->links = $links;

        $this->escaper = $escaper;

        $this->adminLTETags = new AdminLTETags();

        $this->params = $params;

        $this->contactsParams = $contactsParams;

        $this->compSecId = $this->params['componentId'] . '-' . $this->params['sectionId'];

        $this->buildMultipleContactsLayout();
    }

    public function getContent()
    {
        return $this->content;
    }

    protected function buildMultipleContactsLayout()
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
                                    'fieldId'                        => 'contact_ids',
                                    'fieldLabel'                     => 'Contact IDs',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Contact IDs',
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
                                    'fieldId'                        => 'contact_id',
                                    'fieldLabel'                     => 'Contact ID',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Contact ID',
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
                                    'fieldId'                        => 'search_contacts',
                                    'fieldLabel'                     => 'Search Contacts/Add New',
                                    'fieldType'                      => 'input',
                                    'fieldHelp'                      => true,
                                    'fieldHelpTooltipContent'        => 'Search Contact. If contact does not exist, you can fill contact details and the system will add new contact.',
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
                    <div class="row" id="' . $this->compSecId . '-new-contact">
                        <div class="col">' .
                            $this->adminLTETags->useTag('contacts',
                                [
                                    'component'                                   => $this->params['component'],
                                    'componentName'                               => $this->params['componentName'],
                                    'componentId'                                 => $this->params['componentId'],
                                    'sectionId'                                   => $this->params['sectionId'],
                                    'contactFieldType'                            => 'single'
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
                                            'add-contact'       => [
                                                'title'                   => 'Add',
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
                                        'fieldId'                   => 'contacts',
                                        'fieldLabel'                => 'Contacts',
                                        'fieldType'                 => 'html',
                                        'fieldHelp'                 => true,
                                        'fieldHelpTooltipContent'   => 'Note: First contact is the list is primary contact',
                                        'fieldAdditionalClass'      => 'mb-0',
                                        'fieldRequired'             => false,
                                        'fieldBazScan'              => false,
                                        'fieldBazJstreeSearch'      => true,
                                        'fieldBazPostOnCreate'      => false,
                                        'fieldBazPostOnUpdate'      => false
                                    ]
                                ) .
                                '<ul class="list-group list-group-sortable" id="' . $this->compSecId . '-sortable-contacts">';

                                    if (isset($this->params['contact_ids']) &&
                                        count($this->params['contact_ids']) > 0
                                    ) {
                                        $this->content .=
                                            '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-contacts-nodata" hidden>
                                                <div class="row">
                                                    <div class="col text-uppercase">
                                                        <i class="fa fa-fw fa-exclamation"></i> Add Contacts
                                                    </div>
                                                </div>
                                            </div>';

                                        foreach ($this->params['contact_ids'] as $key => $contact) {
                                            if ($key === 0) {
                                                $listType = 'success';
                                            } else {
                                                $listType = 'secondary';
                                            }

                                            $this->content .=
                                                '<li class="list-group-item list-group-item-' . $listType . '" area-disabled="false" style="cursor: pointer"  data-new="0" data-contact-id="' . $contact['id'] . '">
                                                    <div class="row">
                                                        <div class="col">
                                                            <i class="fa fa-sort fa-fw handle"></i>
                                                        </div>
                                                        <div class="col">
                                                            <button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 contactDeleteButton">
                                                                <i class="fa fas fa-fw text-xs fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col list-group-item-data">
                                                            <dl class="row mb-0">
                                                                <dt class="text-uppercase mb-0 col-sm-4">Email</dt>
                                                                <dd class="mb-0 col-sm-8 cla-email">' . $contact['account_email'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Mobile</dt>
                                                                <dd class="mb-0 col-sm-8 cla-mobile">' . $contact['contact_mobile'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">First Name</dt>
                                                                <dd class="mb-0 col-sm-8 cla-first-name">' . $contact['first_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Last Name</dt>
                                                                <dd class="mb-0 col-sm-8 cla-last-name">' . $contact['last_name'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Phone</dt>
                                                                <dd class="mb-0 col-sm-8 cla-state">' . $contact['contact_phone'] . '</dd>
                                                                <dt class="text-uppercase mb-0 col-sm-4">Extension</dt>
                                                                <dd class="mb-0 col-sm-8 cla-extension">' . $contact['contact_phone_ext'] . '</dd>
                                                            </dl>
                                                        </div>
                                                    </div>
                                                </li>';
                                        }
                                    } else {
                                        $this->content .=
                                            '<div class="list-group-item list-group-item-secondary no-data rounded-0" id="' . $this->compSecId . '-contacts-nodata">
                                                <div class="row">
                                                    <div class="col text-uppercase">
                                                        <i class="fa fa-fw fa-exclamation"></i> Add Contacts
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
                    "' . $this->compSecId . '-search_contacts"                   : {
                        afterInit : function () {
                            dataCollectionSection["' . $this->compSecId . '-form"]["autoCompleteSearchContacts"] =
                                new autoComplete({
                                    data: {
                                        src: async() => {
                                            const url = "' . $this->links->url("business/directory/contacts/searchContactFullName") . '";

                                            var myHeaders = new Headers();
                                            myHeaders.append("accept", "application/json");

                                            var formdata = new FormData();
                                            formdata.append("search", document.querySelector("#' . $this->compSecId . '-search_contacts").value);
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
                                            if (response.contacts) {
                                                return response.contacts;
                                            } else {
                                                return [];
                                            }
                                        },
                                        key: ["full_name"],
                                        cache: false
                                    },
                                    selector: "#' . $this->compSecId . '-search_contacts",
                                    threshold : 4,
                                    debounce: 500,
                                    searchEngine: "strict",
                                    resultsList: {
                                        render: true,
                                        container: source => {
                                            source.setAttribute("id", "' . $this->compSecId . '-search_contacts_list");
                                            source.setAttribute("class", "autoComplete_results");
                                        },
                                        destination: "#' . $this->compSecId . '-search_contacts",
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
                                        if (document.querySelector("#' . $this->compSecId . '-search_contacts_list")) {
                                            $("#' . $this->compSecId . '-search_contacts_list").empty().append(result);
                                        } else {
                                            $("#' . $this->compSecId . '-search_contacts").parent(".form-group").append(
                                                \'<div id="' . $this->compSecId . '-search_contacts_list" class="autoComplete_results"></div>\'
                                            );
                                            document.querySelector("#' . $this->compSecId . '-search_contacts_list").appendChild(result);
                                        }
                                    },
                                    onSelection: feedback => {
                                        $("#' . $this->compSecId . '-contact_id").val(feedback.selection.value.id);
                                        $("#' . $this->compSecId . '-contact_id").attr("value", feedback.selection.value.id);
                                        $("#' . $this->compSecId . '-search_contacts").blur();
                                        $("#' . $this->compSecId . '-search_contacts").val(feedback.selection.value.full_name);
                                        $("#' . $this->compSecId . '-search_contacts").attr("value", feedback.selection.value.full_name);

                                        var url = "' . $this->links->url("business/directory/contacts/searchContactId") . '";
                                        var postData = { };
                                        postData["id"] = feedback.selection.value.id;
                                        postData[$("#security-token").attr("name")] = $("#security-token").val();

                                        $.post(url, postData, function(response) {
                                            toggleContactFields(response);
                                            if (response.tokenKey && response.token) {
                                                $("#security-token").attr("name", response.tokenKey);
                                                $("#security-token").val(response.token);
                                            }
                                        }, "json");
                                    }
                            });
                            // On delete
                            $("#' . $this->compSecId . '-search_contacts").on("input propertychange", function() {
                                $("#' . $this->compSecId . '-contact_id").val("");
                                toggleContactFields(false);
                            });

                            $("#' . $this->compSecId . '-search_contacts").focusout(function() {
                                $("#' . $this->compSecId . '-search_contacts_list").children("li").remove();
                            });

                            dataCollectionSection["data"]["contact_ids"] = { }
                            initMainButtons();

                            function initMainButtons() {
                                $("#' . $this->compSecId . '-cancel-contact").off();
                                $("#' . $this->compSecId . '-cancel-contact").click(function(e) {
                                    e.preventDefault();
                                    $("#' . $this->compSecId . '-search_contacts").val("");
                                    toggleContactFields(false);
                                });
                                $("#' . $this->compSecId . '-add-contact").off();
                                $("#' . $this->compSecId . '-add-contact").click(function(e) {
                                    e.preventDefault();
                                    $("#' . $this->compSecId . '-search_contacts").val("");
                                    extractData();
                                });
                            }

                            function toggleContactFields(data) {
                                if (data) {
                                    $("#' . $this->compSecId . '-contact_id").val(data.contact.id);
                                    $("#' . $this->compSecId . '-account_email").val(data.contact.account_email);
                                    $("#' . $this->compSecId . '-contact_mobile").val(data.contact.contact_mobile);
                                    $("#' . $this->compSecId . '-first_name").val(data.contact.first_name);
                                    $("#' . $this->compSecId . '-last_name").val(data.contact.last_name);
                                    $("#' . $this->compSecId . '-contact_phone").val(data.contact.contact_phone);
                                    $("#' . $this->compSecId . '-contact_phone_ext").val(data.contact.contact_phone_ext);
                                    $("#' . $this->compSecId . '-account_email").attr("disabled", true);
                                } else {
                                    $("#' . $this->compSecId . '-contact_id").val("");
                                    $("#' . $this->compSecId . '-account_email").val("");
                                    $("#' . $this->compSecId . '-account_email").attr("disabled", false);
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
                                    var contactId, contactNew;

                                    data["contact_id"] = $("#' . $this->compSecId . '-contact_id").val();
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

                                    var contactsLi = $("#' . $this->compSecId . '-sortable-contacts li");

                                    if (contactsLi.length > 0) {
                                        listType = "secondary";
                                    } else {
                                        listType = "success";
                                    }

                                    if (data["contact_id"] === "") {
                                        contactId = Date.now();
                                        contactNew = "1";
                                    } else {
                                        contactId = data["contact_id"];
                                        contactNew = "0";
                                    }

                                    var list =
                                        \'<li class="list-group-item list-group-item-\' + listType +
                                            \'" area-disabled="false" style="cursor: pointer" data-new="\' + contactNew +
                                            \'" data-contact-id="\' + contactId + \'">\' +
                                            \'<div class="row">\' +
                                                \'<div class="col">\' +
                                                    \'<i class="fa fa-sort fa-fw handle"></i>\' +
                                                \'</div>\' +
                                                \'<div class="col">\' +
                                                    \'<button data-sort-id="" type="button" class="btn btn-xs btn-danger float-right ml-1 contactDeleteButton">\' +
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

                                    if (contactsLi.length > 0) {
                                        var exists = false;
                                        $(contactsLi).each(function(index, li) {
                                            var liContactId = $(li).data("contact-id");
                                            if (liContactId == contactId) {
                                                PNotify.error({"title" : "Contact with same ID already added!"});
                                                exists = true;
                                                return;
                                            }
                                            var email = $($(li).find(".cla-email")).html();

                                            if (email.toLowerCase() === data["account_email"].toLowerCase()) {
                                                PNotify.error({"title" : "Contact with same email already added!"});
                                                exists = true;
                                                return;
                                            }
                                        });
                                        if (exists === false) {
                                            $("#' . $this->compSecId . '-sortable-contacts").append(list);
                                        }
                                    } else {
                                        $("#' . $this->compSecId . '-sortable-contacts").append(list);
                                    }

                                    toggleContactFields(false);
                                    initSortable("' . $this->compSecId . '-sortable-contacts");
                                    collectData();
                                    registerContactButtons();
                                }
                            }

                            function initSortable(element) {
                                var el = document.getElementById(element);
                                dataCollectionSection["' . $this->compSecId . '-form"]["sortable"] = { };
                                dataCollectionSection["' . $this->compSecId . '-form"]["sortable"] = Sortable.create(el, {
                                    dataIdAttr : "data-contact-id",
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
                                dataCollectionSection["data"]["contact_ids"] = { };

                                if ($("#' . $this->compSecId . '-sortable-contacts li").length > 0) {
                                    $("#' . $this->compSecId . '-sortable-contacts li").each(function(index, id) {
                                        var data = { };
                                        data["seq"] = index;
                                        var contactId;

                                        $(id).find("dd").each(function(index,dd) {
                                            contactId = $(dd).parents("li").data("contact-id");
                                            data["id"] = $(dd).parents("li").data("contact-id");
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

                                        dataCollectionSection["data"]["contact_ids"][contactId] = data;
                                    });
                                }
                            }

                            function registerContactButtons() {
                                $(".contactDeleteButton").each(function(index, button) {
                                    $(button).off();
                                    $(button).click(function() {

                                        var contactsCount = $(this).parents("ul").children("li").length;

                                        if (contactsCount > 1) {
                                            if ($(this).parents("li").is(".list-group-item-success")) {

                                                $($(this).parents("li").siblings("li")[0]).removeClass("list-group-item-secondary");
                                                $($(this).parents("li").siblings("li")[0]).addClass("list-group-item-success");
                                            }
                                        }

                                        $(this).parents("li").remove();

                                        collectData();
                                    });
                                });
                            }

                            $(".list-group-sortable").each(function(index, ul) {
                                initSortable($(ul)[0].id);
                            });
                            collectData();
                            registerContactButtons();
                        }
                    }
                });
            </script>';

        return $inclJs;
    }
}