<?php
require_once __DIR__.'/classes/database.php';
require_once __DIR__.'/classes/article.php';
require_once __DIR__.'/classes/theme.php';
require_once __DIR__.'/classes/tag.php';

session_start();
$role = $_SESSION['role'];
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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaBagnole | Blog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- HEADER -->
    <nav class="bg-slate-800 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold">MaBagnole 🚗</a>
            <div class="space-x-6">
                <a href="index.php" class="hover:text-blue-400">Location</a>
                <a href="blog.php" class="text-blue-400 font-semibold">Blog</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="bg-blue-600 px-4 py-2 rounded hover:bg-blue-700">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 md:grid-cols-4 gap-8">
        
        <!-- SIDEBAR -->
        <aside class="space-y-6">
            <div class="bg-white p-5 rounded-xl shadow-sm">
                <h3 class="font-bold text-lg border-b pb-2 mb-4">Thèmes</h3>
                <ul class="space-y-2">
                    <li><a href="blog.php" class="text-gray-600 hover:text-blue-600">Tous les thèmes</a></li>
                    <?php foreach($themes as $t): ?>
                        <li>
                            <a href="blog.php?theme=<?= $t['id_theme'] ?>" class="block p-2 rounded <?= $theme_id == $t['id_theme'] ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-50' ?>">
                                <?= htmlspecialchars($t['nom']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="bg-white p-5 rounded-xl shadow-sm">
                <h3 class="font-bold text-lg border-b pb-2 mb-4">Tags</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach($tags as $tag): ?>
                        <a href="blog.php?tag=<?= $tag['id_tag'] ?>" class="text-xs bg-gray-200 px-2 py-1 rounded hover:bg-blue-500 hover:text-white transition">
                            #<?= htmlspecialchars($tag['nom']) ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="md:col-span-3 space-y-6">
            
            <!-- SEARCH & ADD -->
            <div class="flex flex-col md:flex-row gap-4 justify-between items-center bg-white p-4 rounded-xl shadow-sm">
                <form action="blog.php" method="GET" class="flex w-full md:w-2/3">
                    <input type="text" name="search" placeholder="Rechercher un article..." value="<?= htmlspecialchars($search) ?>" class="w-full border rounded-l-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded-r-lg hover:bg-blue-700">Chercher</button>
                </form>

                <div class="flex items-center gap-4">
                    <select onchange="location = this.value;" class="border rounded-lg px-2 py-2 bg-gray-50">
                        <option value="blog.php?limit=5" <?= $limit==5 ? 'selected':'' ?>>5 / page</option>
                        <option value="blog.php?limit=10" <?= $limit==10 ? 'selected':'' ?>>10 / page</option>
                        <option value="blog.php?limit=15" <?= $limit==15 ? 'selected':'' ?>>15 / page</option>
                    </select>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="client/ajouter_article.php" class="bg-green-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-green-700">+ Article</a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ARTICLES LIST -->
            <div class="space-y-6">
                <?php if(empty($articles)): ?>
                    <div class="bg-white p-10 text-center rounded-xl shadow">Aucun article trouvé.</div>
                <?php else: ?>
                    <?php foreach($articles as $a): ?>
                        <div class="bg-white rounded-xl shadow-sm overflow-hidden flex flex-col md:flex-row border border-transparent hover:border-blue-300 transition">
                            <div class="w-full md:w-48 bg-gray-200 h-48 md:h-auto">
                                <img src="<?= $a['image_url'] ?? 'https://via.placeholder.com/200' ?>" class="w-full h-full object-cover">
                            </div>
                            <div class="p-6 flex-1">
                                <span class="text-xs font-bold text-blue-600 uppercase"><?= htmlspecialchars($a['theme_nom'] ?? 'Blog') ?></span>
                                <h2 class="text-xl font-bold text-gray-800 mt-2"><?= htmlspecialchars($a['titre']) ?></h2>
                                <p class="text-gray-600 mt-3"><?= substr(htmlspecialchars($a['contenu']), 0, 150) ?>...</p>
                                <div class="mt-4 flex justify-between items-center">
                                    <span class="text-sm text-gray-400"><?= date('d M Y', strtotime($a['created_at'])) ?></span>
                                    <a href="article_details.php?id=<?= $a['id_article'] ?>" class="text-blue-600 font-bold hover:underline">Lire la suite →</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- PAGINATION -->
            <?php if($totalPages > 1): ?>
                <div class="flex justify-center space-x-2 py-4">
                    <?php for($i=1; $i<=$totalPages; $i++): ?>
                        <a href="blog.php?page=<?= $i ?>&limit=<?= $limit ?>&theme=<?= $theme_id ?>" 
                           class="px-4 py-2 rounded <?= $page == $i ? 'bg-blue-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-200' ?>">
                           <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>

        </main>
    </div>
</body>
</html>