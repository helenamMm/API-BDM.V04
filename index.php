<?php
include "inc/bootstrap.php";

//header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight OPTIONS request early and exit
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$uri = explode( '/', $uri );
//print_r($uri);

if($uri[3] === 'user'){
    //print "ayuda";
    include "Controller/Api/UserController.php";
    $objFeedController = new UserController();
    $strMethodName = $uri[4] . 'Action';
    //print $strMethodName;
    $objFeedController->{$strMethodName}();
} 
//cambialo a 4 si algo pasa 
else if($uri[3] === 'publicacion'){
    include "Controller/Api/PublicacionController.php";
    $objFeedController = new PublicacionController();
    $strMethodName = $uri[4] . 'Action';
    $objFeedController->{$strMethodName}();
}
else if($uri[3] === 'favorito'){
    include "Controller/Api/FavoritoController.php";
    $objFeedController = new FavoritoController();
    $strMethodName = $uri[4] . 'Action';
    $objFeedController->{$strMethodName}();
}
else if ($uri[3] != 'user' && $uri[3] != 'publicacion') {
    header("HTTP/1.1 404 Not Found");
    echo "aqui me quedo";
    //echo $uri[4];
    
    exit();
} 

?>