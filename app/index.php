<?php
/**
 * Health check endpoint.
 * Returns a 200 OK with basic application status.
 * Does NOT expose server internals.
 */

http_response_code(200);
header('Content-Type: application/json');

echo json_encode([
    'status' => 'ok',
    'app'    => 'php-app-k8s',
    'version' => getenv('APP_VERSION') ?: '0.2.2',
]);
