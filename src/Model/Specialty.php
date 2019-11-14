<?php


namespace App\Model;

use \DateTime;
use Cocur\Slugify\Slugify;
use JsonSerializable;

class Specialty implements JsonSerializable
{
    private $id;
    private $service_id;
    private $name;
    private $created_at;
    private $service;
    private $medecins = [];
    private $status = true;


    public function getId()
    {
        return $this->id;
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }


    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }


    public function getName()
    {
        return $this->name;
    }


    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }


    public function getCreatedAt(): DateTime
    {
        return $this->created_at;
    }


    public function setCreatedAt(string $created_at): self
    {
        $this->created_at = new DateTime($created_at);
        return $this;
    }

    public function getSlug(): ?string
    {
        $slugify = new Slugify();
        return  $slugify->slugify($this->name);
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


    public function getMedecins()
    {
        return $this->medecins;
    }


    public function setMedecins($medecins)
    {
        $this->medecins = $medecins;

        return $this;
    }

    public function addMedecin(Medecin $medecin)
    {
        $this->medecins[] = $medecin;
        $medecin->setSpecialty($this);
    }
}
