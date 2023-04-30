<?php

namespace App\Message;

final class PrivateMessage
{   
    private $message;
    private $conversation;
    private $author;

    public function __construct(string $message, int $conversation, int $author)
    {
        $this->message = $message;
        $this->conversation = $conversation;
        $this->author = $author;
    }
    public function getPrivateMessage(): string
    {
        return $this->message;
    }
    public function getPrivateConversationId(): string
    {
         return $this->conversation;
    }

    public function getAuthorId(): string
    {
         return $this->author;
    }
}
