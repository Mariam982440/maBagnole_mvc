<?php
require_once __DIR__."/../config/database.php";
class Avis {
    private $db;

    
    private $id_avis;
    private $note;
    private $commentaire;
    private $date_avis;
    private $actif; // pour le Soft Delete 1 = visible et 0 = supprimé
    private $user_id;
    private $vehicule_id;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    
    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            // validation de la note 
            if ($name === "note" && ($value < 1 || $value > 5)) {
                throw new Exception("La note doit être comprise entre 1 et 5.");
            }
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    
    public function ajouter() {
        $sql = "INSERT INTO avis (note, commentaire, user_id, vehicule_id) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $this->note,
            $this->commentaire,
            $this->user_id,
            $this->vehicule_id
        ]);
    }

    public function modifierAvis() {
        // l'id ne doit pas etre null 
        if (!$this->id_avis) return false;

        $sql = "UPDATE avis SET note = ?, commentaire = ? 
                WHERE id_avis = ? AND user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $this->note,
            $this->commentaire,
            $this->id_avis,
            $this->user_id
        ]);
    }


    public function supprimer() {
        if (!$this->id_avis) return false;

        $sql = "UPDATE avis SET actif = 0 WHERE id_avis = ? AND user_id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->id_avis, $this->user_id]);
    }

    
    public function listerParVehicule($vehicule_id) {
        $sql = "SELECT a.*, u.nom 
                FROM avis a
                JOIN users u ON a.user_id = u.id
                WHERE a.vehicule_id = ? AND a.actif = 1 
                ORDER BY a.date_avis DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$vehicule_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}