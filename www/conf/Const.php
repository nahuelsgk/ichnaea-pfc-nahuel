<?php 

# Define if is development or production
$developmentEnviroment = 1;

if($developmentEnviroment){
  define("WORKPATH","/opt/lampp/htdocs/ichnaea/www/");
  define("SMARTY_DIR",WORKPATH."lib/SmartyTemplating/");
}

else{
  #Here we have to define the prod path variables
  
}
?>
