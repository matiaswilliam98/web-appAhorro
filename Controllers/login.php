
<?php
class Login extends SessionController{
    function __construct(){
        parent::__construct();
       
        error_log('Login::construct -> inicio de login');
        
        //parent::__construct();
    }
    function render(){

        error_log('Login::render -> carga el index del login');
    $this->view->render('login/index');
    }

    function authenticate(){

    if( $this->existPOST(['username', 'password']) ){
         $username = $this->getPost('username');
      $password = $this->getPost('password');

          if($username == '' || empty($username) || $password == '' || empty($password)){
              $this->redirect('', ['error' => ErrorMessages::ERROR_LOGIN_AUTHENTICATE_EMPTY]);
              //son cambios
               return;
           }
            $user = $this->model->login($username, $password);

           if($user != NULL){

              error_log('Login::authenticate -> autenticación exitosa');    
                $this->initialize($user);
                //cambios
              return;
            }else{
               $this->redirect('', ['error' => ErrorMessages::ERROR_LOGIN_AUTHENTICATE_DATA]);
                //cambios
            return;
           }
       }else{
           $this->redirect('', ['error' => ErrorMessages::ERROR_LOGIN_AUTHENTICATE]);
           
       }

    }

}
?>