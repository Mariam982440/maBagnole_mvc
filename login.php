<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';
session_start();

// if (isset($_SESSION['user_id'])) {
//     header("Location: index.php");
//     exit;
// }

$erreur = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['password'];

    if (!empty($email) && !empty($mdp)) {
        $user = new User();
        
        if ($user->seConnecter($email, $mdp)) {
            // Redirection selon le rôle
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $erreur = "Email ou mot de passe incorrect.";
        }
    } else {
        $erreur = "Veuillez remplir tous les champs.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MaBagnole - Connexion</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .login-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 350px; }
        h2 { text-align: center; color: #333; }
        .error { color: red; font-size: 0.9em; margin-bottom: 10px; text-align: center; }
        input { width: 100%; padding: 10px; margin: 10px 0; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background-color: #28a745; border: none; color: white; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        p { text-align: center; font-size: 0.9em; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="login-container">
    <h2>MaBagnole </h2>
    <p>Connectez-vous pour louer un véhicule</p>

    <?php if ($erreur): ?>
        <div class="error"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="votre@email.com" required>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" placeholder="********" required>

        <button type="submit">Se connecter</button>
    </form>

    <p>Pas encore de compte ? <a href="inscription.php">S'inscrire ici</a></p>
</div>

</body>
</html>