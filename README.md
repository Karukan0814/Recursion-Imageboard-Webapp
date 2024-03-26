# Recursion-Imageboard-Webapp

![service-image](https://github.com/Karukan0814/Recursion-Imageboard-Webapp/blob/main/assets/imgboadDemo.gif)

# 概要

ユーザーが画像やテキストコンテンツを投稿できるイメージボード Web アプリ。このプラットフォームは、ユーザーがメインスレッドを開始し、他のユーザーがそれに返信できる。投稿にユーザーデータが添付されていないため、すべての投稿は匿名。
ユーザーは、画像と共にコンテンツを投稿することで新しいスレッドを作成できる。メインスレッドが作成されると、他のユーザーはテキストや画像を使ってそれに返信。


# 機能

1. ホーム画面
   Home画面から登録されたスレッドの一覧とリプライの最新5件を閲覧できる。

   ![service-image](https://github.com/Karukan0814/Recursion-Imageboard-Webapp/blob/main/assets/threadsList.png)

   また、画像をアップロードすることで新たなスレッドを登録することができる。
   ![service-image](https://github.com/Karukan0814/Recursion-Imageboard-Webapp/blob/main/assets/registerThread.gif)
   

3. スレッド詳細画面
   ホーム画面に表示されたスレッドを押下すると、そのスレッドの詳細画面に遷移する。
   そのスレッドに紐づくすべてのリプライと、新たなリプライの登録が可能

   ![service-image](https://github.com/Karukan0814/Recursion-Imageboard-Webapp/blob/main/assets/registerReply.gif)

4. サムネイル画像
   画像のアップロード時に自動的にImagickによってサムネイル画像が生成される。サムネイル画像をクリックするとオリジナルの画像が表示される。
   ![service-image](https://github.com/Karukan0814/Recursion-Imageboard-Webapp/blob/main/assets/clickThumnail.gif)
   



# 開発環境の構築

開発環境を Docker を使用して立ち上げることが可能。以下、その手順。

1. 当該レポジトリをローカル環境にコピー

2. 環境変数ファイルの準備
   　.env ファイルをルートフォルダ直下に用意し、以下を記述して保存する。

```
DATABASE_NAME=practice_db
DATABASE_USER=任意のユーザー名
DATABASE_USER_PASSWORD=任意のパスワード
DATABASE_HOST=db


```

3. Docker ビルド
   　以下を実行してビルド。なお、以下は Docker がインストール済みであることを前提とする。

```
docker compose build
```

4. Docker 立ち上げ
   　以下を実行してコンテナを立ち上げ。

```
docker compose up -d
```

5. DB Migration 実行
   　以下を実行して初期テーブルの構築。

```
docker-compose exec web php console migrate --init
```

6. 動作確認
   　[http://localhost:8080/](http://localhost:8080/)にアクセスして動作確認

