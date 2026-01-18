<?php
class blogController 
{   public function index(){
        $articleManager = new Article();

        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 5;
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $offset = ($page - 1) * $limit;

        $theme_id = $_GET['theme'] ?? null;
        $tag_id = $_GET['tag'] ?? null;
        $search = $_GET['search'] ?? '';

        // filtrage
        if (!empty($search)) {
            $articles = $articleManager->rechercher($search);
            $totalArticles = count($articles);
        } elseif ($tag_id) {
            $articles = $articleManager->filtrerParTag($tag_id);
            $totalArticles = count($articles);
        } elseif ($theme_id) {
            $articles = $articleManager->listerArticlesParTheme($theme_id, $limit, $offset);
            $totalArticles = $articleManager->compterParTheme($theme_id); 
        } else {
            $articles = $articleManager->listerArticles($limit, $offset, true);
            $totalArticles = $articleManager->compterTotalArticles(); }

        $totalPages = ceil($totalArticles / $limit);

        $themes = Theme::listerTout();
        $tags = Tag::listerTout();

        require_once '../app/views/blog/index.php';
    }



    public function ajouter(){
        
        if (!isset($_SESSION['user_id'])) {
            header("Location: login");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['role'];
        $user_nom = $_SESSION['nom'];

        $message = ""; // pour informer l'utilisateur

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $titre_A = trim($_POST['titre']);
            $contenu_A = trim($_POST['contenu']);
            $image_A = trim($_POST['image_url']);
            $theme_A = $_POST['theme'];
            $statu_A = 'en_attente';

            if (!empty($titre_A) && !empty($contenu_A)) {
                $article = new Article();
                $article->titre = $titre_A;
                $article->contenu = $contenu_A;
                $article->image_url = $image_A;
                $article->statut = $statu_A;
                $article->theme_id = $theme_A;
                $article->user_id = $user_id;
                
                if ($article->ajouter()) {
                    $message = "<div class='bg-green-100 text-green-700 p-3 rounded mb-4'>L'article a été envoyé pour validation.</div>";
                } else {
                    $message = "<div class='bg-red-100 text-red-700 p-3 rounded mb-4'>Erreur lors de l'ajout.</div>";
                }
            } else {
                $message = "<div class='bg-yellow-100 text-yellow-700 p-3 rounded mb-4'>Veuillez remplir les champs obligatoires.</div>";
            }
        }

        $themeTab = Theme::listerTout();

        require_once '../app/views/blog/ajouter_article.php';
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
            header("Location: ../index/detail?id=$article_id");
            exit;
        }

        // -- gestion des favoris (toggle)
        if (isset($_GET['action']) && $_GET['action'] === 'toggle_fav' && $user_id) {
            $favManager->ajouter($user_id, $article_id); 
            header("Location: ../index/detail?id=$article_id");
            exit;
        }

        // 3. Récupération des données liées
        $commentaires = Commentaire::listerParArticle($article_id);
        $tagsArticle = Tag::listerParArticle($article_id);

        require_once '../app/views/vehicules/detail_vehicule.php';
    }
    
}
?>