<?php
require_once '../classes/database.php';
require_once '../classes/article.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../client/login.php");
    exit;
}

$articleManager = new Article();

if (isset($_GET['action']) && $_GET['action'] === 'approuver' && isset($_GET['id'])) {
    $articleManager->approuver((int)$_GET['id']);
    header("Location: admin_articles.php?success=1");
    exit;
}

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

$articles = $articleManager->listerToutAdmin($limit, $offset);
$totalArticles = $articleManager->compterToutAdmin();
$totalPages = ceil($totalArticles / $limit);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Admin | Gestion Articles</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-slate-900 text-white shadow-lg">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span class="text-2xl font-bold text-blue-500">AdminPanel</span>
                <a href="dashboard.php" class="hover:text-blue-400">Statistiques</a>
                <a href="admin_articles.php" class="text-blue-400 border-b-2 border-blue-400">Articles</a>
            </div>
            <a href="../client/logout.php" class="bg-red-600 px-4 py-2 rounded-lg text-sm">Déconnexion</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 py-10">
        
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-black text-slate-800">Modération des articles</h1>
            
            <select onchange="location = this.value;" class="border rounded-lg px-3 py-2 bg-white shadow-sm outline-none">
                <option value="admin_articles.php?limit=6" <?= $limit==6 ? 'selected':'' ?>>Afficher 6</option>
                <option value="admin_articles.php?limit=12" <?= $limit==12 ? 'selected':'' ?>>Afficher 12</option>
                <option value="admin_articles.php?limit=24" <?= $limit==24 ? 'selected':'' ?>>Afficher 24</option>
            </select>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 text-green-700 p-4 rounded-xl mb-6 shadow-sm">
                L'article a été approuvé et est désormais visible par tous.
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 gap-6">
            <?php if(empty($articles)): ?>
                <div class="bg-white p-10 text-center rounded-2xl shadow">Aucun article à gérer.</div>
            <?php else: ?>
                <?php foreach($articles as $a): ?>
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden flex flex-col md:flex-row border-2 <?= $a['statut'] == 'en_attente' ? 'border-amber-200' : 'border-transparent' ?>">
                        
                        <!-- Image -->
                        <div class="w-full md:w-56 h-48 md:h-auto bg-gray-200 relative">
                            <img src="<?= $a['image_url'] ?? 'https://via.placeholder.com/300' ?>" class="w-full h-full object-cover">
                            <?php if($a['statut'] == 'en_attente'): ?>
                                <span class="absolute top-2 left-2 bg-amber-500 text-white text-[10px] font-bold px-2 py-1 rounded">EN ATTENTE</span>
                            <?php else: ?>
                                <span class="absolute top-2 left-2 bg-green-500 text-white text-[10px] font-bold px-2 py-1 rounded">PUBLIÉ</span>
                            <?php endif; ?>
                        </div>

                        <!-- Contenu -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <span class="text-xs font-bold text-blue-600 uppercase"><?= htmlspecialchars($a['theme_nom'] ?? 'Sans thème') ?></span>
                                    <span class="text-xs text-gray-400">ID: #<?= $a['id_article'] ?></span>
                                </div>
                                <h2 class="text-xl font-bold text-gray-800 mt-1"><?= htmlspecialchars($a['titre']) ?></h2>
                                <p class="text-gray-500 text-sm mt-2 line-clamp-2"><?= htmlspecialchars($a['contenu']) ?></p>
                                <p class="text-xs text-gray-400 mt-3">Auteur: <strong><?= htmlspecialchars($a['auteur_nom']) ?></strong> • <?= date('d/m/Y', strtotime($a['created_at'])) ?></p>
                            </div>

                            <div class="mt-6 flex items-center justify-between">
                                <a href="article_details.php?id=<?= $a['id_article'] ?>" class="text-sm font-bold text-slate-600 hover:underline">Voir l'aperçu</a>
                                
                                <div class="flex gap-2">
                                    <?php if($a['statut'] == 'en_attente'): ?>
                                        <a href="admin_articles.php?action=approuver&id=<?= $a['id_article'] ?>" 
                                           onclick="return confirm('Approuver cet article ?')"
                                           class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-green-700 shadow-md">
                                            Approuver l'article
                                        </a>
                                    <?php endif; ?>
                                    <button class="bg-red-50 text-red-600 px-4 py-2 rounded-lg text-sm font-bold hover:bg-red-100">Supprimer</button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <!-- PAGINATION CORRIGÉE -->
        <?php if($totalPages > 1): ?>
            <div class="flex justify-center items-center space-x-2 mt-10">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <a href="admin_articles.php?page=<?= $i ?>&limit=<?= $limit ?>" 
                       class="px-4 py-2 rounded-xl font-bold transition-all <?= $page == $i ? 'bg-blue-600 text-white shadow-lg' : 'bg-white text-gray-600 hover:bg-gray-200' ?>">
                       <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>