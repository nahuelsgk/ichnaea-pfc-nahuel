<?php 

# Define if is development or production
$developmentEnviroment = 1;
if($developmentEnviroment){
  # define the systems path
  define("ROOTPATH","/opt/lampp/htdocs");

  # Important: route from the document root to the base folder ichnaea application: 
  # backslah begin and end importatn, ifnot just an slash
  define("ROOT_FOLDER_ICHNAEA","/ichnaea/www/");

  # define the domain
  define("DOMAIN_ICHNAEA","localhost");


}

else{
  # Here we have to define the prod path variables
  
}
define("WORKPATH",ROOTPATH.ROOT_FOLDER_ICHNAEA);
define("SMARTY_DIR",WORKPATH."lib/SmartyTemplating/");
define("ROOT_DOMAIN_ICHNAEA",DOMAIN_ICHNAEA.ROOT_FOLDER_ICHNAEA);
define("CONTROLLERS_PATH",WORKPATH."app/Controllers/");
define("VIEWS_PATH",WORKPATH."templates/Views/");
?>
