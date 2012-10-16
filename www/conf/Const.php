<?php 

# Define if is development or production
$developmentEnviroment = 1;
if($developmentEnviroment){
  define("ROOTPATH","/opt/lampp/htdocs");
  //Important: route from the document root to the base folder application: backslah begin and end importatn, ifnot just an slash
  define("ROOT_FOLDER_SERVER_APP","/ichnaea/www/");
}

else{
  #Here we have to define the prod path variables
  
}
define("WORKPATH",ROOTPATH.ROOT_FOLDER_SERVER_APP);
define("SMARTY_DIR",WORKPATH."lib/SmartyTemplating/");

?>
