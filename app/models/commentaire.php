<?php
require_once __DIR__."/../config/database.php";
class commentaire 
 {
    private $db;

    private $id;
    private $contenu;
    private $user_id;
    private $created_at;
    private $article_id;

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
    public static function listerParArticle($article_id) {
    $db = Database::getInstance()->getConnection();

    $sql = "SELECT c.*, u.nom 
            FROM commentaires c 
            JOIN users u ON c.user_id = u.id 
            WHERE c.article_id = ? 
            ORDER BY c.created_at DESC";

    $stmt = $db->prepare($sql);
    $stmt->execute([$article_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
public function ajouter() {
    $sql = "INSERT INTO commentaires (contenu, user_id, article_id) VALUES (?, ?, ?)";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$this->contenu, $this->user_id, $this->article_id]);
}

public function supprimer($id_commentaire, $user_id) {
    $sql = "DELETE FROM commentaires WHERE id_commentaire = ? AND user_id = ?";
    $stmt = $this->db->prepare($sql);
    return $stmt->execute([$id_commentaire, $user_id]);
}
 }