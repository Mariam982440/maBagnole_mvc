<?php
require_once __DIR__.'/classes/database.php';
require_once __DIR__.'/classes/article.php';
require_once __DIR__.'/classes/commentaire.php';
require_once __DIR__.'/classes/tag.php';
require_once __DIR__.'/classes/favoris.php';

session_start();

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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['titre']) ?> - MaBagnole</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

    <!-- NAVIGATION -->
    <nav class="bg-slate-800 text-white p-4 shadow-lg">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <a href="blog.php" class="text-xl font-bold">MaBagnole Blog 🚗</a>
            <a href="blog.php" class="text-sm bg-slate-700 px-4 py-2 rounded hover:bg-slate-600 transition">← Retour au blog</a>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto my-10 px-4">
        
        <!-- ARTICLE COMPLET -->
        <article class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100">
            <?php if($article['image_url']): ?>
                <img src="<?= htmlspecialchars($article['image_url']) ?>" class="w-full h-96 object-cover" alt="Image article">
            <?php endif; ?>

            <div class="p-8">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="bg-blue-100 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase">
                            <?= htmlspecialchars($article['theme_nom']) ?>
                        </span>
                        <h1 class="text-4xl font-extrabold text-gray-900 mt-2"><?= htmlspecialchars($article['titre']) ?></h1>
                    </div>
                    
                    <!-- Bouton Favoris -->
                    <?php if($user_id): ?>
                        <a href="article_details.php?id=<?= $article_id ?>&action=toggle_fav" 
                           class="p-3 rounded-full bg-gray-100 hover:bg-red-100 text-gray-400 hover:text-red-500 transition">
                            ❤
                        </a>
                    <?php endif; ?>
                </div>

                <div class="text-gray-500 text-sm mb-8">
                    Par <span class="font-semibold"><?= htmlspecialchars($article['auteur_nom']) ?></span> 
                    • Publié le <?= date('d/m/Y', strtotime($article['created_at'])) ?>
                </div>

                <!-- Contenu de l'article -->
                <div class="prose max-w-none text-gray-700 leading-relaxed text-lg mb-10">
                    <?= nl2br(htmlspecialchars($article['contenu'])) ?>
                </div>

                

                <!-- Tags -->
                <div class="flex flex-wrap gap-2 pt-6 border-t">
                    <span class="text-gray-400 text-sm mr-2">Tags :</span>
                    <?php foreach($tagsArticle as $t): ?>
                        <a href="blog.php?tag=<?= $t['id_tag'] ?>" class="text-sm bg-gray-100 px-3 py-1 rounded-lg hover:bg-blue-500 hover:text-white transition">
                            #<?= htmlspecialchars($t['nom']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>

        <!-- SECTION COMMENTAIRES -->
        <section class="mt-12">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Commentaires (<?= count($commentaires) ?>)</h3>

            <!-- Formulaire d'ajout (si connecté) -->
            <?php if($user_id): ?>
                <form action="article_details.php?id=<?= $article_id ?>" method="POST" class="bg-white p-6 rounded-xl shadow-sm mb-8">
                    <textarea name="contenu" rows="3" required placeholder="Votre commentaire..." 
                        class="w-full border border-gray-200 rounded-lg p-4 focus:ring-2 focus:ring-blue-500 outline-none transition"></textarea>
                    <button type="submit" name="add_comment" class="mt-3 bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700">
                        Publier le commentaire
                    </button>
                </form>
            <?php else: ?>
                <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-8 text-center">
                    <a href="login.php" class="font-bold underline">Connectez-vous</a> pour laisser un commentaire.
                </div>
            <?php endif; ?>

            <!-- Liste des commentaires -->
            <div class="space-y-4">
                <?php foreach($commentaires as $c): ?>
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-2">
                            <span class="font-bold text-gray-800"><?= htmlspecialchars($c['nom']) ?></span>
                            <span class="text-xs text-gray-400"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></span>
                        </div>
                        <p class="text-gray-600"><?= nl2br(htmlspecialchars($c['contenu'])) ?></p>
                        
                        <!-- Actions sur son propre commentaire -->
                        <?php if($user_id == $c['user_id']): ?>
                            <div class="mt-3 flex gap-4 text-xs">
                                <a href="#" class="text-blue-500 hover:underline">Modifier</a>
                                <a href="supprimer_commentaire.php?id=<?= $c['id_commentaire'] ?>&article=<?= $article_id ?>" 
                                   class="text-red-500 hover:underline" onclick="return confirm('Supprimer ce commentaire ?')">Supprimer</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

    </main>

</body>
</html>