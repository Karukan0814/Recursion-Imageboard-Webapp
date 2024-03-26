# ベースイメージを指定
FROM php:8.0-apache

# PHPの拡張機能をインストール
# PHP の拡張機能をインストール

# ImageMagickとimagickのインストール
RUN apt-get update && \
    apt-get install -y imagemagick && \
    rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y libmagickwand-dev --no-install-recommends && rm -rf /var/lib/apt/lists/*

RUN mkdir -p /usr/src/php/ext/imagick; \
    curl -fsSL https://github.com/Imagick/imagick/archive/06116aa24b76edaf6b1693198f79e6c295eda8a9.tar.gz | tar xvz -C "/usr/src/php/ext/imagick" --strip 1; \
    docker-php-ext-install imagick;


RUN docker-php-ext-install pdo_mysql mysqli imagick

# 現在のディレクトリの内容をコンテナにコピー
COPY . /var/www/html

# Apacheの設定を変更して.htaccessファイルを有効にする
RUN a2enmod rewrite
