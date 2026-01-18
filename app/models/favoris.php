<?php
require_once __DIR__."/../config/database.php";
class Favoris {
    private $db;

    public function __construct() { 
        $this->db = Database::getInstance()->getConnection(); 
    }

    public function ajouter($userId, $articleId) {
        $sql = "INSERT IGNORE INTO favoris (user_id, article_id) VALUES (?, ?)";
        return $this->db->prepare($sql)->execute([$userId, $articleId]);
    }

    public function supprimer($userId, $articleId) {
        $sql = "DELETE FROM favoris WHERE user_id = ? AND article_id = ?";
        return $this->db->prepare($sql)->execute([$userId, $articleId]);
    }

    public function listerParUser($userId) {
        $sql = "SELECT a.* FROM articles a JOIN favoris f ON a.id_article = f.article_id WHERE f.user_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}