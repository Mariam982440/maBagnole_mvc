<?php
require_once __DIR__."/../config/database.php";
class Article {
    private $db;

    private $id_article;
    private $titre;
    private $contenu;
    private $image_url;
    private $statut; // en_attente, approuve, rejete
    private $user_id;
    private $theme_id;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function __set($name, $value) { if (property_exists($this, $name)) { $this->$name = $value; } }
    public function __get($name) { if (property_exists($this, $name)) { return $this->$name; } }


    public function ajouter() {
        $sql = "INSERT INTO articles (titre, contenu, image_url, statut, user_id, theme_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->titre,
                                $this->contenu,
                                $this->image_url, 
                                $this->statut, 
                                $this->user_id, 
                                $this->theme_id]
                            );
    }

    public function listerArticlesParTheme($theme_id, $limit, $offset) {
        $sql = "SELECT a.*, t.nom as theme_nom, u.nom as auteur_nom 
                FROM articles a 
                JOIN themes t ON a.theme_id = t.id_theme 
                JOIN users u ON a.user_id = u.id 
                WHERE a.theme_id = :theme_id AND a.statut = 'approuve' 
                ORDER BY a.created_at DESC LIMIT :limit_t OFFSET :offset";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':theme_id', (int)$theme_id, PDO::PARAM_INT);        
        $stmt->bindValue(':limit_t', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function listerArticles($limite, $offset){
        $sql = "SELECT a.*, t.nom as theme_nom, u.nom as auteur_nom
        FROM articles a 
        JOIN themes t ON a.theme_id = t.id_theme 
        JOIN users u ON a.user_id = u.id 
        WHERE statut = 'approuve' 
        ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':limit', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function listerArticlesEnAttente($limite, $offset){
        $sql = "SELECT a.*, t.nom as theme_nom, u.nom as auteur_nom
        FROM articles a 
        JOIN themes t ON a.theme_id = t.id_theme 
        JOIN users u ON a.user_id = u.id 
        WHERE statut = 'en_attente' 
        ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        $stmt->bindValue(':limit', (int)$limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function rechercher($titre){
        $sql = "SELECT * FROM articles where titre LIKE :query";
        $rech = "%$titre%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':query'=>$rech]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filtrerParTag($id_tag) {
        $sql = "SELECT a.* FROM articles a 
                JOIN article_tags a_t ON a.id_article = a_t.article_id 
                WHERE a_t.tag_id = ? AND a.statut = 'approuve'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tag]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function changerStatut($id, $nouveauStatut) {
        $sql = "UPDATE articles SET statut = ? WHERE id_article = ?";
        return $this->db->prepare($sql)->execute([$nouveauStatut, $id]);
    }

    // compter tous les articles approuvés
    public function compterTotalArticles() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM articles WHERE statut = 'approuve'");
        return $stmt->fetchColumn();
    }

    public function compterTotalArticlesAtt() {
        $stmt = $this->db->query("SELECT COUNT(*) FROM articles WHERE statut = 'en_attente'");
        return $stmt->fetchColumn();
    }

    // compter les articles d'un theme précis
    public function compterParTheme($theme_id) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM articles WHERE theme_id = ? AND statut = 'approuve'");
        $stmt->execute([$theme_id]);
        return $stmt->fetchColumn();
    }

    public function getDetails($id) {
        $sql = "SELECT a.*, t.nom as theme_nom, u.nom as auteur_nom 
                FROM articles a 
                JOIN themes t ON a.theme_id = t.id_theme 
                JOIN users u ON a.user_id = u.id 
                WHERE a.id_article = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
}

    public function approuver($id_article) {
        $sql = "UPDATE articles SET statut = 'approuve' WHERE id_article = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_article]);
    }

    public function compterToutAdmin() {
        $sql = "SELECT COUNT(*) FROM articles";
        return $this->db->query($sql)->fetchColumn();
    }

    public function listerToutAdmin($limit, $offset) {
        $sql = "SELECT a.*, t.nom as theme_nom, u.nom as auteur_nom 
                FROM articles a 
                LEFT JOIN themes t ON a.theme_id = t.id_theme 
                LEFT JOIN users u ON a.user_id = u.id 
                ORDER BY a.statut DESC, a.created_at DESC 
                LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}