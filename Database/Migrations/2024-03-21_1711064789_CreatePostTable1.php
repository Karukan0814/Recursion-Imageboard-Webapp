<?php

namespace Database\Migrations;

use Database\SchemaMigration;

class CreatePostTable1 implements SchemaMigration
{
    public function up(): array
    {
        // マイグレーションロジックをここに追加してください
        return ["CREATE TABLE IF NOT EXISTS posts (
            post_id VARCHAR(500) PRIMARY KEY,
            reply_to_id VARCHAR(500) ,
            subject VARCHAR(100) ,
            text VARCHAR(500),
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP  , 
            FOREIGN KEY (reply_to_id) REFERENCES posts(post_id) 
        
        );
        "];
    }

    public function down(): array
    {
        // ロールバックロジックを追加してください
        return [
            "DROP TABLE posts"
        ];
    }
}
