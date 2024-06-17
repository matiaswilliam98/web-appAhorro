<?php
require_once 'controllers/errores.php';

class App{
    
    function __construct(){

        $url = isset($_GET['url']) ? $_GET['url']:'';
        $url = rtrim($url, '/');
        $url = explode('/', $url);
        
        if(empty($url[0])){
            error_log('APP::construct-> no hay controlador especificado');
            $archivoController = 'controllers/login.php';
            require_once $archivoController;
            $controller = new Login();
            $controller->loadModel('login');
            $controller->render();
            return false;
       }
       $archivoController = 'controllers/' . $url[0] . '.php';
       if(file_exists($archivoController)){
        require_once $archivoController;

        $controller = new $url[0];
        $controller->loadModel($url[0]);

        if(isset($url[1])){
            if(method_exists($controller, $url[1])){
                if(isset($url[2])){
                    $nparam = sizeof($url) - 2;
                    $params = [];
                    for($i = 0; $i < $nparam; $i++){
                        array_push($params, $url[$i + 2]);
                    }
                    $controller->{$url[1]}($params);
                }else{
                    $controller->{$url[1]}();  
                }
            }else{
                $controller= new Errores();
                $controller->render();
            
            }
        }else{
            $controller->render();
        }
       }else{
       $controller= new Errores();
       $controller->render();
       }



    }
}
?>