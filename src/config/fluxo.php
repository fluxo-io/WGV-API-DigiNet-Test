<?php

define('APPROOT', dirname(dirname(__FILE__)));

return [
    'database' => [
        'host' => '', // Datenbank-Host
        'db' => '',   // Datenbank-Name
        'user' => '', // Benutzername
        'pass' => '', // Passwort
        'charset' => 'utf8mb4',
    ],
    'api' => [
        'url' => 'https://nngm-test.medicalsyn.com/api/v1.0/xml/patient', // API-URL
        'ssl_verify' => true, // SSL-Verifizierung
    ],
    'auth' => [
        'api_key_header' => 'HTTP_FLUXO_API_KEY', // HTTP-Header für API-Keys
    ],
];