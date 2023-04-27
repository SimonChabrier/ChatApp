<?php

namespace App\Message;

final class Chat
{   
    //TODO : à décommenter pour sauvegarder les messages en BDD

    private $message;
    private $channel;
    private $author;

    public function __construct(string $message, int $channel, int $author)
    {
        $this->message = $message;
        $this->channel = $channel;
        $this->author = $author;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    public function getChannelId(): string
    {
         return $this->channel;
    }

    public function getAuthorId(): string
    {
         return $this->author;
    }
}
