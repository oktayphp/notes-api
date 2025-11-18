<?php
declare(strict_types=1);

use Psr\Container\ContainerInterface;
use App\Repository\NoteRepository;
use PDO;

return function (Psr\Container\ContainerInterface $c) {
    $container = $c;
    // PDO for SQLite
    $container->set('db', function() {
        $dbFile = __DIR__ . '/../data/notes.sqlite';
        // allow override via env
        $envFile = $_ENV['DB_FILE'] ?? null;
        if ($envFile && strpos($envFile, '__DIR__') !== false) {
            $envFile = str_replace('__DIR__', __DIR__ . '/..', $envFile);
        }
        $dbFile = $envFile ?: $dbFile;
        if (!is_dir(dirname($dbFile))) mkdir(dirname($dbFile), 0777, true);
        $pdo = new PDO('sqlite:' . $dbFile);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // PRAGMA for foreign keys and WAL
        $pdo->exec('PRAGMA foreign_keys = ON; PRAGMA journal_mode = WAL;');
        return $pdo;
    });

    $container->set(NoteRepository::class, function(ContainerInterface $c){
        return new NoteRepository($c->get('db'));
    });

    return $container;
};