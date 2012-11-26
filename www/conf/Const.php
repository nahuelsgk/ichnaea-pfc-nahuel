<?php 

# Define if is development or production
$developmentEnviroment = 1;
if($developmentEnviroment){
  # define the systems path
  define("ROOTPATH","/opt/lampp/htdocs/ichnaea/www");

  # Important: route from the document root to the base folder ichnaea application: 
  # backslah begin and end importatn, ifnot just an slash
  define("ROOT_FOLDER_ICHNAEA","/");

  # define the domain
  define("DOMAIN_ICHNAEA","dev.ichnaea.lsi.upc.edu");


}

else{
  define("ROOTPATH","/opt/lampp/htdocs/ichnaea/www");
  define("ROOT_FOLDER_ICHNAEA","/");
  define("DOMAIN_ICHNAEA","dev.ichnaea.lsi.upc.edu");
}

define("WORKPATH",ROOTPATH.ROOT_FOLDER_ICHNAEA);
define("ROOT_DOMAIN_ICHNAEA",DOMAIN_ICHNAEA.ROOT_FOLDER_ICHNAEA);
define("CONTROLLERS_PATH",WORKPATH."app/Controllers/");
define("VIEWS_PATH",WORKPATH."templates/Views/");
?>
