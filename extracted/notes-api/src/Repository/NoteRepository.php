<?php
declare(strict_types=1);

namespace App\Repository;

use PDO;
use RuntimeException;

class NoteRepository
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT id, title, content, created_at FROM notes ORDER BY id DESC');
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, title, content, created_at FROM notes WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function create(string $title, string $content): int
    {
        $stmt = $this->db->prepare('INSERT INTO notes (title, content, created_at) VALUES (?, ?, ?)');
        $ok = $stmt->execute([$title, $content, date('c')]);
        if (!$ok) throw new RuntimeException('Insert failed');
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, ?string $title, ?string $content): int
    {
        $sets = [];
        $params = [];
        if ($title !== null) { $sets[] = 'title = ?'; $params[] = $title; }
        if ($content !== null) { $sets[] = 'content = ?'; $params[] = $content; }
        if (!$sets) return 0;
        $params[] = $id;
        $sql = 'UPDATE notes SET ' . implode(', ', $sets) . ' WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $stmt = $this->db->prepare('DELETE FROM notes WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }
}