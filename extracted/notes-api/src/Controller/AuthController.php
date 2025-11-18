<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Firebase\JWT\JWT;

class AuthController
{
    // For demo: a single seeded user; in real app use users table + hashed passwords
    private array $user = [
        'id' => 1,
        'email' => 'admin@example.com',
        'password' => 'secret' // seed_user.php will create this
    ];

    public function login(Request $req, Response $res): Response
    {
        $body = (array)$req->getParsedBody();
        $email = $body['email'] ?? '';
        $password = $body['password'] ?? '';
        // simple check
        if ($email !== $this->user['email'] || $password !== $this->user['password']) {
            $res->getBody()->write(json_encode(['error' => 'invalid credentials']));
            return $res->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        $now = time();
        $payload = [
            'iat' => $now,
            'exp' => $now + 3600,
            'sub' => $this->user['id']
        ];
        $token = JWT::encode($payload, $_ENV['SECRET_KEY'], 'HS256');
        $res->getBody()->write(json_encode(['token' => $token]));
        return $res->withHeader('Content-Type', 'application/json');
    }
}