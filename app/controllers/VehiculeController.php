<?php
class VehiculeController 
{
    public function index(){

        $role = $_SESSION['role'];
        $vehiculeManager = new Vehicule();


        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $search = $_GET['search'] ?? '';
        $cat_id = $_GET['category'] ?? null;

        if (!empty($search)) {
            $vehicules = $vehiculeManager->rechercher($search);
            $totalVehicules = count($vehicules);
        } elseif ($cat_id) {
            $vehicules = $vehiculeManager->filtrerParCategorie($cat_id);
            $totalVehicules = count($vehicules); 
        } else {
            $vehicules = $vehiculeManager->listerVehicules($limit, $offset);
            $totalVehicules = $vehiculeManager->compterTotal();
        }

        $totalPages = ceil($totalVehicules / $limit);
        $categories = $vehiculeManager->getCategories();

        require_once '../app/views/vehicules/location.php';


    }
    public function detail(){
        $articleManager = new Article();
        $commentManager = new Commentaire();
        $favManager = new Favoris();

        // recuperation de l'article 
        $article_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $article = $articleManager->getDetails($article_id); 

        if (!$article || $article['statut'] !== 'approuve') {
            die("Article non trouvé ou non publié.");
        }

        $user_id = $_SESSION['user_id'] ?? null;

        // -- ajouter un commentaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_comment']) && $user_id) {
            $commentManager->contenu = trim($_POST['contenu']);
            $commentManager->user_id = $user_id;
            $commentManager->article_id = $article_id;
            $commentManager->ajouter();
            header("Location: article_details.php?id=$article_id");
            exit;
        }

        // -- gestion des favoris (Toggle)
        if (isset($_GET['action']) && $_GET['action'] === 'toggle_fav' && $user_id) {
            $favManager->ajouter($user_id, $article_id); 
            header("Location: article_details.php?id=$article_id");
            exit;
        }

        // 3. Récupération des données liées
        $commentaires = Commentaire::listerParArticle($article_id);
        $tagsArticle = Tag::listerParArticle($article_id);

        require_once '../app/views/vehicules/detail_vehicule.php';
    }
}
