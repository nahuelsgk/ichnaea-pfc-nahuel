<?php 

# Define if is development or production
$developmentEnviroment = 0;
if($developmentEnviroment == 1){
  # define the systems path
  define("ROOTPATH","/opt/lampp/htdocs/ichnaea/www");

  # Important: route from the document root to the base folder ichnaea application: 
  # backslah begin and end importatn, ifnot just an slash
  define("ROOT_FOLDER_ICHNAEA","/");

  # define the domain
  define("DOMAIN_ICHNAEA","dev.ichnaea.lsi.upc.edu");


}

else{
  define("ROOTPATH","/home/ichnaea/www");
  define("ROOT_FOLDER_ICHNAEA","/");
  define("DOMAIN_ICHNAEA","ichnaea.lsi.upc.edu");
  define("SMARTY_DIR","/home/ichnaea/www/lib/SmartyTemplating/");
}

define("WORKPATH",ROOTPATH.ROOT_FOLDER_ICHNAEA);
define("ROOT_DOMAIN_ICHNAEA",DOMAIN_ICHNAEA.ROOT_FOLDER_ICHNAEA);
define("ICHNAEA_CORE",WORKPATH."app/");
define("CONTROLLERS_PATH",WORKPATH."app/Controllers/");
<<<<<<< HEAD
define("VIEWS_PATH",WORKPATH."templates/Views/");

=======
define("VIEWS_PATH",WORKPATH."templates/Views");
define("RELATIVE_SMARTY_VIEWS","Views");
>>>>>>> d8d6a852d053575cd5aa7a2dffa0d1672ea31728
?>
