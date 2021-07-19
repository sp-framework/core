/* exported BazMessenger */
/* globals PNotify EmojiPicker autoComplete moment */
/*
* @title                    : BazMessenger
* @description              : Baz Messenger Lib
* @developer                : guru@bazaari.com.au
* @usage                    : BazMessenger._function_(_options_);
* @functions                : BazMessengerInit
* @options                  :
*/

var _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; };
// var _typeof = typeof Symbol === "function" && typeof Symbol.iterator === "symbol" ? function (obj) { return typeof obj; } : function (obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; };

// eslint-disable-next-line no-unused-vars
var BazMessenger = function() {
    var BazMessenger = void 0;
    var dataCollection;
    var messengerButonIconColor = $('#messenger-button-icon').data('iconcolor');
    var initialized = false;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init() {
        initialized = true;
        dataCollection = window.dataCollection;
        dataCollection.env.messenger = { };

        dataCollection.env.messenger.emojiPicker = new EmojiPicker({
            emojiable_selector: '[data-emojiable=true]',
            assetsPath: '/dash/default/images/emoji-picker/',
            popupButtonClasses: 'fa fa-fw fa-smile',
        });

        dataCollection.env.messenger.search =
            new autoComplete({
                data: {
                    src: async() => {
                        const url = '{{links.url("system/messenger/searchAccount")}}';

                        var myHeaders = new Headers();
                        myHeaders.append("accept", "application/json");

                        var formdata = new FormData();
                        formdata.append("search", document.querySelector("#messenger-main-search").value);
                        formdata.append($('#security-token').attr('name'), $('#security-token').val());

                        var requestOptions = {
                            method: 'POST',
                            headers: myHeaders,
                            body: formdata
                        };

                        const responseData = await fetch(url, requestOptions);

                        const response = await responseData.json();

                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }

                        if (response.accounts) {
                            return response.accounts;
                        } else {
                            return [];
                        }
                    },
                    key: ["email"],
                    cache: false
                },
                selector: "#messenger-main-search",
                threshold : 4,
                debounce: 500,
                searchEngine: "strict",
                resultsList: {
                    render: true,
                    container: source => {
                        source.setAttribute("id", "messenger-main-search_list");
                        source.setAttribute("class", "autoComplete_results");
                    },
                    destination: "#messenger-main-search",
                    position: "afterend",
                    element: "div"
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
                    if (document.querySelector("#messenger-main-search_list")) {
                        $("#messenger-main-search_list").empty().append(result);
                    } else {
                        $("#messenger-main-search").parent(".form-group").append(
                            '<div id="messenger-main-search_list" class="autoComplete_results"></div>'
                        );
                        document.querySelector("#messenger-main-search_list").appendChild(result);
                    }
                },
                onSelection: feedback => {
                    $('#messenger-main-search').blur();
                    $('#messenger-main-search').val(feedback.selection.value.email);
                    $('#messenger-main-search').attr('value', feedback.selection.value.email);
                }
            });
        serviceOnline();
    }

    function serviceOnline() {
        //eslint-disable-next-line
        console.log('serviceOnline');

        if (!initialized) {
            init();
        }

        $("#messenger-online").attr('hidden', false);
        $("#messenger-offline").attr('hidden', true);
        $('#messenger-offline-icon').attr('hidden', true);
        $('#messenger-button-icon').removeClass('text-success text-warning text-danger text-secondary').addClass('text-' + messengerButonIconColor);
        initListeners();
    }

    function initListeners() {
        $('#messenger-main-status').off();
        $('#messenger-main-status').change(function() {
            var status = $('#messenger-main-status option:selected').val();

            if (status == 0) {
                return;
            }
            var url = dataCollection.env.rootPath + dataCollection.env.appRoute + '/system/messenger/changestatus';

            var postData = { };
            postData[$('#security-token').attr('name')] = $('#security-token').val();
            postData['status'] = status;

            $.post(url, postData, function(response) {
                if (response.tokenKey && response.token) {
                    $('#security-token').attr('name', response.tokenKey);
                    $('#security-token').val(response.token);
                }
                if (response.responseCode == 0) {
                    var statusTextColor;
                    if (status == 1) {
                        statusTextColor = 'success';
                    } else if (status == 2) {
                        statusTextColor = 'warning';
                    } else if (status == 3) {
                        statusTextColor = 'danger';
                    } else if (status == 4) {
                        statusTextColor = 'secondary';
                    }

                    $('#messenger-button-icon').removeClass('text-success text-warning text-danger text-secondary').addClass('text-' + statusTextColor);
                    messengerButonIconColor = statusTextColor;
                } else {
                    PNotify.error({
                        text        : response.responseMessage,
                        textTrusted : true
                    });
                }
            }, 'json');
        });

        $('#messenger-users li').each(function(index, li) {
            $(li).off();
            $(li).click(function(e) {
                e.preventDefault();
                messengerWindow($(this).data());

                $('#messenger-button').ControlSidebar('toggle');
            });
        });

        // On delete
        $('#messenger-main-search').off();
        $('#messenger-main-search').on('input propertychange', function() {
            if ($('#messenger-main-search').val().length === 0) {
                $('#messenger-main-search_list').children().remove();
            }
        });
        $('#messenger-main-search').focusout(function() {
            $('#messenger-main-search_list').children().remove();
        });
    }

    function messengerWindow(user) {
        if ($('#messenger-windows #messenger-window-' + user.user).length > 0) {
            $('.messenger-input-' + user.user).focus();
            return;
        }

        var currentMessengerWindows = $('.messenger-window').length;

        var fromLeft = 5;
        var fromBottom = -12;

        if (currentMessengerWindows === 0) {
            fromLeft = 5;
            $('.main-footer').append('<div id="messenger-windows"></div>');
        } else if (currentMessengerWindows === 1) {
            fromLeft = 10 + (currentMessengerWindows * 473);
        } else if (currentMessengerWindows > 1 && currentMessengerWindows < 3) {
            fromLeft = (5 * currentMessengerWindows) + (currentMessengerWindows * 473) + 5;
        } else {
            PNotify.error({text: "Only 3 chat windows can be opened at a given time. Please close other chat windows to allow this window to open."});
            return;
        }

        var cardHeader = 'secondary';
        if (user.status == 1) {
            cardHeader = 'success';
        } else if (user.status == 2) {
            cardHeader = 'warning';
        } else if (user.status == 3) {
            cardHeader = 'danger';
        }

        $('#messenger-windows').append(
            '<div id="messenger-window-' + user.user + '" class="messenger-window" style="position: fixed;right: ' + fromLeft + 'px;bottom: ' + fromBottom + 'px;">' +
                '<div id="messenger-card-' + user.user + '" data-user="' + user.user + '" class="card card-' + cardHeader + ' rounded-0 direct-chat direct-chat-info">' +
                    '<div class="card-header rounded-0" style="min-width: 270px;">' +
                        '<h3 class="card-title text-truncate">' + user.name + '</h3>' +
                        '<div class="card-tools">' +
                            '<span class="badge badge-light mr-2 messenger-counter-' + user.user + '"></span>' +
                            '<button type="button" class="btn btn-tool" data-card-widget="collapse" data-animationspeed="0">' +
                                '<i class="fas fa-fw fa-minus"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-tool" data-card-widget="remove">' +
                                '<i class="fas fa-fw fa-times"></i>'+
                            '</button>' +
                        '</div>' +
                    '</div>' +
                    '<div class="card-body rounded-0" style="width: 473px">' +
                        '<div id="direct-chat-messages-' + user.user + '" class="direct-chat-messages">' +
                            '<div class="row m-2" id="messenger-loader-' + user.user + '">' +
                            '    <div class="col">' +
                            '        <div class="fa-2x">' +
                            '            <i class="fa fa-cog fa-spin"></i> Loading Messages...' +
                            '        </div>' +
                            '    </div>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                    '<div class="card-footer rounded-0">' +
                        '<div class="input-group emoji-picker-container">' +
                            '<textarea data-emojiable="true" data-emoji-input="unicode" type="text" autocomplete="off" rows="1" style="resize: none;" name="message" placeholder="Type Message ..." class="form-control messenger-input-' + user.user + '"></textarea>' +
                            '<span class="input-group-append">' +
                                '<button data-action="add" data-msgid="" type="button" class="btn btn-primary messenger-send-' + user.user + '">' +
                                    '<i class="fab fa-fw fa-telegram-plane"></i>'+
                                '</button>' +
                            '</span>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>'
        );

        window.dataCollection.env.messenger.emojiPicker.discover();

        $("#messenger-card-" + user.user).on('collapsed.lte.cardwidget', function(e) {
            expandCollapse(e, false);
        });

       $("#messenger-card-" + user.user).on('expanded.lte.cardwidget', function(e) {
            expandCollapse(e, true);
        });

        function expandCollapse(e, expand) {
            var numberOfWindows = $('.messenger-window').length;

            if (numberOfWindows > 1) {
                var windowObj = { };
                $('.messenger-window').each(function(index, windowId) {
                    windowObj[windowId.id] = index + 1;
                });
                var windowPosition = windowObj[$(e.currentTarget).parent()[0].id];

                for (var leftWindow in windowObj) {
                    if (windowObj[leftWindow] > windowPosition) {
                        var rightVal = parseInt($('#' + leftWindow).css('right'));
                        if (expand) {
                            $('#' + leftWindow).css('right', rightVal + 202);
                        } else {
                            $('#' + leftWindow).css('right', rightVal - 202);
                        }
                    }
                }
            }
        }

        $("#messenger-card-" + user.user).on('removed.lte.cardwidget', function(e) {
            var currentMessengerWindows = $('.main-footer .messenger-window').length;

            if (currentMessengerWindows === 1) {
                $(e.currentTarget).parents('#messenger-windows').remove();
            } else {
                $(e.currentTarget).parent().remove();
            }
        });

        $('.messenger-input-' + user.user).keypress(function(e) {
            if (e.keyCode === 13 && !e.shiftKey) {
                e.preventDefault();

                if ($('div.messenger-input-' + user.user).html() !== '') {
                    var message = $('div.messenger-input-' + user.user).html();

                    $('div.messenger-input-' + user.user).html('');

                    sendMessage(user, message);
                }
            }
        });

        $('.messenger-send-' + user.user).click(function(e) {
            e.preventDefault();

            if ($('div.messenger-input-' + user.user).html() !== '') {
                var message = $('div.messenger-input-' + user.user).html();

                $('div.messenger-input-' + user.user).html('');

                sendMessage(user, message);
            }
        });

                            // '<div class="direct-chat-msg">'
                            //     '<div class="direct-chat-infos clearfix">' +
                            //         <span class="direct-chat-name float-left">Alexander Pierce</span>
                            //         <span class="direct-chat-timestamp float-right">23 Jan 2:00 pm</span>
                            //     </div>
                            //     <img class="direct-chat-img" src="{{users['portrait']}}" alt="message user image">
                            //     <div class="direct-chat-text">
                            //         Is this template really for free? That's unbelievable!
                            //     </div>
                            // </div>
                            // <div class="direct-chat-msg right">
                            //     <div class="direct-chat-infos clearfix">
                            //         <span class="direct-chat-name float-right">Sarah Bullock</span>
                            //         <span class="direct-chat-timestamp float-left">23 Jan 2:05 pm</span>
                            //     </div>
                            //     <img class="direct-chat-img" src="{{users['portrait']}}" alt="message user image">
                            //     <div class="direct-chat-text">
                            //         You better believe it!
                            //     </div>
                            // </div>
    }

    function sendMessage(user, message) {
        var action = $('.messenger-send-' + user.user).data('action');
        var url;

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user.user;
        postData['message'] = message;

        if (action === 'add') {
            url = dataCollection.env.rootPath + dataCollection.env.appRoute + '/system/messenger/add';
        } else if (action === 'update') {
            postData['id'] = $('.messenger-send-' + user.user).data('msgid');
            url = dataCollection.env.rootPath + dataCollection.env.appRoute + '/system/messenger/update';
        }


        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                $('#messenger-loader-' + user.user).attr('hidden', true);

                if (action === 'add') {
                    $('#direct-chat-messages-' + user.user).append(
                        '<div id="messenger-message-' + response.responseData.id + '" data-messageid="' + response.responseData.id + '" class="direct-chat-msg right">' +
                        '    <div class="direct-chat-infos clearfix">' +
                        '        <span class="direct-chat-name float-right"></span>' +
                        '        <span class="direct-chat-timestamp float-left">' +
                        '           <span>' + moment().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                        '           <a href="#" class="messenger-message-tools messenger-message-edit" hidden>' +
                        '               <i class="fas fa-fw fa-edit text-warning"></i>' +
                        '           </a>' +
                        '           <a href="#" class="messenger-message-tools messenger-message-edit-cancel" hidden>' +
                        '               <i class="fas fa-fw fa-times-circle text-danger"></i>' +
                        '           </a>' +
                        '           <a href="#" class="messenger-message-tools messenger-message-remove" hidden>' +
                        '               <i class="fas fa-fw fa-trash text-danger"></i>' +
                        '           </a>' +
                        '        </span>' +
                        '    </div>' +
                        '    <img class="direct-chat-img" src="' + window.dataCollection.env.profile.portrait + '" alt="message user image">' +
                        '    <div class="direct-chat-text">' + message + '</div>' +
                        '</div>'
                    );

                    messageHover(response.responseData.id);

                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit').click(function(e) {
                        e.preventDefault();

                        $('#messenger-message-' + response.responseData.id).off();

                        $('div.messenger-input-' + user.user)
                            .html($(this).parents('.direct-chat-infos').siblings('.direct-chat-text').html());

                        $(this).attr('hidden', true);
                        $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit-cancel').attr('hidden', false);
                        $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit-cancel').off();
                        $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit-cancel').click(function(e) {
                            e.preventDefault();

                            $(this).attr('hidden', true);
                            $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit').attr('hidden', false);
                            messageHover(response.responseData.id);

                            $('div.messenger-input-' + user.user).empty();

                            $('.messenger-send-' + user.user).removeClass('btn-warning').addClass('btn-primary');
                            $('.messenger-send-' + user.user).data('action', 'add');
                            $('.messenger-send-' + user.user).data('msgid', '');
                        });

                        $('.messenger-send-' + user.user).removeClass('btn-primary').addClass('btn-warning');
                        $('.messenger-send-' + user.user).data('action', 'update');
                        $('.messenger-send-' + user.user).data('msgid', response.responseData.id);
                    });

                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-remove').click(function(e) {
                        e.preventDefault();

                        var url = dataCollection.env.rootPath + dataCollection.env.appRoute + '/system/messenger/remove';

                        var postData = { };
                        postData[$('#security-token').attr('name')] = $('#security-token').val();
                        postData['id'] = response.responseData.id;

                        $.post(url, postData, function(response) {
                            if (response.tokenKey && response.token) {
                                $('#security-token').attr('name', response.tokenKey);
                                $('#security-token').val(response.token);
                            }
                            if (response.responseCode == 0) {
                                $('#messenger-message-' + postData['id'] + ' .direct-chat-timestamp span').html(moment().format('MMMM Do YYYY, h:mm:ss a'));
                                $('#messenger-message-' + postData['id'] + ' .direct-chat-text').html('Message Removed');
                                $('#messenger-message-' + postData['id'] + ' .messenger-message-edit-cancel').attr('hidden', true);
                                $('#messenger-message-' + postData['id'] + ' .messenger-message-edit').attr('hidden', true);
                                $('#messenger-message-' + postData['id'] + ' .messenger-message-remove').attr('hidden', true);
                                $('#messenger-message-' + postData['id']).off();
                            } else {
                                PNotify.error({
                                    text        : response.responseMessage,
                                    textTrusted : true
                                });
                            }
                        }, 'json');
                    });
                } else if (action === 'update') {
                    $('#messenger-message-' + response.responseData.id + ' .direct-chat-timestamp span').html(moment().format('MMMM Do YYYY, h:mm:ss a') + ' (Edited)');
                    $('#messenger-message-' + response.responseData.id + ' .direct-chat-text').html(message);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit-cancel').attr('hidden', true);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit').attr('hidden', true);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-remove').attr('hidden', true);
                    messageHover(response.responseData.id);

                    $('.messenger-send-' + user.user).removeClass('btn-warning').addClass('btn-primary');
                    $('.messenger-send-' + user.user).data('action', 'add');
                    $('.messenger-send-' + user.user).data('msgid', '');
                }
            } else {
                PNotify.error({
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function messageHover(id) {
        $('#messenger-message-' + id).hover(
            function() {
                $('#messenger-message-' + id + ' .messenger-message-edit').attr('hidden', false);
                $('#messenger-message-' + id + ' .messenger-message-remove').attr('hidden', false);
            },
            function() {
                $('#messenger-message-' + id + ' .messenger-message-edit').attr('hidden', true);
                $('#messenger-message-' + id + ' .messenger-message-remove').attr('hidden', true);
            }
        );
    }

    function serviceOffline() {
        if (!initialized) {
            init();
        }

        //eslint-disable-next-line
        console.log('serviceOffline');
        $("#messenger-online").attr('hidden', true);
        $("#messenger-offline").attr('hidden', false);
        $('#messenger-offline-icon').attr('hidden', false);
        $('#messenger-button-icon').removeClass('text-success text-warning text-danger text-secondary').addClass('text-secondary');
    }

    function onMessage(message) {
        if (message.responseCode === 0) {
            if (message.responseData.type === 'statusChange') {
                userStatusChange(message.responseData.data);
            }
        }
    }

    function userStatusChange(data) {
        var color, text;

        $('#messenger-users li').each(function(index, li) {
            if ($(li).data('type') === 'user' && $(li).data('user') == data.id) {
                var parentUl = $(li).parent('ul')[0].id;

                if (data.status != 4) {
                    if (data.status == 1) {
                        color = 'success';
                        text = 'Available';
                    } else if (data.status == 2) {
                        color = 'warning';
                        text = 'Away';
                    } else if (data.status == 3) {
                        color = 'danger';
                        text = 'Busy';
                    }
                    if (parentUl === 'messenger-offline-users') {
                        $('#messenger-online-users').append($(li));
                    }
                } else if (data.status == 4) {
                    color = 'secondary';
                    text = 'Offline';
                    if (parentUl === 'messenger-online-users') {
                        $('#messenger-offline-users').append($(li));
                    }
                }
                $(li).data('status', data.status);
                $(li).attr('data-status', data.status);
                $('#messenger-user-' + data.id + '-icon')
                    .removeClass('text-success text-secondary text-warning text-danger')
                    .addClass('text-' + color);
            }
        });

        if ($('#messenger-card-' + data.id).length > 0) {
            $('#messenger-card-' + data.id)
                .removeClass('card-success card-secondary card-warning card-danger')
                .addClass('card-' + color);

            $('#messenger-loader-' + data.id).attr('hidden', true);
            $('#direct-chat-messages-' + data.id).append(
                '<div class="direct-chat-infos clearfix">' +
                '   <span class="direct-chat-name float-right">' + text + '</span>' +
                '   <span class="direct-chat-timestamp float-left">' + moment().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                '</div>'
            );
        }
    }

    //Init
    function getNotificationsCount() {
        // dataCollection = window.dataCollection;
    }

    function bazMessengerConstructor() {
        // if something needs to be constructed
        return null;
    }

    function setup(BazMessengerConstructor) {
        BazMessenger = BazMessengerConstructor;
        BazMessenger.defaults = { };
        BazMessenger.init = function(options) {
            init(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.serviceOnline = function(options) {
            serviceOnline(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.serviceOffline = function(options) {
            serviceOffline(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.onMessage = function(options) {
            onMessage(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.getNotificationsCount = function(options) {
            getNotificationsCount(_extends(BazMessenger.defaults, options));
        }
    }

    setup(bazMessengerConstructor);

    return bazMessengerConstructor;
}();