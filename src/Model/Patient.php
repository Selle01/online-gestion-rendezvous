<?php

namespace App\Model;

use App\Model\User;
use JsonSerializable;

class Patient extends User implements JsonSerializable
{


    protected $patient_id;

    protected $user_id;


    /**
     * Get the value of user_id
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * Set the value of user_id
     *
     * @return  self
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }


    public function getPatientId()
    {
        return $this->patient_id;
    }


    public function setPatientId($patient_id)
    {
        $this->patient_id = $patient_id;

        return $this;
    }
}
