const form = document.querySelector(".typing-area"),
incoming_id = document.querySelector(".incoming_id"),
inputField = document.querySelector(".input-field"),
sendBtn = document.querySelector(".typing-area button"),
chatBox = document.querySelector(".chat-box");
const searchBar = document.querySelector(".search input"),
searchIcon = document.querySelector(".search button"),
usersList = document.querySelector(".users-search"),
users = document.querySelector(".details");

$("#contacts_list").on('click', function() {
    if ($("#contacts").is(':visible')) {
        $("#contacts").attr('hidden', true);
        return;
    }
    if ($("#contacts").is(':hidden')) {
        $("#contacts").attr('hidden', false);
    }
        $.ajax({
            url: '/chat/getSingleChats',
            method: 'POST',
            data: {user_id: $('.incoming_id').val(), action: "get_chats"},
            success: function(msg) {
                $('#contacts').empty();
                msg = JSON.parse(msg);
                console.log(msg);
                msg.forEach(function(item) {
                    soundsIcon = 'assets/img/icons/volume_up.svg';
                    if(item.sound === 'off') {
                        soundsIcon = 'assets/img/icons/volume_off.svg';
                    }
                    img = 'assets/img/default/avatar-default.png';
                    if(item.logo_chat) {
                        img = item.logo_chat;
                    }
                    chat = '<div class="open_chat" ><div class="content" ><img src="' + img +'" alt=""><div class="details" style="width: 300px;display: flex;justify-content: space-between;"><span id="' + item.chat_id + '/' + item.id + '" title="Открыть чат">' + item.chat_name  + '</span><img class="sound" id="' + item.chat_id + '/' + item.id + '" src="' + soundsIcon + '"></div></div></div>'
                    $("#contacts").append(chat);
                });
            }, error:function () {
                console.log('error');
            }
    });
});
$("#group_chats_title").on('click', function() {
    if ($("#group_chats").is(':visible')) {
        $("#group_chats").attr('hidden', true);
        return;
    }
    if ($("#group_chats").is(':hidden')) {
        $("#group_chats").attr('hidden', false);
    }
        $.ajax({
            url: '/chat/getGroupChats',
            method: 'POST',
            data: {user_id: $('.incoming_id').val(), action: "get_chats"},
            success: function(msg) {
                $('#group_chats').empty();
                msg = JSON.parse(msg);
                msg.forEach(function(item) {
                    soundsIcon = 'assets/img/icons/volume_up.svg';
                    if(item.sound === 'off') {
                        soundsIcon = 'assets/img/icons/volume_off.svg';
                    }
                    console.log(item);
                    console.log(soundsIcon);
                    chat = '<div class="open_chat"><div class="content" ><img src="' + item.logo_chat +'" alt=""><div class="details" style="width: 300px;display: flex;justify-content: space-between;"><span id="' + item.chat_id + '" title="Открыть чат">' + item.chat_name  + '</span><img class="sound" id="' + item.chat_id + '" src="' + soundsIcon + '"></div></div></div>'
                    $("#group_chats").append(chat);
                });
            }, error:function () {
                console.log('error');
            }
    });
});
$("#change_data").on('click', function() {
    if ($(".change-user-data").is(':visible')) {
        $(".change-user-data").attr('hidden', true);
        return;
    }
    if ($(".change-user-data").is(':hidden')) {
        $(".change-user-data").attr('hidden', false);
    }
});
$("#group_chat").on('click', function() {
    if ($(".create-group-chat").is(':visible')) {
        $(".create-group-chat").attr('hidden', true);
        return;
    }
    if ($(".create-group-chat").is(':hidden')) {
        $(".create-group-chat").attr('hidden', false);
    }
});
$("#select_users").on('click', function() {
    if ($('#users_list').is(':visible')) {
        $('#users_list').attr('hidden', true);
        $('#users_list').empty();
        return;
    }
    if ($('#users_list').is(':hidden')) {
        $('#users_list').attr('hidden', false);
    }
    $.ajax({
        url: '/user/getContacts',
        method: 'POST',
        data: {user_id: $('.incoming_id').val()},
        success: function(msg) {
            msg = JSON.parse(msg);
            if(msg.status == "ok") {
                $('#users_list').attr('hidden', false);
                msg.data.forEach(function(item) {
                    user_name = item.email;
                    if(item.nickname) {
                        user_name = item.nickname;
                    }
                    $('#users_list').append('<option value="' + item.id + '">' + user_name + '</option>');
                });
            }
        }, error:function () {
            console.log('error');
        }
    });
});
searchIcon.onclick = ()=>{
    $("#contacts").attr('hidden', true);
    searchBar.classList.toggle("show");
    searchIcon.classList.toggle("active");
    searchBar.focus();
    if(searchBar.classList.contains("active")){
        searchBar.value = "";
        searchBar.classList.remove("active");
        $(".users-search").empty();
    }
}
searchBar.onkeyup = ()=>{
    let searchTerm = searchBar.value;
    if(searchTerm != ""){
        searchBar.classList.add("active");
    }else{
        searchBar.classList.remove("active");
    }
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "user/searchUsers", true);
    xhr.onload = ()=>{
        if(xhr.readyState === XMLHttpRequest.DONE){
            if(xhr.status === 200){
                let data = xhr.response;
                usersList.innerHTML = data;
            }
        }
    }
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("searchTerm=" + searchTerm);
}

