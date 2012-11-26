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
    $this->query=$_SERVER['REQUEST_URI'] ; 
    $this->handler=$this;
    $this->resolveControllerPath();
    $this->smarty = new Smarty();
    $this->smarty->debugging = false;
    $this->smarty->caching = false;
  }
  
  //Resolves the main controller and the view needed
  private function resolveControllerPath()
  {
    $explode = explode(ROOT_FOLDER_ICHNAEA,$this->query);
    
    #Go to the queried template
    if(preg_match("/(.*?)$/",$explode[1],$view)){  
      $this->view = $view[1];
    }

    # If empty, redirect to the login
    else if($explode[1] == ''){
      redirectLoginRegistration();  
    }
  } 
  
  #Executes the main controller
  public function executeController(){
    $output = $this->smarty->templateExists("Views/".$this->view.".tpl");
    if ($output === FALSE){
      $this->smarty->display("Views/notfound.tpl");
    }
    else $this->smarty->display("Views/".$this->view.".tpl");
  }

  public function getView(){
    return $this->view;
  }
}

?>
