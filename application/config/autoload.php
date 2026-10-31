<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$autoload['packages'] = array();
$autoload['libraries'] = array('database', 'session', 'form_validation');
$autoload['drivers'] = array();
$autoload['helper'] = array('url', 'form', 'security', 'text', 'date', 'app');
$autoload['model'] = array();
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array(
    'user_model',
    'role_model',
    'trade_model',
    'specialty_model',
    'provider_profile_model',
    'service_request_model',
    'quote_model',
    'service_model',
    'rating_model',
    'complaint_model',
    'availability_model'
);
