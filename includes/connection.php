<?php
    
class Database {

    public static $connection;

    public static function setUpConnection() {

        if(!isset(Database::$connection)) {

            //Database::$connection = new mysqli("localhost", "root", "", "sapsri_admin_core");
            Database::$connection = new mysqli("65.109.146.175", "sapsril_sldevs", "wveeaj+!eTIUNHLI", "sapsril_sapsri_admin_core", 3306);
            // Database::$connection = new mysqli("localhost", "sapsril_sldevs", "TsKEIHAJk}]1F@7g", "sapsril_sldevs_db");

            if (Database::$connection->connect_error) {
                error_log("DB CONNECTION FAILED: " . Database::$connection->connect_error);
                die("Database connection error");
            }

            // REQUIRED: Set charset to utf8mb4 to support Sinhala characters and emojis securely
            Database::$connection->set_charset("utf8mb4");
        }
    }

    public static function search($query) {

        Database::setUpConnection();
        return Database::$connection->query($query);

    }

    public static function iud($query) {

        Database::setUpConnection();
        Database::$connection->query($query);

    }
}
?>