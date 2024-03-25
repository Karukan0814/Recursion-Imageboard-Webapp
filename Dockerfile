# ベースイメージを指定
FROM php:8.0-apache

# PHPの拡張機能をインストール
# PHP の拡張機能をインストール
RUN docker-php-ext-install pdo_mysql mysqli

# 現在のディレクトリの内容をコンテナにコピー
COPY . /var/www/html

# Apacheの設定を変更して.htaccessファイルを有効にする
RUN a2enmod rewrite
