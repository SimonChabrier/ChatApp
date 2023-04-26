<?php

namespace App\Message;

final class Mercure
{

    private $topic;
    private $data;

    public function __construct(string $topic, string $data)
    {
        $this->topic = $topic;
        $this->data = $data;
    }
    /**
     * get Mercure topic name
     *
     * @return string
     */
    public function getTopic(): string
    {
        return $this->topic;
    }

    /**
     * get Mercure topic data
     *
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }
}
