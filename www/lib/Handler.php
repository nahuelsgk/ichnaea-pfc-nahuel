<?php 
/*This resolves the template have to use depending the url*/
function handler(){
  $url_queried = $_GET["url"];
  if($url_queried == "login"){
    return "./app/Login/main.php";
  }
  return "Must be a 404 page";
}
?>
