<?php

class Errores extends Controller{

    function __construct(){
        parent::__construct();
        error_log('Errores::construct -> inicio de Errores');
        //$this->view->render('errores/index');
    }
    function render(){
        //error_log('Login::render -> carga el index del login');
    $this->view->render('errores/index');
    }
}

?>