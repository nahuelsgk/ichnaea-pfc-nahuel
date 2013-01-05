<?php 

/*
  This class resolves the queried path inside the framework.
  Has all the necessary info to display the view.
*/

class Handler
{
  
  private $query;
  private $controller_path;
  private $view;
  private $smarty;  
  private $full_path;
  private $ajax_request = false;
  public function __construct(){
    $this->query=$_SERVER['PATH_INFO'] ; 
    $this->handler=$this;
    $this->resolveControllerPath();
    $this->smarty = new Smarty();
    $this->smarty->debugging = false;
    $this->smarty->caching = false;
    $this->full_path = $this->resolveFullPath();
    $this->ajax_request = $this->detectTypeOfRequest();
  }  

  private function detectTypeOfRequest(){
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
      // If its an ajax request execute the code below       
      //echo 'This is an ajax request!';   
      return true;
    }
    //if it's not an ajax request echo the below.
    //echo 'This is clearly not an ajax request!';
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
  *Resolves the main controller and the view needed
  */
  private function resolveControllerPath()
  {
    #Go to the queried template
    if(preg_match("/(.*?)$/",$this->query,$view)){  
      $this->view = $view[1];
    }

    # If empty, redirect to the login
    else if($this->query == ''){
      redirectLoginRegistration();  
    }
  } 
  
  /*
  * Executes the main controller
  */
  public function executeController(){
    $path = RELATIVE_SMARTY_VIEWS . $this->view . ".tpl";
    $output = $this->smarty->templateExists($path);
    if ($output === FALSE){
      $this->smarty->display(RELATIVE_SMARTY_VIEWS . "/notfound.tpl");
    }
    else if ($this->ajax_request == false) $this->smarty->display($path);
    else {
      global $globalparams;
      $globalparams->setAjaxParams();
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
