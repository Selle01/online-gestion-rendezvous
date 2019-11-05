<?php
#lancer le server :  
#commande : php -S localhost:8007 -d display_errors=1 -t public/

require '../vendor/autoload.php';
//
$app = new \Framework\App();

$app->run();
