# 読書進捗管理アプリ

## 概要
管理したい本のタイトル、総ページ数、現在ページ数などを記録できるアプリ

## webページについて

### 起動方法
1. Bashで以下のコマンドを実行
   ~~~bash
   docker compose up -d
   ~~~
2. "http://localhost:8080/" へ移動

## テーブルについて

### テーブルの作成
DB保存領域が空の初回起動時に、database直下のSQLファイルが自動実行される。
既存DBがある場合、コンテナを作り直してもSQLは再実行されない。
テーブルやデータの変更は、必要なSQLを確認して既存DBへ実行する。
通常の変更では保存用ボリュームを削除しない。

### テーブルへの接続
以下のコマンドを実行する。
~~~bash
docker compose exec db mysql -u book_user -p book_app
~~~
その後、.envで設定したパスワードを入力し、接続を完了する。

### テーブルの文字コード設定について
SQLファイルはUTF-8で保存する。
docker/mysql/charset.cnfで、コンテナ内のMySQLクライアントの接続と
サーバーの既定文字コードをutf8mb4に設定している。
初期化SQLにもSET NAMES utf8mb4を指定し、文字の解釈を明示している。

設定ファイルの追加・マウント変更を反映するには、以下を実行する。
~~~bash
docker compose up -d db
~~~
既にマウントしている設定ファイルの内容変更は、DBを再起動して反映する。
既に開いているMySQLの接続は終了し、接続し直す。

接続後、以下の3項目がutf8mb4になることを確認する。
~~~sql
SELECT @@character_set_client,
       @@character_set_connection,
       @@character_set_results;
~~~
この設定は保存済みの文字化けを修復しない。既存の題名は別途、正しい接続で修正する。
将来PHPから接続する際も、PDOの接続設定でcharset=utf8mb4を明示する。

## ここまでの成果

- DockerとMySQLの設定を完了し、web画面をPHPで作成できた。
- MySQL接続後、データを変更・保存することができた。
- MySQLの文字化けを修正することができた。DBの停止・再開後も文字化けが起こらないことを確認済み。