form.onsubmit = (e)=>{
    e.preventDefault();
}

inputField.focus();
inputField.onkeyup = ()=>{
    if(inputField.value != ""){
        sendBtn.classList.add("active");
    }else{
        sendBtn.classList.remove("active");
    }
}
function showMessage(messageHTML) {
    $('.chat-box').append(messageHTML);
}
    var websocket = new WebSocket("ws://localhost:8090/server.php"); 
    websocket.onopen = function(event) { 
        showMessage("<div class='chat-connection-ack'>Подключение установлено!</div>");		
    }
    websocket.onmessage = function(event) {
        var Data = JSON.parse(event.data);
        console.log(Data);
        if($('.chat_id').val() == Data.chat_id) {
            showMessage("<div class='chat-messages' id='" + Data.message_id + "," + Data.chat_id + "'>" + Data.nickname + ": " + Data.message + "</div>");
            $('#message').val('');
            console.log(Data);
            console.log($('.incoming_id').val());
            if($(".sound_status").val() === 'on' && Data.user_id != $('.incoming_id').val()) {
                var audio = new Audio;
                audio.src = "assets/audio/message.wav";
                audio.play();
            }
        }
    };
    
    websocket.onerror = function(event){
        showMessage("<div class='error'>Ошибка!</div>");
    };
    websocket.onclose = function(event){
        showMessage("<div class='chat-connection-ack'>Подключение закрыто!</div>");
    }; 
    
    $('#wrapper').on("submit", '.typing-area', function(event){
        event.preventDefault();
        $('#incoming_id').attr("type","hidden");		
        var messageJSON = {
            chat_user: $('.incoming_id').val(),
            chat_id: $('.chat_id').val(),
            chat_message: $('#message').val()
        };
        websocket.send(JSON.stringify(messageJSON));
    });

sendBtn.onclick = ()=>{
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "chat/", true);
    xhr.onload = ()=>{
      if(xhr.readyState === XMLHttpRequest.DONE){
          if(xhr.status === 200){
              inputField.value = "";
              scrollToBottom();
          }
      }
    }
    let formData = new FormData(form);
    xhr.send(formData);
}
chatBox.onmouseenter = ()=>{
    chatBox.classList.add("active");
}

