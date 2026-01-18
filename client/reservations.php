<?php
require_once '../classes/database.php';
require_once '../classes/reservation.php';
require_once '../classes/avis.php';

session_start();

// SÉCURITÉ : On vérifie si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$resManager = new Reservation();
$avisManager = new Avis();

$message = "";

// --- TRAITEMENT DE L'AJOUT D'AVIS ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajouter_avis'])) {
    try {
        $avisManager->note = (int)$_POST['note'];
        $avisManager->commentaire = trim($_POST['commentaire']);
        $avisManager->user_id = $user_id;
        $avisManager->vehicule_id = (int)$_POST['vehicule_id'];

        if ($avisManager->ajouter()) {
            $message = "<div class='bg-green-100 text-green-700 p-4 rounded-xl mb-6'>Merci ! Votre avis a été publié.</div>";
        }
    } catch (Exception $e) {
        $message = "<div class='bg-red-100 text-red-700 p-4 rounded-xl mb-6'>" . $e->getMessage() . "</div>";
    }
}

// --- RÉCUPÉRATION DES RÉSERVATIONS DU CLIENT ---
$mesReservations = $resManager->listerParClient($user_id);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations - MaBagnole</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 min-h-screen">

    <!-- NAVBAR -->
    <nav class="bg-slate-900 text-white p-4 shadow-lg">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="index.php" class="text-xl font-bold">MaBagnole 🚗</a>
            <div class="space-x-4">
                <a href="index.php" class="hover:text-blue-400">Catalogue</a>
                <a href="blog.php" class="hover:text-blue-400">Blog</a>
                <a href="logout.php" class="text-red-400 text-sm">Déconnexion</a>
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto my-10 px-4">
        <h1 class="text-3xl font-black text-slate-800 mb-8">Mon historique de location</h1>

        <?= $message ?>

        <?php if (empty($mesReservations)): ?>
            <div class="bg-white p-10 text-center rounded-3xl shadow-sm">
                <p class="text-gray-500">Vous n'avez pas encore effectué de réservation.</p>
                <a href="index.php" class="text-blue-600 font-bold mt-4 inline-block underline">Parcourir les véhicules</a>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 gap-6">
                <?php foreach ($mesReservations as $r): ?>
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col md:flex-row">
                        
                        <!-- Info Réservation -->
                        <div class="p-6 md:w-1/2 border-r border-gray-50">
                            <div class="flex items-center gap-4 mb-4">
                                <div class="bg-blue-100 text-blue-600 p-3 rounded-lg font-bold">
                                    #<?= $r['id_reservation'] ?>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold text-gray-800"><?= htmlspecialchars($r['marque'] . ' ' . $r['modele']) ?></h3>
                                    <p class="text-sm text-gray-500">Réservé le <?= date('d/m/Y', strtotime($r['created_at'])) ?></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-4 text-sm bg-gray-50 p-4 rounded-xl">
                                <div>
                                    <p class="text-gray-400 uppercase text-[10px] font-bold">Du</p>
                                    <p class="font-semibold text-gray-700"><?= date('d/m/Y', strtotime($r['date_debut'])) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($r['lieu_prise']) ?></p>
                                </div>
                                <div>
                                    <p class="text-gray-400 uppercase text-[10px] font-bold">Au</p>
                                    <p class="font-semibold text-gray-700"><?= date('d/m/Y', strtotime($r['date_fin'])) ?></p>
                                    <p class="text-xs text-gray-500"><?= htmlspecialchars($r['lieu_retour']) ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Formulaire Avis -->
                        <div class="p-6 md:w-1/2 bg-slate-50">
                            <h4 class="font-bold text-gray-700 mb-4">Donnez votre avis</h4>
                            <form action="reservations.php" method="POST" class="space-y-3">
                                <input type="hidden" name="vehicule_id" value="<?= $r['vehicule_id'] ?>">
                                
                                <div class="flex items-center gap-2">
                                    <label class="text-sm text-gray-600">Note :</label>
                                    <select name="note" class="p-1 border rounded bg-white outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                                        <option value="4">⭐⭐⭐⭐ (Très bien)</option>
                                        <option value="3">⭐⭐⭐ (Bien)</option>
                                        <option value="2">⭐⭐ (Moyen)</option>
                                        <option value="1">⭐ (Mauvais)</option>
                                    </select>
                                </div>

                                <textarea name="commentaire" placeholder="Qu'avez-vous pensé de ce véhicule ?" required
                                    class="w-full p-3 text-sm border rounded-xl outline-none focus:ring-2 focus:ring-blue-500"></textarea>

                                <button type="submit" name="ajouter_avis" 
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">
                                    Publier l'avis
                                </button>
                            </form>
                        </div>

                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

</body>
</html>