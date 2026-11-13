<?php
// プロジェクト直下のsrcから本一覧の取得処理を読み込む。
require_once dirname(__DIR__) . '/src/books.php';
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>本一覧</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main class="container">
    <h1>本一覧</h1>
    <!-- 本が登録されていない場合 -->
    <?php if (empty($books)): ?>
        <p>本が登録されていません</p>
    <?php else: ?>
    <!-- 狭い画面では表の部分だけを横にスクロールできる。 -->
    <div class="table-scroll" role="region" aria-label="本一覧（横スクロール可能）" tabindex="0">
    <table>
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">タイトル</th>
                <th scope="col">総ページ数</th>
                <th scope="col">読了ページ数</th>
            </tr>
        </thead>
        <tbody>
            <?php // 取得した本を1冊ずつ表の行として表示する。 ?>
            <?php foreach ($books as $book): ?>
                <tr>
                    <?php // 特殊文字をエスケープし、UTF-8の文字として表示する。 ?>
                    <td><?= htmlspecialchars((string) $book['id'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $book['title'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $book['total_pages'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                    <td><?= htmlspecialchars((string) $book['readed_pages'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
    </main>
</body>
</html>
