<?php 
class Database {

    public static ?Database $instance = null;
    private PDO $connection;

    private string $host = "localhost";
    private string $dbName = "mabagnole";
    private string $username = "root";
    private string $password = "";


    private function __construct ()
    {
        try {
           $this->connection = new PDO(
            "mysql:host={$this->host};dbname={$this->dbName};charset=utf8mb4",
            $this->username,
            $this->password
            );
            
        }
        catch (PDOException $e){
            die("erreur de connection à la base données".$e->getMessage());
        }

        $this->connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $this->connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
    } 

    private function __clone(){}

    public function __wakeup()
    {
        throw new Exception("Cannot unserialize singleton");
    }

    // retourne l'instance unique de database
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    
    // retourne la connexion PDO
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
}

