<?php
// Set Content-Type to application/json
header('Content-Type: application/json');

require_once 'config/fluxo.php';
// require_once APPROOT . '/classes/Database.php';
// require_once APPROOT . '/classes/Authenticator.php';
// $authenticator = new Authenticator();

$method = $_SERVER['REQUEST_METHOD'];

$requestUri = explode('?', $_SERVER['REQUEST_URI'])[0];
$path = explode('/', trim($requestUri, '/'));

if ($path[0] === 'wgv_digiNet') {
    require_once APPROOT . '/routes/wgv_digiNet.php';
    exit;
}

http_response_code(404); // Not Found
echo json_encode(['error' => 'Route Not Found']);
exit;

