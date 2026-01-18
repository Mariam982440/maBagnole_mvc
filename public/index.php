<?php
require_once '../app/config/database.php';
require_once '../app/models/Article.php';
require_once '../app/models/Theme.php';
require_once '../app/models/Tag.php';
require_once '../app/models/commentaire.php';
require_once '../app/models/favoris.php';
require_once '../app/models/vehicule.php';


session_start();

$url = isset($_GET['url']) ? 
explode('/', filter_var(trim($_GET['url'], '/'), FILTER_SANITIZE_URL)) 
: ['blog', 'index'];
$role = $url[0];
if($role == 'admin'){
    $controllerName = $url[1] . 'Controller'; 
    $controllerFile = "../app/controllers/admin/" . $controllerName . ".php";
}else if($role == 'client') {
    $controllerName = $url[1] . 'Controller'; 
    $controllerFile = "../app/controllers/client/" . $controllerName . ".php";
}else{
    $controllerName = ucfirst($url[0]) . 'Controller'; 
    $controllerFile = "../app/controllers/" . $controllerName . ".php";
}
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controller = new $controllerName();
    
    $method = $url[2] ?? 'index';

    if (method_exists($controller, $method)) {

        $controller->$method();
    } else {
        var_dump($controller);
        die("Erreur 404 : Méthode non trouvée");
    }
} else {
    echo $controllerFile;
    var_dump($url); 
    die("Erreur 404 : Contrôleur non trouvé");
}

?>



