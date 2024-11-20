<?php 

namespace App\controllers;

use App\core\Controller;
use App\models\Model_Message;
use PDO;
use stdClass;

class Chat extends Controller
{

    public function index()
    {
        $this->view->render('chat.php', 'template.php');
    }

    public function openChat()
    {
        $data = explode('/', $_POST['open_chat']);
        $output = [];
        if(count($data) == 3) {
            $ids = [$data[0], $data[1]];
            $chat = $this->checkChat($ids);
            if(!$chat || !$chat['id']) {
                $chat = $this->createSingleChat($ids);
            }
            if(!$chat || !$chat['id']) {
                echo json_encode(["status" => "bad", "data" => ""]);
            }
        }
        if(count($data) > 1) {
            $chat = $this->getChat($data);
            $userToId = $data[1];
            $user = new User;
            $interlocator = $user->getUser($userToId);
            if(!$interlocator) {
                echo json_encode(["status" => "bad", "data" => ""]);
            }

            $output['photo_file'] = $interlocator['photo_file'] ?? "assets/img/default/avatar-default.png";
            $output['nickname'] = $interlocator['nickname']; 
            $output['chat_id'] = $chat['id'] ?? $data[0];
            $output['sound'] = json_decode($chat['sounds'])->{$_POST['user_id']};
            $output['user_id'] = $data[1];
        } else {
            $chat = $this->getChat($data[0]);

            $output['photo_file'] = "assets/img/default/forum.png";
            $output['nickname'] = $chat['chat_name']; 
            $output['chat_id'] = $data[0];
            $output['sound'] = isset($_POST['user_id']) ? json_decode($chat['sounds'])->{$_POST['user_id']}: $chat['sounds'];
        }
        echo json_encode(["status" => "ok", "data" => $output]);
    }

