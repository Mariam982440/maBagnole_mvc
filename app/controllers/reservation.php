<?php
class VehiculeController 
{
    public function index(){

        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $resManager = new Reservation();
        $avisManager = new Avis();

        $message = "";

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

        $mesReservations = $resManager->listerParClient($user_id);

        require_once '../app/views/reservation/index.php';
    }
}
