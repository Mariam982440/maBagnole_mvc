<?php
require_once '../classes/database.php';
require_once '../classes/vehicule.php';
require_once '../classes/reservation.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$vehiculeManager = new Vehicule();
$reservationManager = new Reservation();

$id_v = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$vehicule = $vehiculeManager->getDetails($id_v);

if (!$vehicule) {
    header("Location: index.php");
    exit;
}

$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $date_debut = $_POST['date_debut'];
    $date_fin   = $_POST['date_fin'];
    $lieu_prise = trim($_POST['lieu_prise']);
    $lieu_retour = trim($_POST['lieu_retour']);

    // Validation simple des dates
    if ($date_fin < $date_debut) {
        $status = "error";
        $message = "La date de retour ne peut pas être avant la date de prise en charge.";
    } else {
        // On remplit l'objet réservation (utilise tes setters magiques)
        $reservationManager->user_id = $_SESSION['user_id'];
        $reservationManager->vehicule_id = $id_v;
        $reservationManager->date_debut = $date_debut;
        $reservationManager->date_fin = $date_fin;
        $reservationManager->lieu_prise = $lieu_prise;
        $reservationManager->lieu_retour = $lieu_retour;

        // Appel de la méthode reserver() qui utilise CALL AjouterReservation(...)
        if ($reservationManager->reserver()) {
            $status = "success";
            $message = "Votre réservation a été enregistrée avec succès !";
        } else {
            $status = "error";
            $message = "Une erreur est survenue. Le véhicule est peut-être déjà réservé.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver <?= htmlspecialchars($vehicule['marque']) ?> - MaBagnole</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">

    <!-- NAVIGATION -->
    <nav class="bg-slate-800 text-white p-4">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <a href="index.php" class="font-bold text-xl">MaBagnole 🚗</a>
            <a href="index.php" class="text-sm hover:underline">Retour au catalogue</a>
        </div>
    </nav>

    <div class="max-w-5xl mx-auto my-10 px-4 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- RÉCAPITULATIF VÉHICULE (Gauche) -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-200">
                <img src="<?= $vehicule['image'] ?? 'https://via.placeholder.com/400x250' ?>" class="w-full h-48 object-cover">
                <div class="p-6">
                    <span class="text-xs font-bold text-blue-600 uppercase"><?= htmlspecialchars($vehicule['categorie_nom']) ?></span>
                    <h2 class="text-2xl font-bold text-gray-800 mt-1"><?= htmlspecialchars($vehicule['marque']) ?></h2>
                    <p class="text-gray-500 mb-4"><?= htmlspecialchars($vehicule['modele']) ?></p>
                    
                    <div class="border-t pt-4 flex justify-between items-center">
                        <span class="text-gray-600">Prix par jour</span>
                        <span class="text-xl font-bold text-gray-900"><?= htmlspecialchars($vehicule['prix_jours']) ?>€</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- FORMULAIRE DE RÉSERVATION (Droite) -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Détails de votre réservation</h3>

                <!-- Messages Alertes -->
                <?php if($message): ?>
                    <div class="mb-6 p-4 rounded-xl <?= $status === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= $message ?>
                        <?php if($status === 'success'): ?>
                            <br><a href="index.php" class="underline font-bold">Retourner à l'accueil</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form action="reserver_vehicule.php?id=<?= $id_v ?>" method="POST" class="space-y-6">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Dates -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de prise en charge</label>
                            <input type="date" name="date_debut" required min="<?= date('Y-m-d') ?>"
                                class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Date de restitution</label>
                            <input type="date" name="date_fin" required min="<?= date('Y-m-d') ?>"
                                class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>

                        <!-- Lieux -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de prise en charge</label>
                            <input type="text" name="lieu_prise" placeholder="Ex: Agence Aéroport Casablanca" required
                                class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Lieu de restitution</label>
                            <input type="text" name="lieu_retour" placeholder="Ex: Agence Centre Ville" required
                                class="w-full p-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none">
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-xl flex items-start gap-3">
                        <span class="text-blue-500 text-xl">ℹ️</span>
                        <p class="text-sm text-blue-800">
                            Le paiement s'effectue directement en agence lors de la récupération du véhicule. 
                            Une pièce d'identité et un permis de conduire valide vous seront demandés.
                        </p>
                    </div>

                    <button type="submit" 
                        class="w-full bg-blue-600 text-white font-bold py-4 rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition-all active:scale-95">
                        Confirmer ma réservation
                    </button>
                </form>
            </div>
        </div>
    </div>

</body>
</html>