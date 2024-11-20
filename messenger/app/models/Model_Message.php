<?php

namespace App\models;

use App\controllers\SQLiteConnection;
use PDO;

class Model_Message
{
    public static function createMessage($data)
    {
        $chatObj = json_decode($data);
        $userId = $chatObj->chat_user ?? null;
        $chatId = $chatObj->chat_id ?? null;
        $message = $chatObj->chat_message ?? null;
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (\PDOException $e) {
            $message = date("H:i:s") . ' - createMessage -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $sql = "INSERT INTO messages (incoming_user_id, chat_id, message) VALUES (:user_id, :chat_id, :message)";
        $stmt = $connect->prepare($sql);
        $stmt->bindValue(":user_id", $userId);
        $stmt->bindValue(":chat_id", $chatId);
        $stmt->bindValue(":message", $message);
        $stmt->execute();
    }

    public static function getMessages($chat_id)
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (\PDOException $e) {
            $message = date("H:i:s") . ' - getMessages -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $sql = "SELECT m.id, m.incoming_user_id, m.chat_id, m.message, u.nickname, u.email FROM messages as m LEFT JOIN users as u ON m.incoming_user_id = u.id WHERE m.chat_id = :chat_id ORDER BY m.created_at";
        $stmt = $connect->prepare($sql);
        $stmt->bindValue(':chat_id', $chat_id);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return ["status" => "ok", "data" => $result];
    }

    public static function deleteMessage($messageId)
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (\PDOException $e) {
            $message = date("H:i:s") . ' - deleteMessage -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $sql = "DELETE FROM messages WHERE id = :id";
        $stmt = $connect->prepare($sql);
        $stmt->bindValue(':id', $messageId);
        $stmt->execute();
    }

    public static function changeMessage($messageId, $message)
    {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (\PDOException $e) {
            $message = date("H:i:s") . ' - changeMessage -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $sql = "UPDATE messages SET message = :message  WHERE id = :id";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":message" => $message, ":id" => $messageId]);
        $stmt->execute();
    }

    public static function transferMessage($messageId, $chat_to) {
        try {
            $connect = (new SQLiteConnection())->connect();
        }
        catch (\PDOException $e) {
            $message = date("H:i:s") . ' - changeMessage -' . $e->getMessage() . "\n";
            file_put_contents(date("Y-m-d") . "errors.txt", $message, FILE_APPEND);
        }
        $sql = "UPDATE messages SET chat_id = :chat_id, created_at = :date WHERE id = :id";
        $stmt = $connect->prepare($sql);
        $stmt->execute([":chat_id" => $chat_to, ":date" => date("Y-m-d H:i:s", strtotime('+4 hours')), ":id" => $messageId]);
        $stmt->execute();
    }
}