chatBox.onmouseleave = ()=>{
    chatBox.classList.remove("active");
}
$(".chat_list").on("click", '.details', function(e) {
    document.getElementById("wrapper").style.display = "block";
    if(e.target.id) {
        $.ajax({    
            url: '/chat/openChat',
            method: 'POST',
            data: {open_chat: e.target.id, user_id: $('.incoming_id').val()},
            success: function(msg) {
                // console.log(msg); return;
                msg = JSON.parse(msg);
                if(msg.status == "ok") {
                    $(".chat_img").attr('src', msg.data.photo_file);
                    $(".chat_nickname").text(msg.data.nickname);
                    $(".chat_id").remove();
                    $(".typing-area").append('<input type="text" class="chat_id" name="chat_id" value="' + msg.data.chat_id + '" hidden>');
                    soundsIcon = 'assets/img/icons/volume_up.svg';
                    if(msg.data.sound === 'off') {
                        soundsIcon = 'assets/img/icons/volume_off.svg';
                    }
                    $(".sound_status").remove();
                    $(".typing-area").append('<input type="text" class="sound_status" name="sound_status" value="' + msg.data.sound + '" hidden>');
                    let timerId = setInterval(function() {
                        if (document.getElementById('wrapper')) {
                            $('.chat-box').empty();
                            $.ajax({
                                url: '/chat/getMessages',
                                method: 'POST',
                                data: {chat_id: $('.chat_id').val()},
                                success: function(msg) {
                                    msg = JSON.parse(msg);
                                    if(msg.status == "ok") {
                                        msg.data.forEach(function(item) {
                                            showMessage("<div class='chat-messages' id='" + item.id + "," + item.chat_id + "'>" + item.nickname + ": " + item.message + "</div>");
                                        });
                                        scrollToBottom();
                                    }
                                }, error:function () {
                                    console.log('error');
                                }
                            }) ;
                            clearInterval(timerId);
                        }
                    }, 500);
                }
            }, error:function () {
                console.log('error');
            }
        });
    }
    document.getElementById("popup").style.display = "none";		
});
$(".users-search").on("click", '.details', function(e) {
    document.getElementById("wrapper").style.display = "block";
    $.ajax({    
        url: '/chat/openChat',
        method: 'POST',
        data: {open_chat: e.target.id, user_id: $('.incoming_id').val()},
        success: function(msg) {
            msg = JSON.parse(msg);
            if(msg.status == "ok") {
                $(".chat_img").attr('src', msg.data.photo_file);
                $(".chat_nickname").text(msg.data.nickname);
                $(".chat_id").remove();
                $(".typing-area").append('<input type="text" class="chat_id" name="chat_id" value="' + msg.data.chat_id + '" hidden>');
                soundsIcon = 'assets/img/icons/volume_up.svg';
                if(msg.data.sound === 'off') {
                    soundsIcon = 'assets/img/icons/volume_off.svg';
                }
                $(".sound_status").remove();
                $(".typing-area").append('<input type="text" class="sound_status" name="sound_status" value="' + msg.data.sound + '" hidden>');
                let timerId = setInterval(function() {
                    if (document.getElementById('wrapper')) {
                        $('.chat-box').empty();
                        $.ajax({
                            url: '/chat/getMessages',
                            method: 'POST',
                            data: {chat_id: $('.chat_id').val()},
                            success: function(msg) {
                                msg = JSON.parse(msg);
                                if(msg.status == "ok") {
                                    msg.data.forEach(function(item) {
                                        showMessage("<div class='chat-messages' id='" + item.id + "," + item.chat_id + "'>" + item.nickname + ": " + item.message + "</div>");
                                    });
                                    scrollToBottom();
                                }
                            }, error:function () {
                                console.log('error');
                            }
                        }) ;
                        clearInterval(timerId);
                    }
                }, 500);
                
            }
        }, error:function () {
            console.log('error');
        }
    });
    document.getElementById("popup").style.display = "none";		
});
$(".back-icon").on("click", function(e) {
    document.getElementById("wrapper").style.display = "none";
});
var context = document.getElementById("contextMenu_message");
var ids = null;
var messageText = null;
$('.chat-box').on('contextmenu', '.chat-messages', function(e) {
    e.preventDefault();
    if(context.style.display == "block") {
        context.style.display = "none";
    } else {
        context.style.display = "block";
        context.style.position = "absolute";
        context.style.zIndex = "1";
        context.style.left = e.pageX + "px";
        context.style.top = e.pageY + "px";
        ids = e.target.id;
        messageText = e.target.innerText;
    }
});
$(document).on('click', function () {
    context.style.display = "none";
});
$(".edit_message").on("click", function() {
    var from = messageText.search(': '); 
    var to = messageText.length;
    newstr = messageText.substring(from + 2,to);
    $(".input-field").val(newstr);
    $(".typing-area").removeClass('typing-area').addClass('edit_send');
    $(".edit_send > button").css('color', 'green');
    $(".edit_send > button").css('background', 'yellow');
});
$(".delete_message").on("click", function() {
    $.ajax({
        url: '/chat/deleteMessage',
        method: 'POST',
        data: {ids: ids},
        success: function(msg) {
            msg = JSON.parse(msg);
            if(msg.status == "ok") {
                $('.chat-box').empty();
                msg.data.forEach(function(item) {
                    showMessage("<div class='chat-messages' id='" + item.id + "," + item.chat_id + "'>" + item.nickname + ": " + item.message + "</div>");
                });
            }
        }, error:function () {
            console.log('error');
        }
    });
});
$(".transfer_message").on("click", function(e) {
    document.getElementById("popup").style.display = "block";
    $.ajax({    
        url: '/chat/chatsList',
        method: 'POST',
        data: {user_id: $('.incoming_id').val()},
        success: function(msg) {
            $("#popup_chat_list").empty();
            msg = JSON.parse(msg);
            msg.forEach(function(item) {
                img = 'assets/img/default/forum.png';
                if(item.logo_chat) {
                    img = item.logo_chat;
                }
                chat = '<div class="user_transfer_message"><div class="content" ><img src="' + img +'" alt=""><div class="details" ><span id="' + item.chat_id + '">' + item.chat_name  + '</span></div></div></div>'
                $("#popup_chat_list").append(chat);
            });
        }, error:function () {
            console.log('error');
        }
    });
});
$("#popup_chat_list").on("click", '.user_transfer_message', function(e) {
    $.ajax({    
        url: '/chat/transferMessage',
        method: 'POST',
        data: {messageData: ids, chat_to: e.target.id},
        success: function(msg) {
            msg = JSON.parse(msg);
            if(msg.status == "ok") {
                $('.chat-box').empty();
                msg.data.forEach(function(item) {
                    showMessage("<div class='chat-messages' id='" + item.id + "," + item.chat_id + "'>" + item.nickname + ": " + item.message + "</div>");
                });
            }
        }, error:function () {
            console.log('error');
        }
    });
    document.getElementById("popup").style.display = "none";		
});

