<?php
require_once __DIR__."/../config/database.php";
class Theme {
    private $db;
    private $id_theme;
    private $nom;
    private $description;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function __set($name, $value) { 
        if (property_exists($this, $name)){ 
            $this->$name = $value; 
        } 
    }
    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name; } 
        }

    public static function listerTout() {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT * FROM themes")->fetchAll(PDO::FETCH_ASSOC);
    }
}