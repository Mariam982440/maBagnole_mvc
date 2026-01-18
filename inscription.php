<?php
require_once 'classes/Database.php';
require_once 'classes/User.php';

session_start();

$erreur = "";
$succes = "";

// traitement de l'inscription
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $email = trim($_POST['email']);
    $mdp = $_POST['password'];
    $mdp_confirm = $_POST['password_confirm'];

    // validation simple
    if (!empty($nom) && !empty($email) && !empty($mdp)) {
        if ($mdp !== $mdp_confirm) {
            $erreur = "Les mots de passe ne correspondent pas.";
        } else {
            $user = new User();
            try {
                // appel de la méthode inscrire de la classe user
                $resultat = $user->inscrire($nom, $email, $mdp);
                
                if ($resultat === true) {
                    $succes = "Inscription réussie ! Vous pouvez maintenant vous <a href='login.php'>connecter</a>.";
                }
            } catch (Exception $e) {
                // on attrape l'erreur si l'email existe déjà (lancée par la classe User)
                $erreur = $e->getMessage();
            }
        }
    } else {
        $erreur = "Tous les champs sont obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>MaBagnole - Inscription</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .reg-container { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); width: 400px; }
        h2 { text-align: center; color: #333; margin-bottom: 20px; }
        .error { background: #fee; color: #c00; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; border: 1px solid #fcc; }
        .success { background: #efe; color: #080; padding: 10px; border-radius: 4px; margin-bottom: 15px; text-align: center; border: 1px solid #cfc; }
        label { display: block; margin-bottom: 5px; color: #666; }
        input { width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 4px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background-color: #007bff; border: none; color: white; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        p { text-align: center; margin-top: 15px; font-size: 0.9em; }
        a { color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

<div class="reg-container">
    <h2>Créer un compte 🚗</h2>

    <?php if ($erreur): ?>
        <div class="error"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <?php if ($succes): ?>
        <div class="success"><?php echo $succes; ?></div>
    <?php endif; ?>

    <form action="inscription.php" method="POST">
        <label for="nom">Nom complet</label>
        <input type="text" name="nom" id="nom" placeholder="Ex: Jean Dupont" required>

        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="jean@exemple.com" required>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" placeholder="Minimum 6 caractères" required>

        <label for="password_confirm">Confirmer le mot de passe</label>
        <input type="password" name="password_confirm" id="password_confirm" placeholder="Répétez le mot de passe" required>

        <button type="submit">S'inscrire</button>
    </form>

    <p>Déjà inscrit ? <a href="login.php">Se connecter</a></p>
</div>

</body>
</html>