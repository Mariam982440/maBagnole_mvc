<?php
require_once __DIR__."/../config/database.php";
class User 
{
    protected ?int $id = null;
    protected string $nom;
    protected string $email;
    protected string $motDePasse;
    protected string $role;
    protected $db;

   public function __construct()
    {
        // on recupere la connexion via le singleton
        $this->db = Database::getInstance()->getConnection();
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getRole() { return $this->role; }

    public function seConnecter($email, $motDePasse)
    {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($motDePasse, $user['mot_de_passe'])) {
            
            $this->id = $user['id'];
            $this->nom = $user['nom'];
            $this->email = $user['email'];
            $this->role = $user['role'];

            // Création de la session
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['user_id'] = $this->id;
            $_SESSION['role'] = $this->role;
            $_SESSION['nom'] = $this->nom;

            return true;
        }

        return false; 
    }


    public function inscrire($nom, $email, $motDePasse){
        $sql_i = "SELECT * from users where email = ?";
        $check = $this->db->prepare($sql_i);
        $check->execute([$email]);
        
        if ($check->rowCount() > 0){
            throw new Exception("Cet email est déjà utilisé.");
        }
        
        $role = 'client';
        $est_approuve = 0;

        $hash = password_hash($motDePasse, PASSWORD_DEFAULT);

        $sql = " INSERT INTO users (nom, email, mot_de_passe, role, approuve) values (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare( $sql);
        return $stmt ->execute([$nom, $email, $hash, $role, $est_approuve]);
            
    }
}