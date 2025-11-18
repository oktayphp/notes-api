<?php
declare(strict_types=1);
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
if (file_exists(__DIR__ . '/../.env')) $dotenv->load();

$dbFile = $_ENV['DB_FILE'] ?? __DIR__ . '/../data/notes.sqlite';
if (strpos($dbFile, '__DIR__')!==false) $dbFile = str_replace('__DIR__', __DIR__, $dbFile);
if (!is_dir(dirname($dbFile))) mkdir(dirname($dbFile), 0777, true);
$pdo = new PDO('sqlite:' . $dbFile);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$pdo->exec("CREATE TABLE IF NOT EXISTS notes (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT NOT NULL,
    created_at TEXT NOT NULL
);");

echo "Migration completed: $dbFile\n";