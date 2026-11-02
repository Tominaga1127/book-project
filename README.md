# 読書進捗管理アプリ

## 概要
管理したい本のタイトル、総ページ数、現在ページ数などを記録できるアプリ

## 起動方法
1. Bashで以下のコマンドを実行
 ~~~bash
 docker compose up -d
 ~~~
1. http://localhost:8080/へ移動

## テーブルの作成
DBの起動完了後、プロジェクトのディレクトリで以下を一度実行する。
既存の`books`テーブルがある場合は実行不要。

~~~bash
docker compose exec -T db sh -c 'MYSQL_PWD="$MYSQL_PASSWORD" mysql -u "$MYSQL_USER" "$MYSQL_DATABASE"' < database/schema.sql
~~~

`schema.sql`はテーブル定義である。このコマンドでは書籍データは登録されない。

## ここまでの成果
Dockerを起動し、webページに文字列を出力できた。
