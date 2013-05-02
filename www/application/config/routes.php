<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

//API Routes
$route['api/project/(:num)/(:any)'] 			= 'api/projectapi/project/id/$1/$2';

$route['api/single_variable_api/format/json']	= 'api/single_variable_api/single_variable/format/json';

$route['api/seasons_api/(:num)/format/json']	= 'api/seasons_api/season/svid/$1/format/json';
$route['api/seasons']							= 'api/seasons_api/season/format/json';
$route['api/seasons/(:num)']					= 'api/seasons_api/season/id/$1/format/json';

$route['api/variable/(:num)']					= 'api/variable_api/variable/id/$1/format/json';
$route['api/variable']							= 'api/variable_api/variable/format/json';

$route['api/matrix/(:num)/variable']			= 'api/matrixs_api/variable/id/$1/format/json';
$route['api/matrix/(:num)/content']				= 'api/matrixs_api/content/id/$1/format/json';
$route['api/matrix/(:num)/sample']				= 'api/matrixs_api/sample/id/$1/format/json';


//Application Routes
$route['matrix/(:num)']					= 'matrix/configuration/$1';
$route['matrix/(:num)/view']			= 'matrix/view/$1';
$route['matrix/(:num)/view2']			= 'matrix/view2/$1';
$route['matrix/(:num)/jqgrid']			= 'matrix/view3/$1';
$route['matrix/(:num)/own']			= 'matrix/view4/$1';
$route['project/(:num)'] 				= 'project/view/$1';
$route['project/logout']				= 'project/logout';

//Logins
$route['verifylogin']					= 'login/verifylogin';
$route['signin']						= 'login/signin';
$route['signin/created']				= 'login/created';
$route['default_controller'] 			= 'login';
$route['404_override'] = '';


/* End of file routes.php */
/*la Location: ./application/config/routes.php */