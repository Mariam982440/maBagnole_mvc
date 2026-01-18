<?php
require_once __DIR__."/../config/database.php";
require_once 'User.php';

class Admin extends User {
    
    public function __construct() {
        parent::__construct();
        $this->role = 'admin';
    } 
}
?>