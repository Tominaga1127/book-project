<?php

require_once __DIR__ . '/database.php';

// 接続済みのPDOで本を登録する。読了ページ数はDBの既定値0を使う。
function createBook(PDO $pdo, string $title, int $totalPages): void
{
    // 入力値はSQLに埋め込まず、プレースホルダーへ渡す。
    $statement = $pdo->prepare('INSERT INTO books (title, total_pages) VALUES (:title, :total_pages)');
    $statement->execute([
        'title' => $title,
        'total_pages' => $totalPages,
    ]);
}

// 接続済みのPDOから、従来どおりID順に本一覧を取得する。
function fetchBooks(PDO $pdo): array
{
    $statement = $pdo->query('SELECT id, title, total_pages, readed_pages
FROM books
ORDER BY id;');

    // 全件を、カラム名をキーとする連想配列で返す。
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}
