<?php

namespace App\Message;

final class Mercure
{

    private $topic;
    private $data;
    private $username;

    public function __construct(string $topic, string $data, string $username)
    {
        $this->topic = $topic;
        $this->data = $data;
        $this->username = $username;
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

    /**
     * get Mercure topic username
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
}
