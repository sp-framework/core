/* exported BazMessenger */
/* globals paginatedPNotify EmojiPicker autoComplete dayjs Swal */
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
    var appRoute;
    // Error
    // function error(errorMsg) {
    //     throw new Error(errorMsg);
    // }

    //Init
    function init(initialConnection = true) {
        initialized = true;

        dayjs.extend(window.dayjs_plugin_advancedFormat);

        dataCollection = window.dataCollection;

        if (dataCollection.env.appRoute !== '') {
            appRoute = dataCollection.env.appRoute + '/'
        } else {
            appRoute = '';
        }

        dataCollection.env.wsTunnels.messenger['emojiPicker'] = new EmojiPicker({
            emojiable_selector: '[data-emojiable=true]',
            assetsPath: '/core/default/images/emoji-picker/',
            popupButtonClasses: 'fa fa-fw fa-smile',
        });

        dataCollection.env.wsTunnels.messenger.search =
            new autoComplete({
                data: {
                    src: async() => {
                        const url = dataCollection.env.rootPath + appRoute + 'system/messenger/searchAccount';

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
                        //eslint-disable-next-line
                        console.log(response.responseData.accounts);
                        if (response.responseData.accounts) {
                            return response.responseData.accounts;
                        } else {
                            return [];
                        }
                    },
                    key: ["name"],
                    cache: false
                },
                selector: "#messenger-main-search",
                threshold : 3,
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
                    element: "div",
                    className: "autoComplete_results"
                },
                maxResults: 5,
                highlight: true,
                resultItem: {
                    content: (data, source) => {
                        //eslint-disable-next-line
                        console.log(data, source);
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
                onSelection: (feedback) => {
                    $('#messenger-main-search').val('');
                    $('#messenger-main-search').blur();
                    //eslint-disable-next-line
                    console.log(feedback.selection.value);
                    messengerWindow(feedback.selection.value);
                    addUserToMembersUsers(feedback.selection.value);
                }
            });

        if (initialConnection) {
            serviceOnline();
        }
    }

    function serviceOnline() {
        //eslint-disable-next-line
        console.log('serviceOnline');

        if (!initialized) {
            init(false);
        }

        $("#messenger-online").attr('hidden', false);
        $("#messenger-offline").attr('hidden', true);
        $('#messenger-offline-icon').attr('hidden', true);
        $('#messenger-button-icon').removeClass(function (index, className) {
            return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
        }).addClass('text-' + messengerButonIconColor);
        initListeners();
        getUnreadMessagesCount();
    }

    function serviceOffline() {
        if (!initialized) {
            init(false);
        }
    }

    function initListeners() {
        $('#messenger-main-status').off();
        $('#messenger-main-status').change(function() {
            //eslint-disable-next-line
            console.log($(this).data('status'));
            var oldStatus = $(this).data('status')

            var status = $('#messenger-main-status option:selected').val();

            if (status == 0) {
                return;
            }
            if (status != 4) {
                $('#messenger-main').attr('hidden', false);

            } else {
                $('#messenger-main').attr('hidden', true);
                $('#messenger-windows').remove();
            }

            var url = dataCollection.env.rootPath + appRoute + 'system/messenger/changestatus';

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

                    $('#messenger-button-icon').removeClass(function (index, className) {
                        return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                    }).addClass('text-' + statusTextColor);
                    messengerButonIconColor = statusTextColor;
                    if (oldStatus == '4') {
                        getUnreadMessagesCount();
                    }
                } else {
                    paginatedPNotify('error', {
                        text        : response.responseMessage,
                        textTrusted : true
                    });
                }
            }, 'json');
            $(this).data('status', status);
        });

        $('#messenger-users li').each(function(index, li) {
            $(li).off();
            $(li).click(function(e) {
                e.preventDefault();
                messengerWindow($(this).data());
            });
        });

        // On delete
        $('#messenger-main-search').off();
        $('#messenger-main-search').on('input propertychange', function() {
            if ($('#messenger-main-search').val().length === 0) {
                $('#messenger-main-search_list').children().remove();
            }
        });

        $('#messenger-main-mute').off();
        $('#messenger-main-mute').click(function(e) {
            e.preventDefault();

            var thisButton = this;
            var url = dataCollection.env.rootPath + appRoute + 'system/messenger/changesettings';

            var dataToSend = { };
            dataToSend[$('#security-token').attr('name')] = $('#security-token').val();
            dataToSend['changestate'] = 1;

            $.post(url, dataToSend, function(response) {
                if (response.tokenKey && response.token) {
                    $('#security-token').attr('name', response.tokenKey);
                    $('#security-token').val(response.token);
                }

                if (response.responseCode == 0) {
                    $(thisButton).attr('hidden', true);
                    $('#messenger-main-unmute').attr('hidden', false);
                    $('#messenger-button').children('i').removeClass('fa-comment').addClass('fa-comment-slash');
                } else {
                    paginatedPNotify('error', {
                        title           : response.responseMessage,
                    });
                }
            }, 'json');
        });

        $('#messenger-main-unmute').off();
        $('#messenger-main-unmute').click(function(e) {
            e.preventDefault();

            var thisButton = this;
            var url = dataCollection.env.rootPath + appRoute + 'system/messenger/changesettings';

            var dataToSend = { };
            dataToSend[$('#security-token').attr('name')] = $('#security-token').val();
            dataToSend['changestate'] = 0;

            $.post(url, dataToSend, function(response) {
                if (response.tokenKey && response.token) {
                    $('#security-token').attr('name', response.tokenKey);
                    $('#security-token').val(response.token);
                }

                if (response.responseCode == 0) {
                    $(thisButton).attr('hidden', true);
                    $('#messenger-main-mute').attr('hidden', false);
                    $('#messenger-button').children('i').removeClass('fa-comment-slash').addClass('fa-comment');
                } else {
                    paginatedPNotify('error', {
                        title           : response.responseMessage,
                    });
                }
            }, 'json');
        });
    }

    function messengerWindow(user) {
        $('.messenger-counter-' + $('#messenger-user-' + user.user).data('user')).html('');

        var totalCounter = 0;
        $('#messenger-users span.badge').each(function() {
            if ($(this).html() !== '') {
                totalCounter = totalCounter + parseInt($(this).html());
            }
        });

        if (totalCounter === 0) {
            $('#messenger-button-counter').html('');
        } else if (totalCounter < 10) {
            $('#messenger-button-counter').css({'right': '10px'});
            $('#messenger-button-counter').html(totalCounter);
        } else if (totalCounter < 99) {
            $('#messenger-button-counter').css({'right': '5px'});
            $('#messenger-button-counter').html(totalCounter);
        } else if (totalCounter > 99) {
            $('#messenger-button-counter').css({'right': 0});
            $('#messenger-button-counter').html('99+');
        }

        $('#messenger-button').ControlSidebar('toggle');

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
            paginatedPNotify('error', {
                text: "Only 3 chat windows can be opened at a given time. Please close other chat windows to allow this window to open."
            });
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
                    '<div class="card-header rounded-0" style="min-width: 473px;">' +
                        '<h3 class="card-title text-truncate">' + user.name + '</h3>' +
                        '<div class="card-tools">' +
                            '<span class="badge badge-light mr-2 messenger-counter-' + user.user + '"></span>' +
                            '<button type="button" class="btn btn-tool" data-card-widget="collapse" data-animationspeed="0">' +
                                '<i class="fas fa-fw fa-minus"></i>' +
                            '</button>' +
                            '<button type="button" class="btn btn-tool" data-card-widget="remove" data-animationspeed="0">' +
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
                            '<div class="row text-center text-info" id="messenger-no-messages-' + user.user + '" hidden>' +
                            '    <div class="col">' +
                            '       <i class="fas fa-fw fa-exclamation-circle"></i> No Messages. Send your first message...' +
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

        if (window.dataCollection.env.wsTunnels.messenger.emojiPicker) {
            window.dataCollection.env.wsTunnels.messenger.emojiPicker.discover();
        }

        $("#messenger-card-" + user.user).on('removed.lte.cardwidget', function(e) {
            removeCard(e);
        });

        function removeCard(e) {
            var numberOfWindows = $('.messenger-window').length;

            if (numberOfWindows > 1) {
                // var collapsedCards = $('.messenger-window .collapsed-card').length;
                var windowObj = { };
                $('.messenger-window').each(function(index, windowId) {
                    windowObj[windowId.id] = index + 1;
                });
                var windowPosition = windowObj[$(e.currentTarget).parent()[0].id];

                for (var leftWindow in windowObj) {
                    if (windowObj[leftWindow] > windowPosition) {
                        var rightVal = parseInt($('#' + leftWindow).css('right'));
                        //eslint-disable-next-line
                        console.log(rightVal);
                        // if (collapsedCards > 0) {
                        //     if ($('#' + leftWindow + ' .card-collapsed')) {
                        //         $('#' + leftWindow).css('right', rightVal - 275);
                        //     } else {
                        //         var newValue = collapsedCards * 275;
                        //         $('#' + leftWindow).css('right', rightVal - newValue);
                        //     }
                        // } else {
                            $('#' + leftWindow).css('right', rightVal - 478);
                        // }
                    }
                }
                $(e.currentTarget).parent().remove();
            } else if (numberOfWindows === 1) {
                $(e.currentTarget).parents('#messenger-windows').remove();
            } else {
                $(e.currentTarget).parent().remove();
            }
        }

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

        //Get Messages from Server for user.user
        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/getmessages';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user.user;

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                $('#messenger-loader-' + user.user).attr('hidden', true);
                populateMessages(user, response.responseData.messages, response.responseData.paginationCounters);

                markAllMessagesRead(user);
            } else {
                paginatedPNotify('error', {
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');

        $('.messenger-input-' + user.user).focus();
    }

    function populateMessages(toUser, messages, paginationCounters, update = false) {
        if (messages.length === 0) {
            $('#messenger-no-messages-' + toUser.user).attr('hidden', false);
            return;
        }
        //eslint-disable-next-line
        console.log(toUser, messages, paginationCounters, update);

        var messagesHtml = '';

        if (paginationCounters) {
            $('#direct-chat-messages-' + toUser.user).data('current', paginationCounters.current);
            $('#direct-chat-messages-' + toUser.user).data('filtered_items', paginationCounters.filtered_items);
            $('#direct-chat-messages-' + toUser.user).data('first', paginationCounters.first);
            $('#direct-chat-messages-' + toUser.user).data('last', paginationCounters.last);
            $('#direct-chat-messages-' + toUser.user).data('limit', paginationCounters.limit);
            $('#direct-chat-messages-' + toUser.user).data('next', paginationCounters.next);
            $('#direct-chat-messages-' + toUser.user).data('previous', paginationCounters.previous);
            $('#direct-chat-messages-' + toUser.user).data('total_items', paginationCounters.total_items);

            if (paginationCounters.current < paginationCounters.last) {
                messagesHtml +=
                    '<div class="row pb-2">' +
                    '   <div class="col text-center">' +
                    '       <button class="btn btn-xs btn-primary" id="messenger-messages-load-more-' + toUser.user + '" ' +
                    '           data-toggle="tooltip" data-html="true" data-placement="auto" title="" role="button" data-original-title="">' +
                    '           <i class="fas fa-fw fa-download"></i> Load More...' +
                    '       </button>' +
                    '   </div>' +
                    '</div>';
            }
        }

        if (!toUser.portrait || (toUser.portrait && toUser.portrait === '')) {
            toUser.portrait = dataCollection.env.rootPath + '/core/default/images/general/user.png';
        } else {
            toUser.portrait =
                '/' + appRoute + 'system/storages/q/uuid/' + toUser.portrait + '/w/80';
        }
        $(messages).each(function(index, message) {
            if (message.to_account_id == toUser.user) {
                messagesHtml +=
                    '<div id="messenger-message-' + message.id + '" data-messageid="' + message.id + '" class="direct-chat-msg right">' +
                    '    <div class="direct-chat-infos clearfix">' +
                    '        <span class="direct-chat-name float-right"></span>' +
                    '        <span class="direct-chat-timestamp float-left">' +
                    '           <span>' + dayjs(message.updated_at, 'YYYY-MM-DD HH:mm:ss').format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
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
                    '    <div class="direct-chat-text">' + message.message + '</div>' +
                    '</div>';
            } else {
                messagesHtml +=
                    '<div id="messenger-message-' + message.id + '" data-messageid="' + message.id + '" class="direct-chat-msg">' +
                    '    <div class="direct-chat-infos clearfix">' +
                    '        <span class="direct-chat-name float-left">' + toUser.name + '</span>' +
                    '        <span class="direct-chat-timestamp float-right">' +
                    '           <span>' + dayjs(message.updated_at, 'YYYY-MM-DD HH:mm:ss').format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                    '        </span>' +
                    '    </div>' +
                    '    <img class="direct-chat-img" src="' + toUser.portrait + '" alt="message user image">' +
                    '    <div class="direct-chat-text">' + message.message + '</div>' +
                    '</div>';
            }
        });

        if (update) {
            $('#direct-chat-messages-' + toUser.user).prepend(messagesHtml);
        } else {
            $('#direct-chat-messages-' + toUser.user).append(messagesHtml);
            $('#direct-chat-messages-' + toUser.user).scrollTop($('#direct-chat-messages-' + toUser.user).get(0).scrollHeight);
        }
        $(messages).each(function(index, message) {
            if (message.removed != 1) {
                initMessagesListeners(message.id, toUser);
                messageHover(message.id, toUser);
            }
        });

        if ($('#messenger-messages-load-more-' + toUser.user).length > 0) {
            $('#messenger-messages-load-more-' + toUser.user).off();
            $('#messenger-messages-load-more-' + toUser.user).click(function(e) {
                e.preventDefault();

                var url = dataCollection.env.rootPath + appRoute + 'system/messenger/getmessages';

                var postData = { };
                postData[$('#security-token').attr('name')] = $('#security-token').val();
                postData['user'] = toUser.user;
                postData['page'] = paginationCounters.current + 1;

                $.post(url, postData, function(response) {
                    if (response.tokenKey && response.token) {
                        $('#security-token').attr('name', response.tokenKey);
                        $('#security-token').val(response.token);
                    }
                    if (response.responseCode == 0) {
                        $('#messenger-loader-' + toUser.user).attr('hidden', true);
                        $('#messenger-messages-load-more-' + toUser.user).closest('div.row').remove();
                        populateMessages(toUser, response.responseData.messages, response.responseData.paginationCounters, true);
                    } else {
                        paginatedPNotify('error', {
                            text        : response.responseMessage,
                            textTrusted : true
                        });
                    }
                }, 'json');
            });
        }
    }

    function addUserToMembersUsers(user) {
        var color;
        if (user.status != 4) {
            if (user.status == 1) {
                color = 'success';
            } else if (user.status == 2) {
                color = 'warning';
            } else if (user.status == 3) {
                color = 'danger';
            }
        } else if (user.status == 4) {
            color = 'secondary';
        }

        if (!user.portrait || (user.portrait && user.portrait === '')) {
            user.portrait = dataCollection.env.rootPath + '/core/default/images/general/user.png';
        } else {
            user.portrait =
                '/' + appRoute + 'system/storages/q/uuid/' + user.portrait + '/w/80';
        }

        var newUser =
            '<li class="nav-item" id="messenger-user-' + user.id + '" data-status="' + user.status + '" data-type="user" data-user="' +
                user.id + '" data-name="' + user.name + '" data-portrait="' + user.portrait + '">' +
                '<a id="messenger-user-' + user.id + '-link" class="nav-link" href="#">' +
                    '<img id="messenger-user-' + user.id + '-img" src="' + user.portrait + '" class="rounded-sm" style="position:relative;top: -3px; width:20px;" alt="User Image">' +
                    '<i id="messenger-user-' + user.id + '-icon" class="fa fa-fw fa-circle text-' + color + '" style="font-size: 8px;position: absolute;top: 8px;left: 27px;"></i>' +
                    '<div id="messenger-user-' + user.id + '-name" class="text-uppercase ml-2 text-truncate" style="position:relative; top: 3px;display: inline-block;width: 150px;">' + user.name + '</div>' +
                    '<span class="badge badge-info messenger-counter-' + user.id + '" style="position: relative;top: -4px;"></span>' +
                '</a>' +
            '</li>';
        if (user.status == 4) {
            $('#messenger-offline-users').append(newUser);
        } else {
            $('#messenger-online-users').append(newUser);
        }

        initListeners();

        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/addusertomembersusers';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user;

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                //
            } else {
                paginatedPNotify('error', {
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function sendMessage(user, message) {
        // OTR (Off The Record) - This will be initiated by a user and a request is sent to the other user/users in case of group chat.
        // Once all users accept the OTR request, OTR is enabled and when messages are sent, they bypass the DB.
        // if (dataCollection.env.wsTunnels.messenger.otr && dataCollection.env.wsTunnels.messenger.otr === true) {
        //     dataCollection.env.wsTunnels.messenger.send(user, message);

        //     return;
        // }

        var action = $('.messenger-send-' + user.user).data('action');
        var url;

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user.user;
        postData['message'] = message;

        if (action === 'add') {
            url = dataCollection.env.rootPath + appRoute + 'system/messenger/add';
        } else if (action === 'update') {
            postData['id'] = $('.messenger-send-' + user.user).data('msgid');
            url = dataCollection.env.rootPath + appRoute + 'system/messenger/update';
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
                        '           <span>' + dayjs().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
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

                    initMessagesListeners(response.responseData.id, user);
                    messageHover(response.responseData.id);
                } else if (action === 'update') {
                    $('#messenger-message-' + response.responseData.id + ' .direct-chat-timestamp span').html(dayjs().format('MMMM Do YYYY, h:mm:ss a') + ' (Edited)');
                    $('#messenger-message-' + response.responseData.id + ' .direct-chat-text').html(message);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit-cancel').attr('hidden', true);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-edit').attr('hidden', true);
                    $('#messenger-message-' + response.responseData.id + ' .messenger-message-remove').attr('hidden', true);
                    messageHover(response.responseData.id);

                    $('.messenger-send-' + user.user).removeClass('btn-warning').addClass('btn-primary');
                    $('.messenger-send-' + user.user).data('action', 'add');
                    $('.messenger-send-' + user.user).data('msgid', '');
                }
                $('#messenger-no-messages-' + user.user).attr('hidden', true);
                $('#direct-chat-messages-' + user.user).scrollTop($('#direct-chat-messages-' + user.user).get(0).scrollHeight);
            } else {
                paginatedPNotify('error', {
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function initMessagesListeners(id, user) {
        $('#messenger-message-' + id + ' .messenger-message-edit').off();
        $('#messenger-message-' + id + ' .messenger-message-edit').click(function(e) {
            e.preventDefault();

            $('div.messenger-input-' + user.user)
                .html($(this).parents('.direct-chat-infos').siblings('.direct-chat-text').html());

            $(this).attr('hidden', true);
            $('#messenger-message-' + id + ' .messenger-message-edit-cancel').attr('hidden', false);
            $('#messenger-message-' + id + ' .messenger-message-edit-cancel').off();
            $('#messenger-message-' + id + ' .messenger-message-edit-cancel').click(function(e) {
                e.preventDefault();

                $(this).attr('hidden', true);
                $('#messenger-message-' + id + ' .messenger-message-edit').attr('hidden', false);
                messageHover(id);

                $('div.messenger-input-' + user.user).empty();

                $('.messenger-send-' + user.user).removeClass('btn-warning').addClass('btn-primary');
                $('.messenger-send-' + user.user).data('action', 'add');
                $('.messenger-send-' + user.user).data('msgid', '');
            });

            $('.messenger-send-' + user.user).removeClass('btn-primary').addClass('btn-warning');
            $('.messenger-send-' + user.user).data('action', 'update');
            $('.messenger-send-' + user.user).data('msgid', id);
        });

        $('#messenger-message-' + id + ' .messenger-message-remove').off();
        $('#messenger-message-' + id + ' .messenger-message-remove').click(function(e) {
            e.preventDefault();

            Swal.fire({
                title                       : '<span class="text-danger"> Delete selected message?</span>',
                icon                        : 'question',
                background                  : 'rgba(0,0,0,.8)',
                backdrop                    : 'rgba(0,0,0,.6)',
                buttonsStyling              : false,
                confirmButtonText           : 'Delete',
                customClass                 : {
                    'confirmButton'             : 'btn btn-danger text-uppercase',
                    'cancelButton'              : 'ml-2 btn btn-secondary text-uppercase',
                },
                showCancelButton            : true,
                keydownListenerCapture      : true,
                allowOutsideClick           : false,
                allowEscapeKey              : false,
                didOpen                      : function() {
                    window.dataCollection.env.sounds.swalSound.play();
                }
            }).then((result) => {
                if (result.value) {
                    var url = dataCollection.env.rootPath + appRoute + 'system/messenger/remove';

                    var postData = { };
                    postData[$('#security-token').attr('name')] = $('#security-token').val();
                    postData['id'] = id;

                    $.post(url, postData, function(response) {
                        if (response.tokenKey && response.token) {
                            $('#security-token').attr('name', response.tokenKey);
                            $('#security-token').val(response.token);
                        }
                        if (response.responseCode == 0) {
                            $('#messenger-message-' + postData['id'] + ' .direct-chat-timestamp span').html(dayjs().format('MMMM Do YYYY, h:mm:ss a'));
                            $('#messenger-message-' + postData['id'] + ' .direct-chat-text').html('Message Removed');
                            $('#messenger-message-' + postData['id'] + ' .messenger-message-edit-cancel').attr('hidden', true);
                            $('#messenger-message-' + postData['id'] + ' .messenger-message-edit').attr('hidden', true);
                            $('#messenger-message-' + postData['id'] + ' .messenger-message-remove').attr('hidden', true);
                            $('#messenger-message-' + postData['id']).off();
                        } else {
                            paginatedPNotify('error', {
                                text        : response.responseMessage,
                                textTrusted : true
                            });
                        }
                    }, 'json');
                }
            });
        });
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

    function onMessage(message) {
        //eslint-disable-next-line
        console.log(message);
        if (message.responseCode === 0) {
            if (message.responseData.type === 'statusChange') {
                userStatusChange(message.responseData.data);
            }
            if (message.responseData.type === 'newMessage') {
                userNewMessage(message.responseData.data);
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
                        text = 'Active';
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
                $('#messenger-user-' + data.id + '-icon').removeClass(function (index, className) {
                    return (className.match (/(^|\s)text-\S+/g) || []).join(' ');
                }).addClass('text-' + color);
            }
        });

        if ($('#messenger-card-' + data.id).length > 0) {
            $('#messenger-card-' + data.id).removeClass(function (index, className) {
                return (className.match (/(^|\s)card-\S+/g) || []).join(' ');
            }).addClass('card-' + color);

            $('#messenger-loader-' + data.id).attr('hidden', true);
            $('#direct-chat-messages-' + data.id).append(
                '<div class="direct-chat-infos clearfix">' +
                '   <span class="direct-chat-name float-right">' + text + '</span>' +
                '   <span class="direct-chat-timestamp float-left">' + dayjs().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                '</div>'
            );
            $('#direct-chat-messages-' + data.id).scrollTop($('#direct-chat-messages-' + data.id).get(0).scrollHeight);
        }
    }

    function userNewMessage(data) {
        if (!data.user.portrait || (data.user.portrait && data.user.portrait === '')) {
            data.user.portrait = dataCollection.env.rootPath + '/core/default/images/general/user.png';
        } else {
            data.user.portrait =
                '/' + appRoute + 'system/storages/q/uuid/' + data.user.portrait + '/w/80';
        }

        if ($('#direct-chat-messages-' + data.user.id).length > 0) {
            $('#messenger-loader-' + data.user.id).attr('hidden', true);
            $('#direct-chat-messages-' + data.user.id).append(
                '<div id="messenger-message-' + data.message.id + '" data-messageid="' + data.message.id + '" class="direct-chat-msg">' +
                '    <div class="direct-chat-infos clearfix">' +
                '        <span class="direct-chat-name float-left">' + $('#messenger-user-' + data.user.id).data('name') + '</span>' +
                '        <span class="direct-chat-timestamp float-right">' +
                '           <span>' + dayjs().format('MMMM Do YYYY, h:mm:ss a') + '</span>' +
                '        </span>' +
                '    </div>' +
                '    <img class="direct-chat-img" src="' + data.user.portrait + '" alt="message user image">' +
                '    <div class="direct-chat-text">' + data.message.message + '</div>' +
                '</div>'
            );
            $('#direct-chat-messages-' + data.user.id).scrollTop($('#direct-chat-messages-' + data.user.id).get(0).scrollHeight);
        } else {
            var currentCount;

            currentCount = $('#messenger-button-counter').html();

            if (currentCount === '99+') {
                //shake bell
                return;
            }
            if (currentCount === '') {
                currentCount = 0;
            } else {
                currentCount = parseInt(currentCount);
            }
            currentCount = currentCount + 1;

            if (currentCount < 10) {
                $('#messenger-button-counter').css({'right': '10px'});
                $('#messenger-button-counter').html(currentCount);
                shakeMessengerButton();
            } else if (currentCount < 99) {
                $('#messenger-button-counter').css({'right': '5px'});
                $('#messenger-button-counter').html(currentCount);
                shakeMessengerButton();
            } else if (currentCount > 99) {
                $('#messenger-button-counter').css({'right': 0});
                $('#messenger-button-counter').html('99+');
                shakeMessengerButton();
            }

            if ($('#messenger-user-' + data.user.id).length === 0) {
                addUserToMembersUsers(data.user);
            }

            var userCounter;

            userCounter = $('.messenger-counter-' + data.user.id).html();

            if (userCounter === '99+') {
                //shake bell
                return;
            }
            if (userCounter === '') {
                userCounter = 0;
            } else {
                userCounter = parseInt(userCounter);
            }

            userCounter = userCounter + 1;

            if (userCounter < 99) {
                $('.messenger-counter-' + data.user.id).html(userCounter);
                shakeMessengerButton();
            } else if (userCounter > 99) {
                $('.messenger-counter-' + data.user.id).html('99+');
                shakeMessengerButton();
            }
            //update counters
            //Ring Bell
            //Shake
        }
    }

    function shakeMessengerButton() {
        if ($('#messenger-button-icon').is('.fa-comment')) {
            window.dataCollection.env.sounds.messengerSound.play();
        }

        $('#messenger-button').addClass('animated tada');

        setTimeout(function() {
            $('#messenger-button').removeClass('animated tada');
        }, 10000);
    }

    //Get Unread Messages Count
    function getUnreadMessagesCount() {
        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/getUnreadMessagesCount';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                if (response.responseData && response.responseData.total) {
                    if (response.responseData.total < 10) {
                        $('#messenger-button-counter').css({'right': '10px'});
                        $('#messenger-button-counter').html(response.responseData.total);
                        shakeMessengerButton();
                    } else if (response.responseData.total < 99) {
                        $('#messenger-button-counter').css({'right': '5px'});
                        $('#messenger-button-counter').html(response.responseData.total);
                        shakeMessengerButton();
                    } else if (response.responseData.total > 99) {
                        $('#messenger-button-counter').css({'right': 0});
                        $('#messenger-button-counter').html('99+');
                        shakeMessengerButton();
                    }
                }

                if (response.responseData && response.responseData.unread_count) {
                    for (var user in response.responseData.unread_count) {
                        if (response.responseData.unread_count[user].count > 0) {
                            if (response.responseData.unread_count[user].count < 99) {
                                $('.messenger-counter-' + response.responseData.unread_count[user].id).html(response.responseData.unread_count[user].count);
                                shakeMessengerButton();
                            } else if (response.responseData.unread_count[user].count > 99) {
                                $('.messenger-counter-' + response.responseData.unread_count[user].id).html('99+');
                                shakeMessengerButton();
                            }
                        }
                    }
                }
            } else {
                paginatedPNotify('error', {
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function markAllMessagesRead(user) {
        var url = dataCollection.env.rootPath + appRoute + 'system/messenger/markAllMessagesRead';

        var postData = { };
        postData[$('#security-token').attr('name')] = $('#security-token').val();
        postData['user'] = user.user;

        $.post(url, postData, function(response) {
            if (response.tokenKey && response.token) {
                $('#security-token').attr('name', response.tokenKey);
                $('#security-token').val(response.token);
            }
            if (response.responseCode == 0) {
                //
            } else {
                paginatedPNotify('error', {
                    text        : response.responseMessage,
                    textTrusted : true
                });
            }
        }, 'json');
    }

    function bazMessengerConstructor() {
        // if something needs to be constructed
        return null;
    }

    function otrServiceOnline() {
        //
    }

    function otrServiceOffline() {
        //
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
        BazMessenger.getUnreadMessagesCount = function(options) {
            getUnreadMessagesCount(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.otrServiceOnline = function(options) {
            otrServiceOnline(_extends(BazMessenger.defaults, options));
        }
        BazMessenger.otrServiceOffline = function(options) {
            otrServiceOffline(_extends(BazMessenger.defaults, options));
        }
    }

    setup(bazMessengerConstructor);

    return bazMessengerConstructor;
}();