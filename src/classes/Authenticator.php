<?php
// src/classes/Authenticator.php

class Authenticator
{
    private $pdo;

    public function __construct()
    {
        require_once APPROOT . '/classes/Database.php';
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    public function authenticate($requiredRole = null)
    {
        $config = require APPROOT . '/config/fluxo.php';
        $apiKeyHeader = $config['auth']['api_key_header'];

        if (!isset($_SERVER[$apiKeyHeader])) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'No API key provided']);
            exit();
        }

        $providedApiKey = $_SERVER[$apiKeyHeader];

        $query = "SELECT role, id FROM api_keys WHERE api_key = :api_key AND active = 1 LIMIT 1";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindParam(':api_key', $providedApiKey);
        $stmt->execute();
        $result = $stmt->fetch();

        if (!$result) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Invalid or inactive API key']);
            exit();
        }

        if ($requiredRole !== null && $result['role'] !== $requiredRole) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => ucfirst($requiredRole) . ' privileges required']);
            exit();
        }

        return $result;
    }
}