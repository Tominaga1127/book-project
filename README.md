# 読書進捗管理アプリ

## 概要
管理したい本のタイトル、総ページ数、現在ページ数などを記録できるアプリ

## 起動方法
1. Bashで以下のコマンドを実行
   ~~~bash
   docker compose up -d
   ~~~
2. http://localhost:8080/へ移動

## テーブルの作成
compose.yaml側で、コンテナ新規作成時にsqlファイルが自動実行される設定にしている。
テーブルのレイアウトを更新したい場合、以下を実行すること。
~~~bash
docker-compose down --volume
docker-compose up --build
~~~

# テーブルへの接続
以下のコマンドを実行する。
~~~bash
docker compose exec db mysql -u book_user -p book_app
~~~
その後、.envで設定したパスワードを入力し、接続を完了する。

## ここまでの成果
Dockerを起動し、webページに文字列を出力できた。
