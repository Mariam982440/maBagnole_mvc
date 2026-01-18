<?php
require_once __DIR__."/../config/database.php";
class Tag {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function ajouterPlusieurs(array $nomsTags) {
        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("INSERT IGNORE INTO tags (nom) VALUES (?)");
            foreach ($nomsTags as $nom) {
                $stmt->execute([trim($nom)]);
            }
            $this->db->commit();
            return true;
        } catch (Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public static function listerTout() {
        $db = Database::getInstance()->getConnection();
        return $db->query("SELECT * FROM tags")->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function listerParArticle($article_id) {
    $db = Database::getInstance()->getConnection();

    $sql = "SELECT t.* 
            FROM tags t 
            JOIN article_tags at ON t.id_tag = at.tag_id 
            WHERE at.article_id = ?";

    $stmt = $db->prepare($sql);
    $stmt->execute([$article_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}