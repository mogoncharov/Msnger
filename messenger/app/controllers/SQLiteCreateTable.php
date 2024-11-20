<?php

namespace App\controllers;

/**
 * SQLite Create Table Demo
 */
class SQLiteCreateTable {

    /**
     * PDO object
     * @var \PDO
     */
    private $pdo;

    /**
     * connect to the SQLite database
     */
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * create tables 
     */
    public function createTables() {

        $commands = ["CREATE TABLE IF NOT EXISTS messages (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        incoming_user_id int(11) NOT NULL,
                        chat_id int(11) NOT NULL,
                        message varchar(1000) NOT NULL,
                        created_at TEXT DEFAULT (datetime('now','localtime'))
                    )",
                    "CREATE TABLE IF NOT EXISTS users (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        email varchar(30) NOT NULL,
                        password varchar(100) NOT NULL,
                        photo_file varchar(500) DEFAULT '',
                        nickname varchar(30) NOT NULL DEFAULT '',
                        is_active tinyint(4) NOT NULL DEFAULT '0',
                        show_email tinyint(4) NOT NULL DEFAULT '1',
                        created_at TEXT DEFAULT (datetime('now','localtime')),
                        updated_at TEXT DEFAULT (datetime('now','localtime'))
                    )",
                    "CREATE TABLE IF NOT EXISTS chats (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        chat_users text DEFAULT '',
                        type varchar(10) DEFAULT 'single',
                        chat_name varchar(20) DEFAULT NULL,
                        sounds TEXT DEFAULT '',
                        created_at TEXT DEFAULT (datetime('now','localtime'))
                    )",
                    "CREATE UNIQUE INDEX idx_users_email 
                    ON users (email);",
                    "CREATE UNIQUE INDEX idx_users_nickname 
                    ON users (nickname);"
                    ];
        // execute the sql commands to create new tables
        foreach ($commands as $command) {
            $this->pdo->exec($command);
        }
    }
    public function getTableList() {

        $stmt = $this->pdo->query("SELECT name
                                   FROM sqlite_master
                                   WHERE type = 'table'
                                   ORDER BY name");
        $tables = [];
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $tables[] = $row['name'];
        }

        return $tables;
    }
}