    public function createSingleChat($ids)
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - createSingleChat -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        asort($ids);
        $sounds = new stdClass;
        foreach ($ids as $id) {
            $sounds->{$id} = "on";
        }
        $sql = "INSERT INTO chats (type, chat_users, sounds) VALUES ('single', :ids, :sounds)";
        $stmt = $connect->prepare($sql);
        $stmt->bindValue(":ids", implode(',', $ids));
        $stmt->bindValue(":sounds", json_encode($sounds));
        $affectedRowsNumber = $stmt->execute();
        if(!$affectedRowsNumber) {
            $_SESSION['errors'] = 'Не удалось создать чат.';
            return false;
        } else {
            return $this->checkChat($ids);
        }
    }
    public function createGroupeChat()
    {
        array_push($_POST['users_list'], $_POST['user_id']);

        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - createGroupeChat -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $sounds = new stdClass;
        foreach ($_POST['users_list'] as $id) {
            $sounds->{$id} = "on";
        }

        $sql = "INSERT INTO chats (type, chat_users, chat_name, sounds) VALUES ('group', :ids, :chat_name, :sounds)";
        $stmt = $connect->prepare($sql);
        $stmt->bindValue(":ids", implode(',', $_POST['users_list']));
        $stmt->bindValue(":chat_name", $_POST['chat_name']);
        $stmt->bindValue(":sounds", json_encode($sounds));
        $affectedRowsNumber = $stmt->execute();
        if(!$affectedRowsNumber) {
            $_SESSION['errors'] = 'Не удалось создать чат.';
            return header("Location: /chat");
        } else {
            return header("Location: /chat");
        }
    }

    public function checkChat($ids)
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - checkChat -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        asort($ids);
        $sql = "SELECT * FROM chats WHERE chat_users = :ids AND type = 'single'";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":ids" => implode(',', $ids)]);

        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if($result){
            return $result[0];
        }
        return false;
    }

    public function getMessages()
    {
        echo json_encode(Model_Message::getMessages($_POST['chat_id']));
    }

    public function getGroupChats()
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - getGroupChats -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $output = [];
        $sql = "SELECT id, chat_name, sounds FROM chats WHERE chat_users LIKE :user_id AND type = 'group'";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":user_id" => '%' . $_POST['user_id'] . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($result as $chat) {
            $groupChat = [];
            $groupChat['chat_id'] = $chat['id'];
            $groupChat['type'] = 'group';
            $groupChat['chat_name'] = $chat['chat_name'];
            $groupChat['logo_chat'] = 'assets/img/default/forum.png';
            $groupChat['sound'] = json_decode($chat['sounds'])->{$_POST['user_id']};

            $output[] = $groupChat;
        }
        if($_POST['action'] == "get_chats") {
            echo json_encode($output);
        } else {
            return $output;
        }
    }
    public function deleteMessage()
    {
        $ids = explode(',', $_POST['ids']);
        Model_Message::deleteMessage($ids[0]);

        echo json_encode(Model_Message::getMessages($ids[1]));

    }
    public function changeMessage()
    {
        $ids = explode(',', $_POST['ids']);
        $message = $_POST['chat_message'];
        
        Model_Message::changeMessage($ids[0], $message);

        echo json_encode(Model_Message::getMessages($ids[1]));

    }
    public function getSingleChats()
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - changeInfo -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $output = [];
        $sql = "SELECT id, chat_users, sounds FROM chats WHERE chat_users LIKE :user_id AND type = 'single'";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":user_id" => '%' . $_POST['user_id'] . '%']);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $contacts = [];
        foreach ($result as $item) {
            $contacts = array_merge($contacts, explode(',', $item['chat_users']));
        }
        $contacts = array_unique($contacts);
        unset($contacts[array_search($_POST['user_id'], $contacts)]);
        $contacts = implode(',', $contacts);

        $sql = "SELECT id, email, nickname, photo_file FROM users WHERE id IN ($contacts)";
        $stmt = $connect->prepare($sql);
        $stmt->execute();
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($users as $user) {
            $singleChat = [];
            $singleChat['id'] = $user['id'];
            $chat = array_filter($result, function($v) use ($user){
                return str_contains($v['chat_users'], $user['id']);
            });
            $chat = array_values($chat);

            $singleChat['chat_id'] = $chat[0]['id'];
            $singleChat['type'] = 'single';
            $singleChat['chat_name'] = $user['nickname'];
            $singleChat['logo_chat'] = $user['photo_file'];
            $singleChat['sound'] = json_decode($chat[0]['sounds'])->{$_POST['user_id']};

            $output[] = $singleChat;
        }
        if(isset($_POST['action']) &&  $_POST['action'] == "get_chats") {
            echo json_encode($output);
        } else {
            return $output;
        }
    }
    public function chatsList()
    {
        $output = $this->getSingleChats($_POST);
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - changeInfo -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }

        $sql = "SELECT id as chat_id, chat_name, type FROM chats WHERE chat_users LIKE :user_id AND type = 'group'";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":user_id" => '%' . $_POST['user_id'] . '%']);
        $groupChat = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if($groupChat) {
            $output = array_merge($output, $groupChat);
        }

        echo json_encode($output);
    }

    public function transferMessage()
    {
        $ids = explode(',', $_POST['messageData']);
        $chat_to = $_POST['chat_to'];

        Model_Message::transferMessage($ids[0], $chat_to);

        echo json_encode(Model_Message::getMessages($ids[1]));
    }

    public function getChat($chatId)
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - getChat -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        if(!is_array($chatId) || count($chatId) != 3) {
            if(is_array($chatId) && count($chatId) == 2) {
                $chatId = $chatId[0];
            }
            $sql = "SELECT id, chat_name, sounds FROM chats WHERE id = :chat_id";
            $stmt = $connect->prepare($sql);
            $stmt->execute([":chat_id" => $chatId]);
        } else {
            unset($chatId[2]);
            asort($chatId);
            $sql = "SELECT id, chat_name, sounds FROM chats WHERE chat_users = :chat_users";
            $stmt = $connect->prepare($sql);
            $stmt->execute([":chat_users" => implode(',', $chatId)]);
        }
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $result[0];
    }
    public function changeSound()
    {
        $ids = explode('/', $_POST['chat_id']);
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (PDOException $e) {
            $message = date("H:i:s") . ' - changeSound -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $sql = "SELECT sounds FROM chats WHERE id = :chat_id";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":chat_id" => $ids[0]]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $sounds = json_decode($result['sounds']);
        if($sounds->{$_POST['user_id']} == 'on') {
            $sounds->{$_POST['user_id']} = 'off';
        } else {
            $sounds->{$_POST['user_id']} = 'on';
        }
        $sound = $sounds->{$_POST['user_id']};
        $sql = "UPDATE chats SET sounds = :sounds WHERE id = :chat_id";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":chat_id" => $ids[0], ":sounds" => json_encode($sounds)]);

        echo $sound;
    }
}