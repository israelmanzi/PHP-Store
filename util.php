<?php

class Util
{
    public static function getDb()
    {
        $db = new PDO('pgsql:host=localhost;dbname=php-store', 'postgres', 'israel1108!');
        return $db;
    }

    public static function isUUID(string $uuid)
    {
        return preg_match('/^[a-f\d]{8}(-[a-f\d]{4}){4}[a-f\d]{8}$/i', $uuid);
    }
}