$('#wrapper').on("submit", '.edit_send', function(event){
    $.ajax({
        url: '/chat/changeMessage',
        method: 'POST',
        data: {ids: ids, chat_message: $('#message').val()},
        success: function(msg) {
            msg = JSON.parse(msg);
            if(msg.status == "ok") {
                $('.chat-box').empty();
                msg.data.forEach(function(item) {
                    showMessage("<div class='chat-messages' id='" + item.id + "," + item.chat_id + "'>" + item.nickname + ": " + item.message + "</div>");
                });
            }
        }, error:function () {
            console.log('error');
        }
    });
    $(".edit_send").removeClass('edit_send').addClass('typing-area');
    $(".typing-area > button").css('color', '#fff');
    $(".typing-area > button").css('background', '#333');

});
$(".chat_list").on("click", '.sound', function(e) {
    if($(this).attr('src') == 'assets/img/icons/volume_off.svg') {
        $(this).attr('src', 'assets/img/icons/volume_up.svg');
    } else {
        $(this).attr('src', 'assets/img/icons/volume_off.svg');
    }
    $.ajax({
        url: '/chat/changeSound',
        method: 'POST',
        data: {user_id: $('.incoming_id').val(), chat_id: e.target.id},
        success: function(msg) {

        }, error:function () {
            console.log('error');
        }
    });

});

function scrollToBottom(){
    chatBox.scrollTop = chatBox.scrollHeight;
  }
  