<?php
require_once __DIR__."/../config/database.php";
class tagTheme 
 {
    private $db;

    private $id;
    private $;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    
    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }
 }