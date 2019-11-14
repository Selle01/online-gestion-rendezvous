<?php

namespace App\Model;

use App\Model\User;
use JsonSerializable;

class Role implements JsonSerializable
{

    private $id;

    private $title;

    private $users;

    private $status;

    public function __construct()
    { }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }


    public function getUsers()
    {
        return $this->users;
    }

    public function getStatus()
    {
        return $this->status;
    }


    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    public function addUser(User $user): self
    {
        if (!in_array($user, $this->users)) {
            $this->users[] = $user;
            $user->setRole($this);
        }
        return $this;
    }
}
