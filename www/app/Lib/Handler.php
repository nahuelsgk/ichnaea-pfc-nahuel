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
  private $document_root;
  private $params;
  private $smarty;  
  
  public function __construct($query){
    $this->query=$query; 
    $this->resolveControllerPath();
    $this->smarty = new Smarty;
    $this->smarty->debugging = false;
    $smarty->caching = false;
  }
  
  //Resolves the main controller and the view needed
  private function resolveControllerPath()
  {
    $explode = explode(ROOT_FOLDER_ICHNAEA,$this->query);
    
    #Go to the queried template
    if(preg_match("/(.*?)$/",$explode[1],$view)){  
      $this->document_root = "./template/Views/";
      $this->view = $view[1];
    }

    # If empty, redirect to the login
    if($explode[1] == ''){
      redirectLoginRegistration();  
    }
    #Should return a 404 page
  } 
  
  #Executes the main controller
  public function executeController(){
    $this->smarty->display("Views/".$this->view.".tpl");
  }

  public function getView(){
    return $this->view;
  }
}

?>
