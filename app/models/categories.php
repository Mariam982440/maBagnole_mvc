<?php
require_once __DIR__."/../config/database.php";
class Categories {
    private $db;

    private $id_c;
    private $nom;
    private $description;

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

    
    public static function listerTout() {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT * FROM categories ORDER BY nom ASC";
        return $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function ajouter() {
        $sql = "INSERT INTO categories (nom, description) VALUES (?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->nom, $this->description]);
    }

   
    public function modifier() {
        if (!$this->id_c) return false;
        $sql = "UPDATE categories SET nom = ?, description = ? WHERE id_c = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->nom, $this->description, $this->id_c]);
    }

    public function supprimer($id) {
        $sql = "DELETE FROM categories WHERE id_c = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }

    public function chargerParId($id) {
        $sql = "SELECT * FROM categories WHERE id_c = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($res) {
            $this->id_c = $res['id_c'];
            $this->nom = $res['nom'];
            $this->description = $res['description'];
            return true;
        }
        return false;
    }
}