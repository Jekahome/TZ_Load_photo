<?php
namespace App\Controller;
use \App\Model\Model;

class Controller{

    function __construct()
    {
            if(isset($_POST['files'])){
                (new Model($_POST['files']));
                exit;
            }
            else if( $_SERVER['REQUEST_METHOD'] == 'GET'){

                Model::clear();
                require_once dirname(__DIR__).'/View/layout.php';
            }else{
                header('Location: '.WEB);
                exit;
            }
    }

}
