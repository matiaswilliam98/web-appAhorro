<?php

class View{
    public $d;
    function __construct(){
    }

    function render($nombre, $data = []){
        $this->d = $data;
        
        $filePath = 'views/' . $nombre . '.php';
        if (file_exists($filePath)) {
            //
            $this->handleMessages();
            //
            require $filePath;
        } else {
            // Maneja el error si el archivo de vista no existe
            error_log("View file not found: " . $filePath);
            echo "View file not found: " . $filePath;
        }
    }
    private function handleMessages(){
        if(isset($_GET['success']) && isset($_GET['error'])){
            //error
        }else if(isset($_GET['success'])){
            $this->handleSuccess();
        }else if(isset($_GET['error'])){
            $this->handleError();
        }
}
private function handleError(){
    $hash = $_GET['error'];
    $error = new ErrorMessages();
    if($error->existsKey($hash)){
$this->d['error'] = $error->get($hash);

    }
}
    
private function handleSuccess(){
    $hash = $_GET['success'];
    $success = new SuccessMessages();
    if($success->existsKey($hash)){
        $this->d['success'] = $success->get($hash);

    }


}
public function showMessages(){
    $this->showErrors();
        $this->showSuccess();
}
public function showErrors(){
    if(array_key_exists('error', $this->d)){
        echo '<div class="error">'.$this->d['error'].'</div>';
    }
}  
public function showSuccess(){
if(array_key_exists('success', $this->d)){
    echo '<div class="success">'.$this->d['success'].'</div>';
}
}  
        
        //require 'views/' . $nombre . '.php';
        //$this->handleMessages();
    //}
    
}
?>