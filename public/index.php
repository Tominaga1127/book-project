<?php
// 初回表示では入力を読まず、POST時だけ文字列として取り出して検証する。
$errors = [];
$title = '';
$totalPagesInput = '';
$totalPages = null;

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    if (!isset($_POST['title']) || !is_string($_POST['title'])) {
        $errors[] = '題名を文字列で入力してください。';
    } else {
        // 全角スペースなども含め、題名の前後の空白を取り除く。
        $title = preg_replace('/\A[\s\p{Z}]+|[\s\p{Z}]+\z/u', '', $_POST['title']);
        if ($title === null) {
            $title = '';
            $errors[] = '題名を正しいUTF-8の文字列で入力してください。';
        } elseif ($title === '') {
            $errors[] = '題名を入力してください。空白のみの題名は使えません。';
        }
    }

    if (!isset($_POST['total_pages']) || !is_string($_POST['total_pages'])) {
        $errors[] = '総ページ数を文字列で入力してください。';
    } else {
        $totalPagesInput = $_POST['total_pages'];
        // 小数・負数・0やDBの上限を超える値を、整数へ変換する前に拒否する。
        $validatedPages = filter_var($totalPagesInput, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1, 'max_range' => 4294967295],
        ]);
        if (preg_match('/\A[0-9]+\z/', $totalPagesInput) !== 1 || $validatedPages === false) {
            $errors[] = '総ページ数は1以上4294967295以下の整数で入力してください。';
        } else {
            $totalPages = $validatedPages;
        }
    }
}

// 入口で接続・登録・取得の例外を受け止め、利用者には短い案内だけを返す。
$isRegistration = ($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $errors === [];
try {
    require_once dirname(__DIR__) . '/src/books.php';
    $pdo = connectDatabase();

    // 入力エラーがないPOSTだけを登録し、一覧取得より先に処理する。
    if ($isRegistration) {
        createBook($pdo, $title, $totalPages);

        // GETで一覧を開き直し、再読み込みによるPOSTの再送信を防ぐ。
        header('Location: index.php', true, 303);
        exit;
    }

    // 通常のGETと入力エラー時は、一覧を取得してフォームとともに表示する。
    $books = fetchBooks($pdo);
} catch (PDOException $exception) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=UTF-8');

    // 認証情報を含み得る例外本文やトレースを避け、エラーコードと発生箇所を記録する。
    error_log(sprintf(
        'Book database failure: code=%s file=%s line=%d',
        $exception->getCode(),
        $exception->getFile(),
        $exception->getLine()
    ));

    echo ($isRegistration
        ? '本を登録できませんでした。しばらくしてから再度お試しください。'
        : '本一覧を取得できませんでした。しばらくしてから再度お試しください。') . PHP_EOL;
    exit;
}
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
    <!-- 入力エラーの理由をフォームの上に表示する。 -->
    <?php if ($errors !== []): ?>
        <ul role="alert">
            <?php foreach ($errors as $error): ?>
                <li><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    <!-- 題名と総ページ数を同じページへPOST送信する。読了ページ数はDBの初期値を使う。 -->
    <!-- ブラウザ標準の検証で送信を止めず、PHPで検証したエラーを表示する。 -->
    <form class="book-form" method="post" action="index.php" novalidate>
        <div>
            <label for="title">題名</label>
            <input type="text" id="title" name="title" maxlength="255" value="<?= htmlspecialchars($title, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>
        </div>
        <div>
            <label for="total_pages">総ページ数</label>
            <input type="number" id="total_pages" name="total_pages" min="1" step="1" value="<?= htmlspecialchars($totalPagesInput, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>" required>
        </div>
        <button type="submit">送信</button>
    </form>
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
