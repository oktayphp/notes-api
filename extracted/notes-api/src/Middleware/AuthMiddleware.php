<?php
declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class AuthMiddleware implements MiddlewareInterface
{
    private string $secret;

    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    public function process(Request $request, RequestHandlerInterface $handler): Response
    {
        $auth = $request->getHeaderLine('Authorization');
        if (!$auth || !str_starts_with($auth, 'Bearer ')) {
            $res = new \Slim\Psr7\Response();
            $res->getBody()->write(json_encode(['error' => 'Unauthorized']));
            return $res->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
        $token = substr($auth, 7);
        try {
            $decoded = JWT::decode($token, new Key($this->secret, 'HS256'));
            // you could attach user info to request attribute here
            return $handler->handle($request->withAttribute('jwt', $decoded));
        } catch (\Throwable $e) {
            $res = new \Slim\Psr7\Response();
            $res->getBody()->write(json_encode(['error' => 'Invalid token']));
            return $res->withStatus(401)->withHeader('Content-Type', 'application/json');
        }
    }
}