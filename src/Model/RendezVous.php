<?php


namespace App\Model;

use \DateTime;
use JsonSerializable;

class Rendezvous implements JsonSerializable
{
    protected $rv_id;
    protected $medecin_id;
    protected $secretary_id;
    protected $patient_id;

    protected $heure_rv;
    protected $date_rv;
    // protected $intervalle;

    protected $medecin;
    protected $secretary;
    protected $patient;

    protected $status = true;

    /**
     * Get the value of id
     */
    public function getRVId()
    {
        return $this->rv_id;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setRVId($rv_id)
    {
        $this->rv_id = $rv_id;

        return $this;
    }


    public function getMedecinId()
    {
        return $this->medecin_id;
    }


    public function setMedecinId(int $medecin_id)
    {
        $this->medecin_id = $medecin_id;

        return $this;
    }





    public function getPatientId()
    {
        return $this->patient_id;
    }


    public function setPatientId(int $patient_id)
    {
        $this->patient_id = $patient_id;

        return $this;
    }


    public function getHeureRV()
    {
        return $this->heure_rv;
    }


    public function setHeureRV($heure_rv)
    {
        $this->heure_rv = $heure_rv;

        return $this;
    }

    public function getDateRV(): DateTime
    {
        return $this->date_rv;
    }

    public function setDateRV(string $date_rv): self
    {
        $this->date_rv = new DateTime($date_rv);

        return $this;
    }


    /**
     * Get the value of medecin
     */
    public function getMedecin()
    {
        return $this->medecin;
    }

    /**
     * Set the value of medecin
     *
     * @return  self
     */
    public function setMedecin($medecin)
    {
        $this->medecin = $medecin;

        return $this;
    }


    /**
     * Get the value of patient
     */
    public function getPatient()
    {
        return $this->patient;
    }

    /**
     * Set the value of patient
     *
     * @return  self
     */
    public function setPatient($patient)
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the value of status
     *
     * @return  self
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }


    // public function getIntervalle()
    // {
    //     return $this->intervalle;
    // }


    // public function setIntervalle($intervalle)
    // {
    //     $this->intervalle = $intervalle;

    //     return $this;
    // }

    public function getSecretaryId()
    {
        return $this->secretary_id;
    }


    public function setSecretaryId($secretary_id)
    {
        $this->secretary_id = $secretary_id;

        return $this;
    }

    /**
     * Get the value of secretary
     */
    public function getSecretary()
    {
        return $this->secretary;
    }

    /**
     * Set the value of secretary
     *
     * @return  self
     */
    public function setSecretary($secretary)
    {
        $this->secretary = $secretary;

        return $this;
    }
}
