<?php 
/*This resolves the template have to use depending the url*/
function handler(){
  printHTML("[Handler]: La url pedida es: ".$_SERVER['REQUEST_URI']);

  #The url request 
  $full_request = $_SERVER['REQUEST_URI'];
  $explode = explode(ROOT_FOLDER_SERVER_APP,$full_request);
  
  #resolve the path
  if($explode[1] == "login/"){
    return "./app/Login/main.php";
  }

  if($explode[1] == "home/"){
    return "./app/Home/main.php";
  }
  //By now, all the return pages goes to the login
  else return "./app/Login/main.php";

  return "Must be a 404 page";
}
?>
