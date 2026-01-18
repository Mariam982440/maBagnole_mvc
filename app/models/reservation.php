<?php
require_once __DIR__."/../config/database.php";
class Reservation {
    private $db;

    private $id_reservation;
    private $date_debut;
    private $date_fin;
    private $lieu_prise;
    private $lieu_retour;
    private $user_id;
    private $vehicule_id;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function __set($name, $value) {
        if (property_exists($this, $name)) {
            // validation pour les dates
            if ($name === "date_fin" && isset($this->date_debut) && $value < $this->date_debut) {
                throw new Exception("La date de fin ne peut pas être avant la date de début.");
            }
            $this->$name = $value;
        }
    }

    public function __get($name) {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    public function reserver() {
    $sql = "CALL AjouterReservation(?, ?, ?, ?, ?, ?)";
    
    $stmt = $this->db->prepare($sql);
    
    return $stmt->execute([
        $this->date_debut,
        $this->date_fin,
        $this->lieu_prise,
        $this->lieu_retour,
        $this->user_id,
        $this->vehicule_id
    ]);
    }

    // lister les réservations d'un client spécifique
    public function listerParClient($userId) {
        $sql = "SELECT r.*, v.marque, v.modele 
                FROM reservations r
                JOIN vehicule v ON r.vehicule_id = v.id_v
                WHERE r.user_id = ?
                ORDER BY r.date_debut DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // modifier une réservation
    public function modifierRes() {
        if (!$this->id_reservation) return false;

        $sql = "UPDATE reservations SET 
                date_debut = ?, date_fin = ?, lieu_prise = ?, lieu_retour = ? 
                WHERE id_reservation = ? AND user_id = ?"; // check du user_id
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $this->date_debut,
            $this->date_fin,
            $this->lieu_prise,
            $this->lieu_retour,
            $this->id_reservation,
            $this->user_id
        ]);
    }

    public function annulerRes() {
        if (!$this->id_reservation) return false;

        $sql = "DELETE FROM reservations WHERE id_reservation = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$this->id_reservation]);
    }
}