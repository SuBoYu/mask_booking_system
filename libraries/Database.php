<?php
/**
 * 使用單例模式的資料庫連接方式，無論使用多少次都是使用同一個連線
 */
class Database {

    private static $instance;
    private function __construct(){
        // 使用 private 建構子避免在外面被意外地初始化
    }

    private static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance = new DatabaseAccessObject(
                MySQL::ADDRESS,
                MySQL::USERNAME,
                MySQL::PASSWORD,
                MySQL::DATABASE
            );
        }
    }

    public static function get(){
        // 使用Database::get()來construct自己，而不是在外面用建構子
        self::getInstance();
        if(isset(self::$instance)){
            return self::$instance;
        }
        else{
            return NULL;
        }
    }

    public static function unlinkDAO(){
        if(isset(self::$instance)){
            self::$instance = null; // 會自動執行解構子 close link
        }
    }
}