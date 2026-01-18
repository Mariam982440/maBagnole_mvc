<?php
require_once '../classes/database.php';
require_once '../classes/article.php';
require_once '../classes/theme.php';
require_once '../classes/tag.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaBagnole | Ajouter un article</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800">

    <nav class="bg-slate-800 text-white shadow-md p-4">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <a href="../blog.php" class="text-xl font-bold">MaBagnole Blog 🚗</a>
            <div class="flex items-center gap-4">
                <span class="text-sm italic">Bonjour, <?= htmlspecialchars($user_nom) ?></span>
                <a href="../blog.php" class="text-xs bg-slate-700 px-3 py-1 rounded hover:bg-slate-600 transition">Retour</a>
            </div>
        </div>
    </nav>

    <div class="max-w-2xl mx-auto my-10 p-8 bg-white rounded-xl shadow-sm border border-gray-100">
        <h2 class="text-2xl font-bold mb-6 text-slate-800">Proposer un nouvel article</h2>
        
        <?= $message ?>

        <form action="ajouter_article.php" method="POST" class="space-y-5">
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Titre de l'article *</label>
                <input type="text" name="titre" required
                    class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Choisir un thème *</label>
                <select name="theme" class="w-full border-gray-300 border p-2.5 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                    <?php foreach($themeTab as $t): ?>
                        <option value="<?= $t['id_theme'] ?>">
                            <?= htmlspecialchars($t['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Contenu de l'article *</label>
                <textarea name="contenu" rows="6" required
                    class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition"></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Lien de l'image</label>
                <input type="text" name="image_url" placeholder="https://exemple.com/image.jpg"
                    class="w-full border-gray-300 border p-2.5 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition">
            </div>

            <div class="pt-4">
                <button type="submit" 
                    class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 shadow-md transition-all active:scale-95">
                    Soumettre pour approbation
                </button>
            </div>
        </form>
    </div>

</body>
</html>