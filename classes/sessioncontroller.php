<?php
/**
 * Controlador que también maneja las sesiones
 */
require_once 'classes/session.php';
require_once 'models/usermodel.php';


class SessionController extends Controller {
    private $userSession;
    private $username;
    private $userid;
    private $session;
    private $sites;
    private $user;
    private $defaultSites;

    function __construct() {
        parent::__construct();
        $this->init();
    }

    function init() {
        $this->session = new Session();
        $json = $this->getJSONFileConfig();
        $this->sites = $json['sites'];
        $this->defaultSites = $json['default-sites'];
        $this->validateSession();
    }

    private function getJSONFileConfig() {
        $string = file_get_contents('config/access.json');
        $json = json_decode($string, true);
        return $json;
    }

    public function validateSession() {
      error_log('SESSIONCONTROLLER::validateSession');

      if ($this->existsSession()) {
           $role = $this->getUserSessionData()->getRole();
            error_log('SESSIONCONTROLLER::validateSession -> Usuario logueado con rol: ' . $role);

            if ($this->isPublic()) {
                $this->redirectDefaultSiteByRole($role);
                error_log("SESSIONCONTROLLER::validateSession -> sitio público, redirige al main de cada rol");
            } else {
                if ($this->isAuthorized($role)) {
                    error_log("SESSIONCONTROLLER::validateSession -> usuario autorizado para esta página");
                } else {
                    error_log("SESSIONCONTROLLER::validateSession -> usuario NO autorizado, redirigiendo");
                    $this->redirectDefaultSiteByRole($role);
                }
            }
        } else {
            if ($this->isPublic()) {
                error_log("SESSIONCONTROLLER::validateSession -> página pública, no se requiere sesión");
            } else {
                error_log("SESSIONCONTROLLER::validateSession -> página privada, redirigiendo al login");
               //header('location: ' . constant('URL') . 'login');
               header('location: ' . constant('URL') . 'login');
              
            }
        }
       

    
    }

    function existsSession() {
        if (!$this->session->exists()) return false;
        if ($this->session->getCurrentUser() == NULL) return false;
        return true;
    }

    function getUserSessionData() {
        $id = $this->session->getCurrentUser();
        $this->user = new UserModel();
        $this->user->get($id);
        error_log('sessionController::getUserSessionData -> ' . $this->user->getUsername());
        return $this->user;
    }

    function isPublic() {
        $currentURL = $this->getCurrentPage();
        error_log("sessionController::isPublic(): currentURL => " . $currentURL);
       $currentURL = preg_replace("/\?.*/", "", $currentURL);
        error_log('SESSIONCONTROLLER::isPublic -> página actual: ' . $currentURL);

        for ($i = 0; $i < sizeof($this->sites); $i++) {
          if ($currentURL === $this->sites[$i]['site'] && $this->sites[$i]['access'] === 'public') {
               return true;
            }
        }
        return false;
    
   
    return false;
    }

    function getCurrentPage() {
        $actualLink = trim("$_SERVER[REQUEST_URI]");
        $url = explode('/', $actualLink);
        error_log('SESSIONCONTROLLER::getCurrentPage -> ' . (isset($url[2]) ? $url[2] : ''));
        return isset($url[2]) ? $url[2] : '';
    }

    private function redirectDefaultSiteByRole($role) {



  
   
       $url = '';
      for($i = 0; $i < sizeof($this->sites); $i++){
          if($this->sites[$i]['role'] === $role){
             error_log('SessionController::redirectDefaultSiteByRole -> role = ' . $role);
             $url = ''.$this->sites[$i]['site'];
               error_log('SessionController::redirectDefaultSiteByRole -> url = ' . $url);
                break;
            }

       }
     header('location: ' .constant('URL') .$url);
    
     
    }

    private function isAuthorized($role) {
        $currentURL = $this->getCurrentPage();
        error_log("sessionController::isAuthorized(): currentURL => " . $currentURL);
        $currentURL = preg_replace("/\?.*/", "", $currentURL);
       //error_log('SESSIONCONTROLLER::isAuthorized -> página actual: ' . $currentURL);

        for ($i = 0; $i < sizeof($this->sites); $i++) {
            if ($currentURL === $this->sites[$i]['site'] && $this->sites[$i]['role'] === $role) {
                return true;
            }
        }
        return false;
    }

    function initialize($user) {
        $this->session->setCurrentUser($user->getId());
        $this->authorizeAccess($user->getRole());
    }

    function authorizeAccess($role) {
        switch ($role) {
            case 'user':
                $this->redirect($this->defaultSites['user']);
                break;
            case 'admin':
                $this->redirect($this->defaultSites['admin']);
                break;
            default:
                error_log('SESSIONCONTROLLER::authorizeAccess -> rol no autorizado');
        }
    }

    function logout() {
        $this->session->closeSession();
    }


}
?>
