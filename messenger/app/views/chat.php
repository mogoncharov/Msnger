<?php 
    // session_start();
    // var_dump(($_SESSION)); die();
    if(!isset($_SESSION['user'])){
        header("location: /login");
    }
?>

<div class="wrapper">
    <section class="users" id="users">
        <header>
            <div class="content">
                <?php 
                    echo "<img src=\"" . $_SESSION['user']['photo_file'] . "\" alt=\"\">";
                ?>
                <div class="details">
                    <span><?php echo $_SESSION['user']['nickname']; ?></span>
                </div>
            </div>
            <i class='fas fa-cog' id="change_data" style='font-size:30px; cursor: pointer;'></i>

            <a href="user/logout/<?php echo $_SESSION['user']['id']; ?>" class="logout">Выйти</a>

        </header>
        <?php 
                if(isset($_SESSION['errors'])) {
                    echo ' <div class="error-text">' . $_SESSION['errors'] . '</div>';
                }
            ?>
        <div class="change-user-data" hidden>

            <form action="/user/changeInfo" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="mb-3">
                    <label for="nickname" class="form-label">Сменить ник</label>
                    <input type="text" class="form-control" name="nickname">
                </div>
                <div class="mb-3">
                    <label for="avatar" class="form-label">Сменить аватар</label>
                    <input type="file" name="avatar" accept="image/x-png,image/gif,image/jpeg,image/jpg">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="hideEmail" name="hideEmail" value="hide">
                    <label class="form-check-label" for="hideEmail" >Скрыть свой email</label>
                </div>
                <input type="text" class="incoming_id" name="user_id" value="<?php echo $_SESSION['user']['id']; ?>" hidden>
                <button type="submit" class="btn btn-primary">Подтвердить</button>
            </form>
        </div>
        <div class="create-group-chat" hidden>
            <form action="/chat/createGroupeChat" method="POST" enctype="multipart/form-data" autocomplete="off">
                <div class="mb-3">
                    <label for="chat_name" class="form-label">Название чата</label>
                    <input type="text" class="form-control" name="chat_name" required>
                </div>
                <div class="mb-3">
                <label for="chat_name" class="form-label" id="select_users">Выберите участников</label>
                <select size="3" class="form-select" name="users_list[]" id="users_list" multiple hidden required>
                    <option disabled>CTRL для множественного выбора</option>
                </select>
                </div>
                <input type="text" class="incoming_id" name="user_id" value="<?php echo $_SESSION['user']['id']; ?>" hidden>
                <button type="submit" class="btn btn-primary">Подтвердить</button>
            </form>
        </div>
        <div class="search">
            <span class="text">Поиск собеседников</span>
            <input type="text" placeholder="Введите имя или почту">
            <button><i class="fas fa-search"></i></button>
        </div>
        <div class="users-search">
            
        </div>
        <div id="group_chat">Создать групповой чат</div>
        <div class="contacts">
            <div id="contacts_list">Личные чаты</div>
            <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $_SESSION['user']['id']; ?>" hidden>
            <div id="contacts" class="chat_list" hidden></div>
            <div id="group_chats_title">Групповые чаты</div>
            <div id="group_chats" class="chat_list" hidden></div>
        </div>
        <div id="contextMenu_message" class="context-menu" style="display: none;">
            <ul class="menu">
                <li class="edit_message"><span>Редактировать сообщение</span></li>
                <li class="delete_message"><span>Удалить сообщение</span></li>
                <li class="transfer_message"><span>Переслать сообщение</span></li>
            </ul>
        </div>
    </section>
</div>
<div class="wrapper" style="margin-left: 20px; display: none;" id="popup">
    <section class="users" id="users">
        <div class="popup" >
            <div id="popup_title">Список чатов</div>
            <div id="popup_chat_list"></div>
        </div>
    </section>
</div>

<div class="wrapper" style="margin-left: 20px; display: none;" id="wrapper">
    <section class="chat-area">
    <header>
        <div class="back-icon"><i class="fas fa-arrow-left"></i></div>
        <img class="chat_img" src="" alt="">

        <div class="details" style="width: 300px;display: flex;justify-content: space-between;">
            <span class="chat_nickname"></span>
        </div>
    </header>
    <div class="chat-box">

    </div>
    <form action="#" class="typing-area">
        <input type="text" class="chat_id" name="chat_id" value="" hidden>
        <input type="text" name="message" class="input-field" id="message" placeholder="Type a message here..." autocomplete="off">
        <button class="send"><i class="fab fa-telegram-plane" id="send_message"></i></button>
    </form>
    </section>
</div>