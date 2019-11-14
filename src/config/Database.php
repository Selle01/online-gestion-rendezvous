<?php

namespace App\Config;

use \PDO;

class DataBase
{
    private static $instance = null;
    /**
     * @return PDO
     */
    public static function getPdo()
    {
        if (self::$instance === null) {
            self::$instance = new PDO('mysql:host=localhost;dbname=gestion_hopital_online;charset=utf8', 'root', 'root', [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        }
        return self::$instance;
    }
}
