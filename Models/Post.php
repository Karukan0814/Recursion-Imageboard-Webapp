<?php

namespace Models;

use Models\Interfaces\Model;
use Models\Traits\GenericModel;

class Post implements Model {
    use GenericModel;

    // php 8のコンストラクタのプロパティプロモーションは、インスタンス変数を自動的に設定します。
    public function __construct(
        private string $post_id,
        private ?string $reply_to_id = null,
        private string $subject,
        private string $text,
        private ?string $created_at = null,
        private ?string $updated_at = null

    ) {}

    public function getPost_id(): ?string {
        return $this->post_id;
    }

    public function setPost_id(string $post_id): void {
        $this->post_id = $post_id;
    }
    public function getReply_to_id(): ?string {
        return $this->reply_to_id;
    }

    public function setReply_to_id(string $reply_to_id): void {
        $this->reply_to_id = $reply_to_id;
    }
    public function getSubject(): string {
        return $this->subject;
    }

    public function setSubject(string $subject): void {
        $this->subject = $subject;
    }

    public function getText(): string {
        return $this->text;
    }

    public function setText(string $text): void {
        $this->text = $text;
    }

    public function getCreated_at(): ?string {
        return $this->created_at;
    }



    public function getUpdated_at(): ?string {
        return $this->updated_at;
    }


}