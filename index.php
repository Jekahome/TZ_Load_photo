<?php

require_once __DIR__ . '/vendor/autoload.php';

require_once __DIR__.'/config/index.php';


ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('allow_url_fopen',1);

try{
    (new App\Controller\Controller());

}catch (Throwable $e){
    echo '<pre>';print_r($e);exit;
}
catch (Error $e){
    echo '<pre>';print_r($e);exit;
}
?>

