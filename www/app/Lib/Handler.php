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
  
  public function __construct(){
    $this->query=$_SERVER['PATH_INFO'] ; 
    $this->handler=$this;
    $this->resolveControllerPath();
    $this->smarty = new Smarty();
    $this->smarty->debugging = false;
    $this->smarty->caching = false;
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
    else $this->smarty->display($path);
  }

  public function getView(){
    return $this->view;
  }
}

?>
