<?php

namespace App\Model;

use App\Model\User;
use JsonSerializable;

class Secretary extends User implements JsonSerializable
{

    private $secretary_id;

    protected $user_id;

    private $service_id;

    private $service;


    public function getUserId()
    {
        return $this->user_id;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getServiceId()
    {
        return $this->service_id;
    }

    public function setServiceId($service_id)
    {
        $this->service_id = $service_id;
        return $this;
    }

    public function getService()
    {
        return $this->service;
    }

    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }


    public function getSecretaryId()
    {
        return $this->secretary_id;
    }


    public function setSecretaryId($secretary_id)
    {
        $this->secretary_id = $secretary_id;

        return $this;
    }
}
