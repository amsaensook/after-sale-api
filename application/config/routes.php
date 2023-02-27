<?php
defined('BASEPATH') or exit('No direct script access allowed');

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
|    example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|    https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|    $route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|    $route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|    $route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:    my-controller/index    -> my_controller/index
|        my-controller/my-method    -> my_controller/my_method
 */
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = false;

// Auth API Routes
$route['login'] = 'api/auth/login';
$route['logout'] = 'api/auth/logout';
$route['validate_token'] = 'api/auth/validate_token';
$route['refresh_token'] = 'api/auth/refresh_token';

// User API Routes
$route['user'] = 'api/user/index';
//$route['user/:(any)'] = 'api/user/show/$1';
$route['create_user'] = 'api/user/create';
$route['update_user'] = 'api/user/update';
$route['delete_user'] = 'api/user/delete';

// Menu API Routes
$route['menu'] = 'api/menu/index';
//$route['menu/:(any)'] = 'api/menu/show/$1';
$route['create_menu'] = 'api/menu/create';
$route['update_menu'] = 'api/menu/update';
$route['delete_menu'] = 'api/menu/delete';
$route['parent_menu'] = 'api/menu/parent';

// Menu Type API Routes
$route['menu_type'] = 'api/MenuType/index';
//$route['menu_type/:(any)'] = 'api/MenuType/show/$1';

// Platform API Routes
$route['platform'] = 'api/platform/index';
//$route['platform/:(any)'] = 'api/platform/show/$1';

// Group API Routes
$route['group'] = 'api/group/index';
//$route['group/:(any)'] = 'api/group/show/$1';
$route['create_group'] = 'api/group/create';
$route['update_group'] = 'api/group/update';
$route['delete_group'] = 'api/group/delete';

// Grade API Routes
$route['grade'] = 'api/grade/index';
//$route['grade/:(any)'] = 'api/grade/show/$1';
$route['create_grade'] = 'api/grade/create';
$route['update_grade'] = 'api/grade/update';
$route['delete_grade'] = 'api/grade/delete';

// Permission API Routes
$route['permission'] = 'api/permission/index';
$route['create_permission'] = 'api/permission/create';

// ReceivePart API Routes (Mobile)
$route['receive_part'] = 'api/mobile/ReceivePart/index';
$route['receive_part_item'] = 'api/mobile/ReceivePart/item';
$route['update_receive_part'] = 'api/mobile/ReceivePart/update';
$route['exec_receive_part_transaction'] = 'api/mobile/ReceivePart/exec_transaction';

// SplitPart API Routes (Mobile)
$route['split_part'] = 'api/mobile/SplitPart/index';
$route['split_part_item'] = 'api/mobile/SplitPart/item';
$route['update_split_part'] = 'api/mobile/SplitPart/update';
$route['exec_split_part_transaction'] = 'api/mobile/SplitPart/exec_transaction';

// ReceiveSaleService API Routes (Mobile)
$route['receive_sale_service'] = 'api/mobile/ReceiveSaleService/index';
$route['receive_sale_service_item'] = 'api/mobile/ReceiveSaleService/item';
$route['update_receive_sale_service'] = 'api/mobile/ReceiveSaleService/update';
$route['exec_receive_sale_service_transaction'] = 'api/mobile/ReceiveSaleService/exec_transaction';

// RequestSaleService API Routes (Mobile)
$route['request_sale_service'] = 'api/mobile/RequestSaleService/index';
$route['request_sale_service_item'] = 'api/mobile/RequestSaleService/item';
$route['update_request_sale_service'] = 'api/mobile/RequestSaleService/update';
$route['exec_request_sale_service_transaction'] = 'api/mobile/RequestSaleService/exec_transaction';

// ReceiveReturn API Routes (Mobile)
$route['receive_return'] = 'api/mobile/ReceiveReturn/index';
$route['receive_return_item'] = 'api/mobile/ReceiveReturn/item';
$route['update_receive_return'] = 'api/mobile/ReceiveReturn/update';
$route['exec_receive_return_transaction'] = 'api/mobile/ReceiveReturn/exec_transaction';

// ReturnPart API Routes (Mobile)
$route['update_return_part'] = 'api/mobile/ReturnPart/update';

// CheckPart API Routes (Mobile)
$route['check_part'] = 'api/mobile/CheckPart/index';

// CountStock API Routes (Mobile)
$route['count_stock'] = 'api/mobile/CountStock/index';
$route['count_stock_item'] = 'api/mobile/CountStock/item';
$route['update_count_stock'] = 'api/mobile/CountStock/update';
$route['exec_count_stock_item'] = 'api/mobile/CountStock/exec_item';

