<?php
// api.php - simple REST CRUD for notes using SQLite
$db = new PDO('sqlite:' . __DIR__ . '/notes.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
header('Content-Type: application/json; charset=utf-8');

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int)$_GET['id'] : null;

if ($method === 'GET') {
    if ($id) {
        $stmt = $db->prepare('SELECT * FROM notes WHERE id = ?');
        $stmt->execute([$id]);
        $note = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($note ?: null);
    } else {
        $stmt = $db->query('SELECT * FROM notes ORDER BY id DESC');
        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
    exit;
}

parse_str(file_get_contents('php://input'), $input);
if ($method === 'POST') {
    $title = trim($_POST['title'] ?? $input['title'] ?? '');
    $content = trim($_POST['content'] ?? $input['content'] ?? '');
    if ($title === '' || $content === '') { http_response_code(400); echo json_encode(['error'=>'title and content required']); exit; }
    $stmt = $db->prepare('INSERT INTO notes (title, content, created_at) VALUES (?, ?, ?)');
    $stmt->execute([$title, $content, date('c')]);
    echo json_encode(['id' => $db->lastInsertId()]);
    exit;
}

if ($method === 'PUT') {
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }
    $title = $input['title'] ?? null;
    $content = $input['content'] ?? null;
    $sets = [];
    $params = [];
    if ($title !== null) { $sets[] = 'title = ?'; $params[] = $title; }
    if ($content !== null) { $sets[] = 'content = ?'; $params[] = $content; }
    if (!$sets) { http_response_code(400); echo json_encode(['error'=>'nothing to update']); exit; }
    $params[] = $id;
    $stmt = $db->prepare('UPDATE notes SET '.implode(',',$sets).' WHERE id = ?');
    $stmt->execute($params);
    echo json_encode(['updated' => $stmt->rowCount()]);
    exit;
}

if ($method === 'DELETE') {
    if (!$id) { http_response_code(400); echo json_encode(['error'=>'id required']); exit; }
    $stmt = $db->prepare('DELETE FROM notes WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['deleted' => $stmt->rowCount()]);
    exit;
}

http_response_code(405);
echo json_encode(['error' => 'Method not allowed']);