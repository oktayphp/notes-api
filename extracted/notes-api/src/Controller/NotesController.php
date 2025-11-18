<?php
declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Repository\NoteRepository;
use Slim\Psr7\Response as SlimResponse;

class NotesController
{
    private NoteRepository $repo;

    public function __construct(NoteRepository $repo)
    {
        $this->repo = $repo;
    }

    public function list(Request $req, Response $res): Response
    {
        $data = $this->repo->all();
        $payload = json_encode($data);
        $res->getBody()->write($payload);
        return $res->withHeader('Content-Type', 'application/json');
    }

    public function get(Request $req, Response $res, array $args): Response
    {
        $id = (int)$args['id'];
        $note = $this->repo->find($id);
        if (!$note) {
            $res->getBody()->write(json_encode(['error' => 'Not found']));
            return $res->withStatus(404)->withHeader('Content-Type', 'application/json');
        }
        $res->getBody()->write(json_encode($note));
        return $res->withHeader('Content-Type', 'application/json');
    }

    public function create(Request $req, Response $res): Response
    {
        $body = (array)$req->getParsedBody();
        $title = trim((string)($body['title'] ?? ''));
        $content = trim((string)($body['content'] ?? ''));
        if ($title === '' || $content === '') {
            $res->getBody()->write(json_encode(['error' => 'title and content required']));
            return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $id = $this->repo->create($title, $content);
        $res->getBody()->write(json_encode(['id' => $id]));
        return $res->withStatus(201)->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $req, Response $res, array $args): Response
    {
        $id = (int)$args['id'];
        $body = (array)$req->getParsedBody();
        $title = array_key_exists('title', $body) ? trim((string)$body['title']) : null;
        $content = array_key_exists('content', $body) ? trim((string)$body['content']) : null;
        if ($title === null && $content === null) {
            $res->getBody()->write(json_encode(['error' => 'nothing to update']));
            return $res->withStatus(400)->withHeader('Content-Type', 'application/json');
        }
        $updated = $this->repo->update($id, $title, $content);
        $res->getBody()->write(json_encode(['updated' => $updated]));
        return $res->withHeader('Content-Type', 'application/json');
    }

    public function delete(Request $req, Response $res, array $args): Response
    {
        $id = (int)$args['id'];
        $deleted = $this->repo->delete($id);
        $res->getBody()->write(json_encode(['deleted' => $deleted]));
        return $res->withHeader('Content-Type', 'application/json');
    }
}