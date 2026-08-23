<?php
    
class Database {

    public static $connection;

    public static function setUpConnection() {

        if(!isset(Database::$connection)) {

            // Sashin Local
            //Database::$connection = new mysqli("localhost", "root", "", "sapsri_admin_core");

            // Batawala Local
            // Database::$connection = new mysqli("localhost", "root", "", "sapsril_sapsri_admin_core");
            
            // SAPSRI DB
            Database::$connection = new mysqli("localhost", "sapsril_sldevs", "wveeaj+!eTIUNHLI", "sapsril_sapsri_admin_core");
    

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