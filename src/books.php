<?php

require_once __DIR__ . '/database.php';

// データベースに接続する。
$pdo = connectDatabase();

// ID順に本一覧を取得する。
$statement = $pdo->query('SELECT id, title, total_pages, readed_pages
FROM books
ORDER BY id;');

// 全件を、カラム名をキーとする連想配列で取得する。
$books = $statement->fetchAll(PDO::FETCH_ASSOC);
