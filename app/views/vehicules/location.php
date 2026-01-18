<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaBagnole | Location de véhicules</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- HEADER / NAVBAR -->
    <nav class="bg-slate-900 text-white shadow-xl">
        <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
            <a href="index.php" class="text-2xl font-bold tracking-tighter">MaBagnole <span class="text-blue-500">.</span></a>
            <div class="hidden md:flex space-x-8 items-center">
                <a href="../vehicule/index" class="text-blue-400 border-b-2 border-blue-400 pb-1">Location</a>
                <a href="../blog/index" class="hover:text-blue-400 transition">Blog</a>
                <?php if($role === 'client'): ?>
                    <a href="./client/reservations.php" class="hover:text-blue-400 transition">reservations</a>
                <?php endif; ?>
                <?php if($role === 'admin'): ?>
                    <a href="./admin/gerer_article.php" class="hover:text-blue-400 transition">articles</a>
                <?php endif; ?>
                
                <?php if(isset($_SESSION['user_id'])): ?>
                    <span class="text-gray-400 text-sm">Bienvenue, <?= htmlspecialchars($_SESSION['nom']) ?></span>
                    <a href="logout.php" class="bg-red-600 px-4 py-2 rounded-lg hover:bg-red-700 text-sm font-bold">Déconnexion</a>
                <?php else: ?>
                    <a href="login.php" class="bg-blue-600 px-4 py-2 rounded-lg hover:bg-blue-700 text-sm font-bold">Connexion</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- BARRE DE FILTRES -->
    <section class="bg-white border-b border-gray-200 sticky top-0 z-10 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-4">
            <form action="../vehicule/index" method="GET" class="flex flex-col md:flex-row gap-4 items-center">
                
                <!-- Recherche -->
                <div class="relative flex-1 w-full">
                    <input type="text" name="search" placeholder="Rechercher un modèle (ex: Clio, Tesla...)" 
                           value="<?= htmlspecialchars($search) ?>"
                           class="w-full pl-10 pr-4 py-2 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                    <span class="absolute left-3 top-2.5 text-gray-400">🔍</span>
                </div>

                <!-- Catégories -->
                <select name="category" class="w-full md:w-64 p-2 border rounded-xl bg-gray-50 outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">Toutes les catégories</option>
                    <?php foreach($categories as $c): ?>
                        <option value="<?= $c['id_c'] ?>" <?= $cat_id == $c['id_c'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['nom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="w-full md:w-auto bg-blue-600 text-white px-8 py-2 rounded-xl font-bold hover:bg-blue-700 transition">
                    Filtrer
                </button>
                
                <?php if($search || $cat_id): ?>
                    <a href="../vehicule/index" class="text-sm text-red-500 hover:underline">Réinitialiser</a>
                <?php endif; ?>
            </form>
        </div>
    </section>

    <!-- GRILLE DES VÉHICULES -->
    <main class="max-w-7xl mx-auto px-4 py-10">
        <h2 class="text-3xl font-extrabold text-gray-800 mb-8">Véhicules disponibles</h2>
        <?php if($role === 'admin'): ?>
        <button  class="w-full md:w-auto bg-blue-600 text-white px-8 py-2 rounded-xl font-bold hover:bg-blue-700 transition mb-8">
            <a href="admin/ajouter_vehicule.php" >
                ajouter vehicule
                </a>       
        </button>
        <?php endif; ?>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php if(empty($vehicules)): ?>
                <div class="col-span-full bg-white p-12 text-center rounded-3xl shadow-inner border-2 border-dashed">
                    <p class="text-gray-500 text-lg">Aucun véhicule ne correspond à votre recherche.</p>
                </div>
            <?php else: ?>
                <?php foreach($vehicules as $v): ?>
                    <div class="bg-white rounded-3xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 group">
                        <!-- Image -->
                        <div class="h-56 bg-gray-200 relative">
                            <img src="<?= $v['image'] ?? 'https://via.placeholder.com/400x250' ?>" 
                                 alt="<?= $v['modele'] ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-4 left-4">
                                <span class="bg-white/90 backdrop-blur px-3 py-1 rounded-full text-xs font-bold text-gray-800 shadow-sm">
                                    <?= htmlspecialchars($v['categorie_nom'] ?? 'Auto') ?>
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h3 class="text-xl font-bold text-gray-900"><?= htmlspecialchars($v['marque']) ?></h3>
                                    <p class="text-gray-500"><?= htmlspecialchars($v['modele']) ?></p>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-black text-blue-600"><?= htmlspecialchars($v['prix_jours']) ?>€</span>
                                    <p class="text-xs text-gray-400">/ jour</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-4 mb-6 mt-4 text-sm text-gray-600">
                                <span class="flex items-center gap-1">⛽ Essence</span>
                                <span class="flex items-center gap-1">⚙️ Manuelle</span>
                            </div>

                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="client/reserver_vehicule.php?id=<?= $v['id_v'] ?>" 
                                   class="block w-full text-center bg-gray-900 text-white font-bold py-3 rounded-xl hover:bg-blue-600 transition-colors">
                                    Réserver maintenant
                                </a>
                            <?php else: ?>
                                <a href="login.php" 
                                   class="block w-full text-center bg-gray-100 text-gray-400 font-bold py-3 rounded-xl cursor-not-allowed">
                                    Connectez-vous pour réserver
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <?php if($totalPages > 1 && !$search && !$cat_id): ?>
            <div class="flex justify-center mt-12 gap-2">
                <?php for($i=1; $i<=$totalPages; $i++): ?>
                    <a href="../vehicule/index?page=<?= $i ?>&limit=<?= $limit ?>" 
                       class="w-10 h-10 flex items-center justify-center rounded-xl font-bold <?= $page == $i ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 border hover:bg-gray-100' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer class="bg-gray-900 text-gray-500 py-10 text-center mt-20">
        <p>&copy; 2024 MaBagnole Agence de Location. Tous droits réservés.</p>
    </footer>

</body>
</html>