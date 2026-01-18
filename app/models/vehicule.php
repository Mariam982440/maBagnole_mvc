<?php
require_once __DIR__."/../config/database.php";
class Vehicule {
    private $db;
    
    private $id_v;
    private $marque;
    private $modele;
    private $prix_jours;
    private $disponibilite;
    private $image;
    private $c_id;

    public function __construct($marque = null, $modele = null, $prix = null, $dispo = 1, $image = null, $c_id = null) {
        $this->db = Database::getInstance()->getConnection();
        
        $this->marque = $marque;
        $this->modele = $modele;
        $this->prix_jours = $prix;
        $this->disponibilite = $dispo;
        $this->image = $image;
        $this->c_id = $c_id;
    }

    public function __set($name, $value) {
        // on vérifie si la propriété existe dans cette classe
        if (property_exists($this, $name)) {
            // validation du prix 
            if ($name === "prix_jours" && $value < 0) {
                throw new Exception("Le prix ne peut pas être négatif.");
            }

            $this->$name = $value;
        } else {
            throw new Exception("La propriété '$name' n'existe pas dans la classe Vehicule.");
        }
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }


    public function ajouter() {
        $sql = "INSERT INTO vehicule (marque, modele, prix_jours, disponibilite, image, c_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
    
        return $stmt->execute([
            $this->marque,
            $this->modele,
            $this->prix_jours,
            $this->disponibilite,
            $this->image,
            $this->c_id
        ]);
    }

    public function modifier() {
        // verifier s'il existe
        if (!$this->id_v) {
            return false;
        }

        $sql = "UPDATE vehicule 
                SET marque = ?, modele = ?, prix_jours = ?, disponibilite = ?, image = ?, c_id = ? 
                WHERE id_v = ?";
        
        $stmt = $this->db->prepare($sql);
        
        // on utilise $this pour envoyer les valeurs actuelles de l'objet
        return $stmt->execute([
            $this->marque,
            $this->modele,
            $this->prix_jours,
            $this->disponibilite,
            $this->image,
            $this->c_id,
            $this->id_v 
        ]);
    }

    public function supprimer() {
        if (!$this->id_v) {
            return false;
        }

        $sql = "DELETE FROM vehicule WHERE id_v = ?";
        $stmt = $this->db->prepare($sql);
        
        return $stmt->execute([$this->id_v]);
    }


    public function listerVehicules($limit, $offset) {
        $sql = "SELECT * FROM ListeVehicules LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  compter le nombre total de véhicules (pour la pagination)
    public function compterTotal() {
        return $this->db->query("SELECT COUNT(*) FROM vehicule")->fetchColumn();
    }

    // afficher les détails d'un véhicule spécifique
    public function getDetails($id) {
        $sql = "SELECT * FROM ListeVehicules WHERE id_v = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // rechercher un véhicule par modèle ou marque
    public function rechercher($query) {
        $sql = "SELECT * FROM ListeVehicules WHERE modele LIKE :query OR marque LIKE :query";
        $stmt = $this->db->prepare($sql);
        $searched = "%$query%";
        $stmt->execute([':query' => $searched]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // filtrer par catégorie
    public function filtrerParCategorie($id_categorie) {
        $sql = "SELECT * FROM ListeVehicules WHERE c_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_categorie]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // récupérer toute les catégories (pour le menu de filtrage)
    public function getCategories() {
        return $this->db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
    }
}