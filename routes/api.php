<?php

use App\Controllers\GupshupMonitorController;
use App\Database\Connection;
use App\Repositories\GupshupMonitorRepository;
use App\Services\GupshupMonitorService;

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = rtrim(parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?? '/', '/') ?: '/';

if ($uri === '/' || $uri === '/index.php') {
    require __DIR__ . '/../src/Views/index.php';
    exit;
}

if ($method === 'GET' && $uri === '/health') {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(200);

    echo json_encode([
        'success' => true,
        'message' => 'API is running'
    ]);

    exit;
}

if ($method === 'POST' && $uri === '/v1/gupshup/event') {
    $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    $apiToken = $_ENV['API_TOKEN'] ?? '';

    if (!preg_match('/^Bearer\s+(.+)$/i', $authorization, $matches)) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized'
        ]);

        exit;
    }

    $receivedToken = $matches[1];

    if (
        $apiToken === '' ||
        !hash_equals($apiToken, $receivedToken)
    ) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized'
        ]);

        exit;
    }

    try {
        $connection = new Connection();

        $repository = new GupshupMonitorRepository(
            $connection->getConnection()
        );

        $service = new GupshupMonitorService($repository);

        $controller = new GupshupMonitorController($service);

        $controller->store();
    } catch (\Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Internal server error'
        ]);
    }

    exit;
}

// Qualquer rota que não exista redireciona para a raiz
header('Location: /', true, 302);
exit;