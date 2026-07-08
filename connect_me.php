<?php 
class Database {
    private static $instance = NULL;
  
    private function __construct() {}
  
    public static function getInstance() {
      if (!isset(self::$instance)) {
        $hostname = 'localhost';
        $username = 'osudlagb_product';
        $password = 'rYOA}.*W^K3x';
        $database = 'osudlagb_rc_center';
  
        try {
          self::$instance = new PDO("mysql:host=$hostname;dbname=$database", $username, $password);
          self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
          echo 'Connection failed: ' . $e->getMessage();
        }
      }
  
      return self::$instance;
    }
  }
