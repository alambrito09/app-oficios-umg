<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'auth/login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// --- Autenticación ---
$route['login'] = 'auth/login';
$route['logout'] = 'auth/logout';
$route['register'] = 'auth/register';
$route['recover'] = 'auth/recover';
$route['service_request/(.*)'] = 'ServiceRequest/$1';

// --- Cliente ---
$route['client/dashboard'] = 'client/dashboard';
$route['client/search'] = 'client/search';
$route['client/results'] = 'client/results';
$route['client/profile/(:num)'] = 'client/profile/$1';
$route['client/request/create'] = 'ServiceRequest/create';
$route['client/request/(:num)'] = 'ServiceRequest/view/$1';
$route['client/quotes/(:num)/approve'] = 'quote/approve/$1';
$route['client/history'] = 'client/history';
$route['client/rating/(:num)'] = 'rating/create/$1';
$route['client/complaints'] = 'complaint/client_list';
$route['client/complaint/create/(:num)'] = 'complaint/create/$1';

// --- Prestador ---
$route['provider/dashboard'] = 'provider/dashboard';
$route['provider/requests'] = 'provider/requests';
$route['provider/request/(:num)/respond'] = 'ServiceRequest/respond/$1';
$route['provider/quote/create/(:num)'] = 'quote/create/$1';
$route['provider/quote/(:num)/edit'] = 'quote/edit/$1';
$route['provider/services'] = 'provider/services';
$route['provider/service/(:num)/progress'] = 'provider/progress/$1';
$route['provider/profile'] = 'provider/profile';
$route['provider/complaints'] = 'complaint/provider_list';

// --- Administrador ---
$route['admin/dashboard'] = 'admin/dashboard';
$route['admin/users'] = 'admin/users';
$route['admin/trades'] = 'admin/trades';
$route['admin/complaints'] = 'admin/complaints';
$route['admin/reports'] = 'admin/reports';
$route['admin/verify/(:num)'] = 'admin/verify_provider/$1';
$route['admin/users/create'] = 'admin/create_user';
$route['admin/users/(:num)/status'] = 'admin/update_user_status/$1';

// --- Pago simulado ---
$route['client/payment/(:num)'] = 'payment/checkout/$1';
