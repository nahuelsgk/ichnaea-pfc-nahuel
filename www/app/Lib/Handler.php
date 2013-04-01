<?php 

/*
  This class resolves the queried path inside the framework.
  Has all the necessary info to display the view.
*/
use \Api;

class Handler
{
  
  private $query;
  private $controller_path;
  private $view;
  private $smarty;  
  private $full_path;
  private $ajax_request = false;
  private $api = false;

  public function __construct(){
    $this->query=$_SERVER['PATH_INFO'] ; 
    $this->handler=$this;
    $this->resolveControllerPath();
    $this->smarty = new Smarty();
    $this->smarty->debugging = false;
    $this->smarty->caching = false;
    $this->full_path = $this->resolveFullPath();
    
    //If it wasn't resolve as ajax before, try for the bad code ajax first version
    if ($this->ajax_request == false) $this->ajax_request = $this->detectTypeOfRequest();
  }  

  private function detectTypeOfRequest(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
       !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
       strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
         return true;
    }
    return false;
  }
 
  private function resolveFullPath(){
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $uri      = $_SERVER['REQUEST_URI'];
    $currentUrl = $protocol . '://' . $host . $uri;
    return $currentUrl;		    
  }
  /*
  *Resolves the library to use: the app controllers or the reponse event
  */
  private function resolveControllerPath()
  {
    //This wil resolve as an pseudo api
    if(preg_match("/api\/(.*?)$/", $this->query, $view)){
      $this->ajax_request = true;
      $this->api = true;
    }

    #Resolve the queried 
    else if(preg_match("/(.*?)$/", $this->query, $view)){
      $this->view = $view[1];
    }

    # If empty, redirect to the login
    else if($this->query == ''){
      redirectLoginRegistration();  
    }
  } 
  
  /*
  * Resolves and Executes the two  main controller: MVC or Api
  */
  public function executeController(){
    $path = RELATIVE_SMARTY_VIEWS . $this->view . ".tpl";
    $output = $this->smarty->templateExists($path);
    
    //MVC controller
    if ($this->ajax_request == false){
      if ($output === FALSE){
        $this->smarty->display(RELATIVE_SMARTY_VIEWS . "/notfound.tpl");
      }
      else if ($this->ajax_request == false) {
        $this->smarty->display($path);
      }
    }

    //Api: Event response controller. Two versions. We must have just one and single controller
    else {
      if($this->api==false){
        global $globalparams;
        $globalparams->setAjaxParams();
      }
      else{
        $api = new Api\Api();
      }
    }
  }

  public function getView(){
    return $this->view;
  }

  public function getFullPath(){
    return $this->full_path;
  }

  public function isAjaxRequest(){
    return $this->ajax_request;
  }
}

?>
