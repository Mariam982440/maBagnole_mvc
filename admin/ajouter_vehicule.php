<?php
require_once '../classes/database.php';
require_once '../classes/vehicule.php';
require_once '../classes/categories.php';

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../client/login.php");
    exit;
}

$vehiculeManager = new Vehicule();
$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $marque = trim($_POST['marque']);
    $modele = trim($_POST['modele']);
    $prix   = trim($_POST['prix_jours']);
    $image  = trim($_POST['image']);
    $c_id   = (int)$_POST['category'];
    $dispo  = isset($_POST['disponibilite']) ? 1 : 0;

    if (!empty($marque) && !empty($modele) && !empty($prix)) {
        
        $vehiculeManager->marque = $marque;
        $vehiculeManager->modele = $modele;
        $vehiculeManager->prix_jours = $prix;
        $vehiculeManager->image = $image;
        $vehiculeManager->c_id = $c_id;
        $vehiculeManager->disponibilite = $dispo;

        if ($vehiculeManager->ajouter()) {
            $status = "success";
            $message = "Le véhicule <strong>$marque $modele</strong> a été ajouté avec succès.";
        } else {
            $status = "error";
            $message = "Erreur lors de l'ajout. Vérifiez si ce modèle n'existe pas déjà.";
        }
    } else {
        $status = "error";
        $message = "Veuillez remplir tous les champs obligatoires.";
    }
}

$categories = Categories::listerTout();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | Ajouter un véhicule</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <nav class="bg-slate-900 text-white p-4 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <div class="flex items-center gap-4">
                <span class="text-xl font-bold text-blue-500">AdminPanel</span>
                <span class="text-gray-400">|</span>
                <a href="dashboard.php" class="text-sm hover:text-white transition">Dashboard</a>
            </div>
            <a href="../client/logout.php" class="text-sm bg-red-600 px-3 py-1 rounded hover:bg-red-700">Déconnexion</a>
        </div>
    </nav>

    <div class="max-w-3xl mx-auto my-10 px-4">
        
        <a href="dashboard.php" class="inline-block mb-6 text-sm text-gray-500 hover:text-blue-600">
            ← Retour à la gestion
        </a>

        <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
            <div class="bg-slate-800 p-6 text-white">
                <h2 class="text-2xl font-bold">Ajouter un nouveau véhicule 🚗</h2>
                <p class="text-slate-400 text-sm">Remplissez les informations ci-dessous pour enrichir le catalogue.</p>
            </div>

            <div class="p-8">
                <?php if($message): ?>
                    <div class="mb-6 p-4 rounded-2xl <?= $status === 'success' ? 'bg-green-100 text-green-700 border border-green-200' : 'bg-red-100 text-red-700 border border-red-200' ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <form action="ajouter_vehicule.php" method="POST" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Marque -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Marque *</label>
                            <input type="text" name="marque" required placeholder="Ex: Toyota"
                                class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>

                        <!-- Modèle -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Modèle *</label>
                            <input type="text" name="modele" required placeholder="Ex: Yaris"
                                class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Prix Jours -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Prix par jour (€) *</label>
                            <input type="number" name="prix_jours" required placeholder="Ex: 45"
                                class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                        </div>

                        <!-- Catégorie -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Catégorie *</label>
                            <select name="category" required 
                                class="w-full p-3 border border-gray-200 rounded-xl bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none transition">
                                <?php foreach($categories as $c): ?>
                                    <option value="<?= $c['id_c'] ?>"><?= htmlspecialchars($c['nom']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Image URL -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Lien de l'image (URL)</label>
                        <input type="text" name="image" placeholder="https://image-voiture.com/photo.jpg"
                            class="w-full p-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition">
                    </div>

                    <!-- Disponibilité -->
                    <div class="flex items-center gap-3 bg-gray-50 p-4 rounded-xl">
                        <input type="checkbox" name="disponibilite" id="dispo" checked 
                               class="w-5 h-5 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                        <label for="dispo" class="text-sm font-medium text-gray-700 cursor-pointer">
                            Rendre ce véhicule immédiatement disponible à la location
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full bg-slate-800 text-white font-black py-4 rounded-2xl hover:bg-blue-600 shadow-lg transition-all active:scale-[0.98]">
                            ENREGISTRER LE VÉHICULE
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>