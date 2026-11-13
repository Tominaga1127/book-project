<?php

// データベースに接続し、成功時にPDOを返す。接続失敗時の例外は呼び出し元へ伝わる。
function connectDatabase(): PDO
{
    // 環境変数から接続情報を取得する。
    $host = getenv('DB_HOST');
    $name = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');

    // 接続先と文字コードを指定する。
    $dsn = "mysql:host={$host};dbname={$name};charset=utf8mb4";

    return new PDO($dsn, $user, $password, [
        // SQL実行時のエラーも例外として通知する。
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
}
